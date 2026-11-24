<?php

/**
 * Resumen: Validador de los datos personales y de la presencia de documentos de la verificación manual.
 * Normaliza los textos (recorta y colapsa espacios, países en mayúsculas) y valida: nombres y apellidos tal
 * como figuran en el documento, tipo y número de documento, países ISO 3166-1 (Venezuela es el valor
 * predeterminado de los formularios pero se admite cualquier país), fecha de nacimiento con edad mínima,
 * ciudad, dirección, correo de contacto y la aceptación del tratamiento de datos. No valida el contenido
 * de los archivos: eso lo hace KycDocumentoService.
 * Devuelve siempre un arreglo ['ok', 'errores', 'datos'] con los mensajes ya traducidos al español.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use Illuminate\Support\Facades\Validator;
use Reda\RedaAlojamiento\Models\Kyc\DocumentoKyc;
use Reda\RedaAlojamiento\Models\Kyc\SolicitudKyc;
use Reda\RedaAlojamiento\Support\PaisesIso;

class ValidadorKycManual
{
    /**
     * Campos de texto de la solicitud que el usuario puede enviar.
     *
     * @var array<int,string>
     */
    public const CAMPOS = [
        'nombres', 'apellidos', 'tipo_documento', 'numero_documento', 'pais_expedicion',
        'fecha_nacimiento', 'pais_residencia', 'ciudad_residencia', 'direccion_residencia', 'correo_contacto',
    ];

    /**
     * Valida el primer envío de una solicitud manual (exige consentimiento y todos los documentos del tipo elegido).
     *
     * @param  array<string,mixed> $datos
     * @param  array<string,mixed> $archivos Archivos recibidos por tipo (frente, reverso, selfie).
     * @return array{ok:bool,errores:array<string,array<int,string>>,datos:array<string,mixed>}
     */
    public function validarEnvio(array $datos, array $archivos, ?string $correoUsuario): array
    {
        $resultado = $this->validarCampos($datos, $correoUsuario, true);

        if ($resultado['ok']) {
            $resultado['errores'] = $this->documentosFaltantes((string) $resultado['datos']['tipo_documento'], array_keys($archivos));
            $resultado['ok']      = empty($resultado['errores']);
        }

        return $resultado;
    }

    /**
     * Valida el reenvío de una corrección (los archivos son opcionales; la presencia de los obligatorios la
     * comprueba KycService con los documentos vigentes).
     *
     * @param  array<string,mixed> $datos
     * @return array{ok:bool,errores:array<string,array<int,string>>,datos:array<string,mixed>}
     */
    public function validarCorreccion(array $datos, ?string $correoUsuario): array
    {
        return $this->validarCampos($datos, $correoUsuario, false);
    }

    /**
     * Tipos de documento (frente, reverso, selfie) obligatorios según el tipo de documento de identidad.
     *
     * @return array<int,string>
     */
    public function tiposObligatorios(string $tipoDocumento): array
    {
        $config = config('kyc.tipos_documento.' . $tipoDocumento, []);
        $tipos  = [DocumentoKyc::TIPO_FRENTE];

        if (!empty($config['requiere_reverso'])) {
            $tipos[] = DocumentoKyc::TIPO_REVERSO;
        }

        if (!empty($config['requiere_selfie'])) {
            $tipos[] = DocumentoKyc::TIPO_SELFIE;
        }

        return $tipos;
    }

    /**
     * Errores por cada documento obligatorio que no se envió.
     *
     * @param  array<int,string> $recibidos
     * @return array<string,array<int,string>>
     */
    public function documentosFaltantes(string $tipoDocumento, array $recibidos): array
    {
        $errores = [];

        foreach ($this->tiposObligatorios($tipoDocumento) as $tipo) {
            if (!in_array($tipo, $recibidos, true)) {
                $errores['documento_' . $tipo] = [__('Este documento es obligatorio.')];
            }
        }

        return $errores;
    }

    /**
     * Normaliza y valida los campos de texto.
     *
     * @param  array<string,mixed> $entrada
     * @return array{ok:bool,errores:array<string,array<int,string>>,datos:array<string,mixed>}
     */
    private function validarCampos(array $entrada, ?string $correoUsuario, bool $exigirConsentimiento): array
    {
        $datos = [];

        foreach (self::CAMPOS as $campo) {
            $valor = $entrada[$campo] ?? null;
            $datos[$campo] = is_string($valor) ? trim((string) preg_replace('/\s+/u', ' ', $valor)) : null;
        }

        $datos['pais_expedicion'] = $datos['pais_expedicion'] !== null ? strtoupper($datos['pais_expedicion']) : null;
        $datos['pais_residencia'] = $datos['pais_residencia'] !== null ? strtoupper($datos['pais_residencia']) : null;

        if (($datos['correo_contacto'] ?? '') === '') {
            $datos['correo_contacto'] = $correoUsuario;
        }

        $textoNombre = '/^[\pL\pM][\pL\pM\s\'’.\-]*$/u';
        $reglas = [
            'nombres'              => ['required', 'string', 'min:2', 'max:100', 'regex:' . $textoNombre],
            'apellidos'            => ['required', 'string', 'min:2', 'max:100', 'regex:' . $textoNombre],
            'tipo_documento'       => ['required', 'in:' . implode(',', array_keys((array) config('kyc.tipos_documento', [])))],
            'numero_documento'     => ['required', 'string', 'min:4', 'max:30', 'regex:/^[A-Za-z0-9][A-Za-z0-9\-\.\s]*$/'],
            'pais_expedicion'      => ['required', 'in:' . implode(',', PaisesIso::codigos())],
            'fecha_nacimiento'     => ['required', 'date_format:Y-m-d', 'after:1900-01-01',
                                       'before_or_equal:' . now()->subYears((int) config('kyc.manual.edad_minima', 18))->format('Y-m-d')],
            'pais_residencia'      => ['required', 'in:' . implode(',', PaisesIso::codigos())],
            'ciudad_residencia'    => ['required', 'string', 'min:2', 'max:120', 'regex:/^[\pL\pM\pN][\pL\pM\pN\s\'’.,\-()]*$/u'],
            'direccion_residencia' => ['required', 'string', 'min:5', 'max:255', 'regex:/^[^\x00-\x1F\x7F<>]+$/u'],
            'correo_contacto'      => ['nullable', 'string', 'max:190', 'email:rfc'],
        ];

        $verificar = $datos;

        if ($exigirConsentimiento) {
            $reglas['acepta_tratamiento'] = ['accepted'];
            $verificar['acepta_tratamiento'] = $entrada['acepta_tratamiento'] ?? null;
        }

        $validador = Validator::make($verificar, $reglas, $this->mensajes(), $this->atributos());

        $validador->after(function ($v) use ($datos) {
            $normalizado = SolicitudKyc::normalizarDocumento($datos['numero_documento'] ?? '');

            if (!$v->errors()->has('numero_documento') && (strlen($normalizado) < 4 || strlen($normalizado) > 20)) {
                $v->errors()->add('numero_documento', __('El número de documento no es válido.'));
            }
        });

        if ($validador->fails()) {
            return ['ok' => false, 'errores' => $validador->errors()->toArray(), 'datos' => $datos];
        }

        return ['ok' => true, 'errores' => [], 'datos' => $datos];
    }

    /**
     * Mensajes de validación en español, independientes de los archivos de idioma del proyecto.
     *
     * @return array<string,string>
     */
    private function mensajes(): array
    {
        return [
            'required'        => __('El campo :attribute es obligatorio.'),
            'string'          => __('El campo :attribute no es válido.'),
            'min'             => __('El campo :attribute es demasiado corto.'),
            'max'             => __('El campo :attribute es demasiado largo.'),
            'regex'           => __('El campo :attribute contiene caracteres no permitidos.'),
            'in'              => __('El valor de :attribute no es válido.'),
            'email'           => __('El correo electrónico no es válido.'),
            'accepted'        => __('Debes aceptar el tratamiento de tus datos personales para continuar.'),
            'date_format'     => __('La fecha debe tener el formato AAAA-MM-DD.'),
            'after'           => __('La fecha de nacimiento no es válida.'),
            'before_or_equal' => __('Debes ser mayor de edad para verificar tu identidad.'),
        ];
    }

    /**
     * Nombres legibles de los campos para los mensajes.
     *
     * @return array<string,string>
     */
    private function atributos(): array
    {
        return [
            'nombres'              => __('nombres'),
            'apellidos'            => __('apellidos'),
            'tipo_documento'       => __('tipo de documento'),
            'numero_documento'     => __('número de documento'),
            'pais_expedicion'      => __('país de expedición'),
            'fecha_nacimiento'     => __('fecha de nacimiento'),
            'pais_residencia'      => __('país de residencia'),
            'ciudad_residencia'    => __('ciudad de residencia'),
            'direccion_residencia' => __('dirección de residencia'),
            'correo_contacto'      => __('correo electrónico'),
            'acepta_tratamiento'   => __('aceptación del tratamiento de datos'),
        ];
    }
}
