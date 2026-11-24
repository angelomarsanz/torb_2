<?php

/**
 * Resumen: Servicio de documentos de identidad del KYC (verificación manual). Responsabilidades:
 *  - Validar de forma estricta cada archivo en el servidor: extensión permitida, tamaño, tipo MIME REAL
 *    (finfo, no el que declara el navegador), coherencia extensión/MIME (MIME falso), imágenes legibles con
 *    límites de dimensiones y memoria, y PDF sin contenido activo.
 *  - Neutralizar las imágenes: se recodifican con GD (si está disponible) para eliminar metadatos EXIF,
 *    comentarios y cargas ocultas, respetando la orientación.
 *  - Guardar los archivos CIFRADOS (AES-256 con APP_KEY) en el disco privado "kyc_privado", con nombre UUID
 *    sin datos personales, sin URL pública y con un .htaccess de denegación en la carpeta.
 *  - Registrar los metadatos en documentos_kyc conservando el historial: un documento reemplazado pasa a
 *    "reemplazado" y no se borra.
 *  - Leer un documento verificando su integridad (sha256) y registrar cada acceso en la auditoría.
 *  - Eliminar archivos (retención) dejando la fila de auditoría.
 * La autorización (quién puede leer) NO se decide aquí: la aplica KycPolicy en el controlador.
 * Las escrituras en base de datos usan la ventana GuardiaEscrituraKyc.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Reda\RedaAlojamiento\Models\Kyc\DocumentoKyc;
use Reda\RedaAlojamiento\Models\Kyc\EventoKyc;
use Reda\RedaAlojamiento\Models\Kyc\SolicitudKyc;
use Throwable;

class KycDocumentoService
{
    /**
     * Extensión declarada -> tipo MIME real exigido.
     */
    private const MIME_POR_EXTENSION = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'pdf'  => 'application/pdf',
    ];

    /**
     * Nombres de PDF que ejecutan código, abren recursos externos o impiden inspeccionar el archivo.
     */
    private const PATRON_PDF_PELIGROSO = '#/(JavaScript|JS|Launch|EmbeddedFiles?|OpenAction|AA|RichMedia|XFA|SubmitForm|ImportData|GoToR|GoToE|URI|Encrypt|Movie|Sound|Rendition|Screen)(?![A-Za-z0-9])#';

    /**
     * Nombres de PDF escritos con códigos #xx (técnica de ofuscación para evadir filtros). Se decodifican y solo se
     * rechazan si el resultado es un nombre peligroso (muchos PDF legítimos usan #20 en nombres de fuentes).
     */
    private const PATRON_PDF_OFUSCADO = '#/[^\s/<>\[\]()%{}]*\#[0-9A-Fa-f]{2}[^\s/<>\[\]()%{}]*#';

    /**
     * Ruta relativa válida de un archivo almacenado (se comprueba antes de leer o borrar).
     */
    private const PATRON_RUTA = '#^\d{4}/\d{2}/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.enc$#';

    private static bool $directorioProtegido = false;

    /**
     * Disco privado de documentos.
     */
    public function disco(): Filesystem
    {
        return Storage::disk((string) config('kyc.documentos.disco', 'kyc_privado'));
    }

    // ------------------------------------------------------------------
    // VALIDACIÓN
    // ------------------------------------------------------------------

    /**
     * Valida y prepara todos los archivos recibidos (tipo => UploadedFile).
     *
     * @param  array<string,mixed> $archivos
     * @return array<string,array<string,mixed>> Archivos listos para guardar, por tipo.
     *
     * @throws DocumentoKycInvalidoException
     */
    public function prepararArchivos(array $archivos): array
    {
        $maximo = (int) config('kyc.documentos.maximo_archivos', 3);

        if (count($archivos) > $maximo) {
            throw new DocumentoKycInvalidoException('documentos', __('Se enviaron más archivos de los permitidos.'));
        }

        $preparados = [];

        foreach ($archivos as $tipo => $archivo) {
            if (!in_array($tipo, DocumentoKyc::tipos(), true)) {
                throw new DocumentoKycInvalidoException('documentos', __('Tipo de documento no permitido.'));
            }

            $preparados[$tipo] = $this->prepararArchivo($archivo, $tipo);
        }

        return $preparados;
    }

    /**
     * Valida un archivo y devuelve su contenido ya neutralizado junto con sus metadatos.
     *
     * @return array{tipo:string,campo:string,contenido:string,mime:string,extension:string,nombre_original:string,sha256:string,tamano:int}
     *
     * @throws DocumentoKycInvalidoException
     */
    public function prepararArchivo(mixed $archivo, string $tipo): array
    {
        $campo = 'documento_' . $tipo;

        if (!$archivo instanceof UploadedFile) {
            throw new DocumentoKycInvalidoException($campo, __('El archivo no es válido.'));
        }

        if (!$archivo->isValid()) {
            throw new DocumentoKycInvalidoException($campo, __('No se pudo recibir el archivo. Verifica su tamaño e inténtalo de nuevo.'));
        }

        $extension = strtolower((string) pathinfo((string) $archivo->getClientOriginalName(), PATHINFO_EXTENSION));
        $permitidas = (array) config('kyc.documentos.extensiones_permitidas', []);

        if (!in_array($extension, $permitidas, true) || !isset(self::MIME_POR_EXTENSION[$extension])) {
            throw new DocumentoKycInvalidoException($campo, __('Formato no permitido. Usa JPG, PNG o PDF.'));
        }

        $maximoBytes = (int) config('kyc.documentos.tamano_maximo_kb', 5120) * 1024;
        $tamano = (int) $archivo->getSize();

        if ($tamano <= 0) {
            throw new DocumentoKycInvalidoException($campo, __('El archivo está vacío.'));
        }

        if ($tamano > $maximoBytes) {
            throw new DocumentoKycInvalidoException($campo, __('El archivo supera el tamaño máximo permitido.'));
        }

        $ruta = (string) $archivo->getRealPath();
        $contenido = $ruta !== '' ? @file_get_contents($ruta, false, null, 0, $maximoBytes + 1) : false;

        if ($contenido === false || $contenido === '' || strlen($contenido) > $maximoBytes) {
            throw new DocumentoKycInvalidoException($campo, __('No se pudo leer el archivo.'));
        }

        $mime = $this->detectarMime($contenido);

        // El MIME real debe estar permitido y coincidir con la extensión (un .png que en realidad es PHP o PDF se rechaza).
        if (!in_array($mime, (array) config('kyc.documentos.mimes_permitidos', []), true)
            || $mime !== self::MIME_POR_EXTENSION[$extension]) {
            throw new DocumentoKycInvalidoException($campo, __('El contenido del archivo no corresponde a su formato.'));
        }

        if ($mime === 'application/pdf' && in_array($tipo, (array) config('kyc.documentos.tipos_solo_imagen', []), true)) {
            throw new DocumentoKycInvalidoException($campo, __('Este documento debe ser una imagen JPG o PNG.'));
        }

        $contenido = $mime === 'application/pdf'
            ? $this->validarPdf($contenido, $campo)
            : $this->procesarImagen($contenido, $mime, $campo);

        if (strlen($contenido) > $maximoBytes) {
            throw new DocumentoKycInvalidoException($campo, __('El archivo supera el tamaño máximo permitido.'));
        }

        return [
            'tipo'            => $tipo,
            'campo'           => $campo,
            'contenido'       => $contenido,
            'mime'            => $mime,
            'extension'       => $mime === 'application/pdf' ? 'pdf' : ($mime === 'image/png' ? 'png' : 'jpg'),
            'nombre_original' => $this->sanearNombre((string) $archivo->getClientOriginalName()),
            'sha256'          => hash('sha256', $contenido),
            'tamano'          => strlen($contenido),
        ];
    }

    /**
     * Detecta el tipo MIME real a partir del contenido.
     */
    private function detectarMime(string $contenido): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = $finfo ? finfo_buffer($finfo, $contenido) : false;

        return is_string($mime) ? $mime : 'application/octet-stream';
    }

    /**
     * Valida una imagen y, si GD está disponible, la recodifica sin metadatos ni cargas ocultas.
     *
     * @throws DocumentoKycInvalidoException
     */
    private function procesarImagen(string $contenido, string $mime, string $campo): string
    {
        // Poliglotas: una imagen válida con código del servidor o scripts incrustados.
        if (preg_match('/<\?php|<\?=|<script/i', $contenido) === 1) {
            throw new DocumentoKycInvalidoException($campo, __('El archivo contiene contenido no permitido.'));
        }

        $info = @getimagesizefromstring($contenido);

        if (!is_array($info) || !isset($info[0], $info[1], $info[2])) {
            throw new DocumentoKycInvalidoException($campo, __('La imagen no es válida o está dañada.'));
        }

        [$ancho, $alto, $tipoImagen] = $info;
        $esperado = $mime === 'image/png' ? IMAGETYPE_PNG : IMAGETYPE_JPEG;

        if ($tipoImagen !== $esperado) {
            throw new DocumentoKycInvalidoException($campo, __('El contenido del archivo no corresponde a su formato.'));
        }

        $minimo = (int) config('kyc.documentos.lado_minimo_px', 300);
        $maximo = (int) config('kyc.documentos.lado_maximo_px', 12000);

        if ($ancho < $minimo || $alto < $minimo) {
            throw new DocumentoKycInvalidoException($campo, __('La imagen es demasiado pequeña para ser legible.'));
        }

        if ($ancho > $maximo || $alto > $maximo
            || ($ancho * $alto) > ((int) config('kyc.documentos.megapixeles_maximo', 40)) * 1000000) {
            throw new DocumentoKycInvalidoException($campo, __('La imagen es demasiado grande. Reduce su tamaño e inténtalo de nuevo.'));
        }

        if (!function_exists('imagecreatefromstring')) {
            return $contenido;
        }

        if (($ancho * $alto * 6) > $this->memoriaDisponible()) {
            throw new DocumentoKycInvalidoException($campo, __('La imagen es demasiado grande. Reduce su tamaño e inténtalo de nuevo.'));
        }

        $imagen = @imagecreatefromstring($contenido);

        if ($imagen === false) {
            throw new DocumentoKycInvalidoException($campo, __('La imagen no es válida o está dañada.'));
        }

        try {
            if ($mime === 'image/jpeg') {
                $imagen = $this->corregirOrientacion($imagen, $contenido);
            }

            ob_start();
            $correcto = $mime === 'image/png'
                ? imagepng($imagen, null, 6)
                : imagejpeg($imagen, null, (int) config('kyc.documentos.calidad_jpeg', 90));
            $recodificada = ob_get_clean();
        } finally {
            imagedestroy($imagen);
        }

        if (!$correcto || !is_string($recodificada) || $recodificada === '') {
            throw new DocumentoKycInvalidoException($campo, __('No se pudo procesar la imagen.'));
        }

        return $recodificada;
    }

    /**
     * Aplica la orientación EXIF antes de eliminar los metadatos para que la foto no quede girada.
     *
     * @param \GdImage $imagen
     * @return \GdImage
     */
    private function corregirOrientacion($imagen, string $contenido)
    {
        if (!function_exists('exif_read_data')) {
            return $imagen;
        }

        $exif = @exif_read_data('data://image/jpeg;base64,' . base64_encode($contenido));
        $orientacion = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;
        $grados = [3 => 180, 6 => -90, 8 => 90][$orientacion] ?? 0;

        if ($grados === 0) {
            return $imagen;
        }

        $rotada = imagerotate($imagen, $grados, 0);

        if ($rotada === false) {
            return $imagen;
        }

        imagedestroy($imagen);

        return $rotada;
    }

    /**
     * Memoria libre estimada para procesar una imagen (bytes).
     */
    private function memoriaDisponible(): int
    {
        $limite = trim((string) ini_get('memory_limit'));

        if ($limite === '-1') {
            return PHP_INT_MAX;
        }

        $bytes = (int) $limite;
        $unidad = strtolower(substr($limite, -1));
        $bytes *= match ($unidad) { 'g' => 1073741824, 'm' => 1048576, 'k' => 1024, default => 1 };

        return max(0, $bytes - memory_get_usage(true));
    }

    /**
     * Valida un PDF: estructura mínima y ausencia de contenido activo. Se inspeccionan la sintaxis fuera de los
     * flujos binarios y los flujos de objetos comprimidos (ObjStm), donde puede esconderse ese contenido.
     * Se omiten los flujos de datos (imágenes, páginas) para no producir falsos positivos con datos binarios.
     *
     * @throws DocumentoKycInvalidoException
     */
    private function validarPdf(string $contenido, string $campo): string
    {
        $mensaje = __('El PDF contiene elementos no permitidos. Envía una imagen o un PDF sin contenido activo.');

        if (strpos(substr($contenido, 0, 1024), '%PDF-') === false || strpos(substr($contenido, -2048), '%%EOF') === false) {
            throw new DocumentoKycInvalidoException($campo, __('El PDF no es válido o está dañado.'));
        }

        $sintaxis = '';
        $posicion = 0;
        $flujos   = 0;
        $totalDescomprimido = 0;
        $longitud = strlen($contenido);

        while (($inicio = strpos($contenido, 'stream', $posicion)) !== false) {
            // Se descarta la palabra "endstream" y las apariciones sin salto de línea posterior.
            if ($inicio >= 3 && substr($contenido, $inicio - 3, 3) === 'end') {
                $sintaxis .= substr($contenido, $posicion, $inicio + 6 - $posicion);
                $posicion = $inicio + 6;
                continue;
            }

            $comienzoDatos = $inicio + 6;
            if (substr($contenido, $comienzoDatos, 2) === "\r\n") {
                $comienzoDatos += 2;
            } elseif (isset($contenido[$comienzoDatos]) && ($contenido[$comienzoDatos] === "\n" || $contenido[$comienzoDatos] === "\r")) {
                $comienzoDatos += 1;
            }

            $fin = strpos($contenido, 'endstream', $comienzoDatos);

            if ($fin === false || ++$flujos > 5000) {
                throw new DocumentoKycInvalidoException($campo, __('El PDF no es válido o está dañado.'));
            }

            $diccionario = substr($contenido, max($posicion, $inicio - 2000), min(2000, $inicio - $posicion));
            $sintaxis   .= substr($contenido, $posicion, $comienzoDatos - $posicion);

            if (str_contains($diccionario, '/ObjStm')) {
                if (!str_contains($diccionario, '/FlateDecode') || preg_match('#/(LZWDecode|ASCIIHexDecode|ASCII85Decode|RunLengthDecode|Crypt)#', $diccionario)) {
                    throw new DocumentoKycInvalidoException($campo, $mensaje);
                }

                $descomprimido = @zlib_decode(substr($contenido, $comienzoDatos, $fin - $comienzoDatos), 20 * 1048576);

                if (!is_string($descomprimido)) {
                    throw new DocumentoKycInvalidoException($campo, $mensaje);
                }

                $totalDescomprimido += strlen($descomprimido);

                if ($totalDescomprimido > 20 * 1048576) {
                    throw new DocumentoKycInvalidoException($campo, $mensaje);
                }

                $sintaxis .= "\n" . $descomprimido . "\n";
            }

            $posicion = $fin;
        }

        $sintaxis .= $posicion < $longitud ? substr($contenido, $posicion) : '';

        if (preg_match(self::PATRON_PDF_PELIGROSO, $sintaxis) === 1 || $this->tieneNombreOfuscadoPeligroso($sintaxis)) {
            throw new DocumentoKycInvalidoException($campo, $mensaje);
        }

        return $contenido;
    }

    /**
     * Indica si algún nombre PDF escrito con códigos #xx equivale, ya decodificado, a un nombre peligroso.
     */
    private function tieneNombreOfuscadoPeligroso(string $sintaxis): bool
    {
        if (preg_match_all(self::PATRON_PDF_OFUSCADO, $sintaxis, $coincidencias) === 0) {
            return false;
        }

        foreach (array_slice($coincidencias[0], 0, 5000) as $nombre) {
            $decodificado = preg_replace_callback('/#([0-9A-Fa-f]{2})/', static fn ($m) => chr(hexdec($m[1])), $nombre);

            if (preg_match(self::PATRON_PDF_PELIGROSO, (string) $decodificado) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Nombre del archivo original reducido a caracteres inofensivos (solo informativo; nunca se usa como ruta).
     */
    private function sanearNombre(string $nombre): string
    {
        $nombre = basename(str_replace('\\', '/', $nombre));
        $nombre = preg_replace('/[^\p{L}\p{N}\._\- ]/u', '_', $nombre) ?? '';

        return mb_substr(trim($nombre), 0, 120);
    }

    // ------------------------------------------------------------------
    // ALMACENAMIENTO
    // ------------------------------------------------------------------

    /**
     * Guarda el contenido cifrado en el disco privado y devuelve su ruta relativa (aleatoria, sin datos personales).
     *
     * @param  array<string,mixed> $preparado Resultado de prepararArchivo().
     *
     * @throws \RuntimeException Si no se pudo escribir el archivo.
     */
    public function guardarCifrado(array $preparado): string
    {
        $this->protegerDirectorio();

        $ruta = date('Y/m') . '/' . Str::uuid()->toString() . '.enc';
        $dato = config('kyc.documentos.cifrar', true) ? Crypt::encryptString($preparado['contenido']) : $preparado['contenido'];

        if (!$this->disco()->put($ruta, $dato)) {
            throw new \RuntimeException('No se pudo escribir el documento KYC.');
        }

        return $ruta;
    }

    /**
     * Elimina un archivo físico (se ignora si ya no existe). No toca la base de datos.
     */
    public function eliminarArchivoFisico(?string $ruta): void
    {
        if ($ruta && preg_match(self::PATRON_RUTA, $ruta) === 1) {
            try {
                $this->disco()->delete($ruta);
            } catch (Throwable $e) {
                Log::warning('KYC: no se pudo eliminar un archivo físico (' . get_class($e) . ').');
            }
        }
    }

    /**
     * Crea el .htaccess de denegación en la carpeta privada (defensa adicional si quedara dentro de la raíz web).
     */
    private function protegerDirectorio(): void
    {
        if (self::$directorioProtegido) {
            return;
        }

        $disco = $this->disco();

        if (!$disco->exists('.htaccess')) {
            $disco->put('.htaccess', "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
        }

        self::$directorioProtegido = true;
    }

    // ------------------------------------------------------------------
    // BASE DE DATOS (dentro de la ventana de escritura KYC)
    // ------------------------------------------------------------------

    /**
     * Registra un documento en la solicitud. El documento vigente del mismo tipo pasa a "reemplazado"
     * (se conserva el historial). Debe llamarse dentro de una transacción de KycService.
     *
     * @param array<string,mixed> $preparado
     */
    public function registrarDocumento(SolicitudKyc $solicitud, array $preparado, string $ruta): DocumentoKyc
    {
        $anteriores = DocumentoKyc::where('solicitud_id', $solicitud->id)
            ->where('tipo', $preparado['tipo'])
            ->where('estado', DocumentoKyc::ESTADO_VIGENTE)
            ->lockForUpdate()
            ->get();

        $nuevo = DocumentoKyc::create([
            'solicitud_id'    => $solicitud->id,
            'user_id'         => $solicitud->user_id,
            'tipo'            => $preparado['tipo'],
            'disco'           => (string) config('kyc.documentos.disco', 'kyc_privado'),
            'ruta'            => $ruta,
            'nombre_original' => $preparado['nombre_original'],
            'mime'            => $preparado['mime'],
            'tamano_bytes'    => $preparado['tamano'],
            'sha256'          => $preparado['sha256'],
            'cifrado'         => (bool) config('kyc.documentos.cifrar', true),
            'estado'          => DocumentoKyc::ESTADO_VIGENTE,
        ]);

        foreach ($anteriores as $anterior) {
            $anterior->estado = DocumentoKyc::ESTADO_REEMPLAZADO;
            $anterior->reemplazado_por_id = $nuevo->id;
            $anterior->save();
        }

        return $nuevo;
    }

    /**
     * Lee y descifra un documento comprobando su integridad. No comprueba permisos (lo hace KycPolicy).
     *
     * @return array{contenido:string,mime:string,extension:string,tipo:string}
     *
     * @throws DocumentoKycInvalidoException Si el documento fue eliminado, no existe o está alterado.
     */
    public function leer(DocumentoKyc $documento): array
    {
        $noDisponible = new DocumentoKycInvalidoException('documento', __('El documento no está disponible.'));

        if ($documento->estado === DocumentoKyc::ESTADO_ELIMINADO || preg_match(self::PATRON_RUTA, (string) $documento->ruta) !== 1) {
            throw $noDisponible;
        }

        try {
            $disco = Storage::disk((string) $documento->disco);

            if (!$disco->exists($documento->ruta)) {
                throw $noDisponible;
            }

            $almacenado = $disco->get($documento->ruta);
            $contenido  = $documento->cifrado ? Crypt::decryptString($almacenado) : $almacenado;
        } catch (DecryptException $e) {
            Log::error('KYC: el documento ' . $documento->id . ' no se pudo descifrar (alterado o clave distinta).');
            throw $noDisponible;
        } catch (DocumentoKycInvalidoException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('KYC: error leyendo el documento ' . $documento->id . ' (' . get_class($e) . ').');
            throw $noDisponible;
        }

        if (!is_string($contenido) || !hash_equals((string) $documento->sha256, hash('sha256', $contenido))) {
            Log::error('KYC: la integridad del documento ' . $documento->id . ' no coincide con su huella.');
            throw $noDisponible;
        }

        $mime = (string) $documento->mime;

        return [
            'contenido' => $contenido,
            'mime'      => $mime,
            'extension' => $mime === 'application/pdf' ? 'pdf' : ($mime === 'image/png' ? 'png' : 'jpg'),
            'tipo'      => (string) $documento->tipo,
        ];
    }

    /**
     * Registra en la auditoría que alguien consultó un documento.
     */
    public function registrarAcceso(DocumentoKyc $documento, string $origen, ?int $adminId = null, ?string $ip = null): void
    {
        GuardiaEscrituraKyc::ejecutar(function () use ($documento, $origen, $adminId, $ip) {
            EventoKyc::create([
                'user_id'      => $documento->user_id,
                'solicitud_id' => $documento->solicitud_id,
                'origen'       => $origen,
                'tipo'         => 'documento_consultado',
                'resultado'    => EventoKyc::RESULTADO_PROCESADO,
                'detalle'      => ['documento_id' => $documento->id, 'tipo' => $documento->tipo],
                'admin_id'     => $adminId,
                'ip'           => $ip,
            ]);
        });
    }

    /**
     * Fija la fecha prevista de eliminación de los documentos de una solicitud (política de retención).
     */
    public function programarEliminacion(SolicitudKyc $solicitud, ?\DateTimeInterface $fecha): void
    {
        GuardiaEscrituraKyc::ejecutar(function () use ($solicitud, $fecha) {
            $solicitud->eliminar_documentos_en = $fecha;
            $solicitud->save();
        });
    }

    /**
     * Elimina los archivos físicos de una solicitud y los marca como eliminados. Conserva las filas y la
     * auditoría. No modifica el estado de verificación.
     *
     * @return int Cantidad de documentos eliminados.
     */
    public function eliminarDocumentosDeSolicitud(SolicitudKyc $solicitud, string $motivo): int
    {
        return GuardiaEscrituraKyc::ejecutar(function () use ($solicitud, $motivo) {
            $eliminados = 0;

            foreach (DocumentoKyc::where('solicitud_id', $solicitud->id)->where('estado', '!=', DocumentoKyc::ESTADO_ELIMINADO)->get() as $documento) {
                $this->eliminarArchivoFisico($documento->ruta);
                $documento->estado       = DocumentoKyc::ESTADO_ELIMINADO;
                $documento->eliminado_en = now();
                $documento->save();
                $eliminados++;
            }

            $solicitud->documentos_eliminados_en = now();
            $solicitud->save();

            EventoKyc::create([
                'user_id'      => $solicitud->user_id,
                'solicitud_id' => $solicitud->id,
                'origen'       => EventoKyc::ORIGEN_SISTEMA,
                'tipo'         => 'documentos_eliminados',
                'resultado'    => EventoKyc::RESULTADO_PROCESADO,
                'detalle'      => ['motivo' => $motivo, 'cantidad' => $eliminados],
            ]);

            return $eliminados;
        });
    }
}
