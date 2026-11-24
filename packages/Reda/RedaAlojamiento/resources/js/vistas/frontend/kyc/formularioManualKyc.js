/**
 * Resumen: Formulario de verificación manual del usuario (modal de Bootstrap 4). Reúne los datos personales y las
 * fotografías de los documentos, adelanta las validaciones básicas (campos obligatorios, mayoría de edad, tipo y
 * tamaño de archivo) y envía todo al servidor, que repite y decide todas las validaciones. Los documentos que se
 * piden dependen del tipo elegido (por ejemplo, el pasaporte no tiene reverso). También atiende las correcciones
 * pedidas por un administrador: precarga los datos y deja los documentos como opcionales (se conserva el actual
 * si no se elige uno nuevo).
 *
 * Diseño: formulario de una columna en el celular (campos grandes, 16 px para evitar el zoom de iOS) y de dos
 * columnas en escritorio. Los errores del servidor se muestran junto a cada campo.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Resources\Js\Vistas\Frontend\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

import { enviarSolicitudManualKyc } from './enviarSolicitudManualKyc.js';
import { t, escapar, mensajeSeguro, formatearTamano } from './utilidadesKyc.js';

const ID_MODAL = 'modal-kyc-manual';

/**
 * Fecha en formato AAAA-MM-DD según la hora local (no UTC) restando años.
 *
 * @param {number} anios
 * @returns {string}
 */
const fechaHaceAnios = (anios) => {
    const fecha = new Date();
    fecha.setFullYear(fecha.getFullYear() - anios);
    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
    const dia = String(fecha.getDate()).padStart(2, '0');
    return `${fecha.getFullYear()}-${mes}-${dia}`;
};

/**
 * Lista de países [{codigo, nombre}] ordenada por nombre, con el predeterminado primero.
 *
 * @param {string[]} codigos       Códigos ISO admitidos por el servidor.
 * @param {string}   predeterminado Código que debe aparecer primero.
 * @returns {Array<{codigo:string,nombre:string}>}
 */
const listarPaises = (codigos, predeterminado) => {
    let nombres = null;
    try {
        nombres = new Intl.DisplayNames([document.documentElement.lang || 'es'], { type: 'region' });
    } catch (e) {
        nombres = null;
    }

    const paises = (codigos || []).map((codigo) => {
        let nombre = codigo;
        try {
            nombre = (nombres && nombres.of(codigo)) || codigo;
        } catch (e) {
            nombre = codigo;
        }
        return { codigo, nombre };
    });

    paises.sort((a, b) => {
        if (a.codigo === predeterminado) return -1;
        if (b.codigo === predeterminado) return 1;
        return a.nombre.localeCompare(b.nombre, 'es');
    });

    return paises;
};

/**
 * HTML de las opciones de un <select> de países.
 *
 * @param {Array<{codigo:string,nombre:string}>} paises
 * @param {string} seleccionado
 * @returns {string}
 */
const opcionesPaises = (paises, seleccionado) => paises.map((p) =>
    `<option value="${escapar(p.codigo)}" ${p.codigo === seleccionado ? 'selected' : ''}>${escapar(p.nombre)}</option>`
).join('');

/**
 * HTML de un campo de texto, fecha o correo.
 *
 * @param {Object}  c
 * @param {string}  c.nombre      Atributo name.
 * @param {string}  c.etiqueta
 * @param {string}  [c.tipo='text']
 * @param {string}  [c.valor='']
 * @param {boolean} [c.requerido=true]
 * @param {string}  [c.ayuda='']
 * @param {string}  [c.atributos='']  Atributos HTML extra ya escapados.
 * @param {string}  [c.columnas='col-md-6']
 * @returns {string}
 */
const campoTexto = ({ nombre, etiqueta, tipo = 'text', valor = '', requerido = true, ayuda = '', atributos = '', columnas = 'col-md-6' }) => `
    <div class="form-group ${columnas}">
        <label for="kyc-${escapar(nombre)}">${escapar(etiqueta)} ${requerido ? '<span class="text-danger">*</span>' : `<span class="text-muted">(${escapar(t('opcional'))})</span>`}</label>
        <input type="${escapar(tipo)}" class="form-control reda-kyc-input" id="kyc-${escapar(nombre)}" name="${escapar(nombre)}" value="${escapar(valor)}" ${requerido ? 'required' : ''} ${atributos}>
        ${ayuda ? `<small class="form-text text-muted">${escapar(ayuda)}</small>` : ''}
        <div class="invalid-feedback"></div>
    </div>`;

/**
 * HTML de un campo de selección.
 *
 * @param {Object} c
 * @param {string} c.nombre
 * @param {string} c.etiqueta
 * @param {string} c.opciones HTML de las <option>.
 * @param {string} [c.columnas='col-md-6']
 * @returns {string}
 */
const campoSeleccion = ({ nombre, etiqueta, opciones, columnas = 'col-md-6' }) => `
    <div class="form-group ${columnas}">
        <label for="kyc-${escapar(nombre)}">${escapar(etiqueta)} <span class="text-danger">*</span></label>
        <select class="form-control reda-kyc-input" id="kyc-${escapar(nombre)}" name="${escapar(nombre)}" required>${opciones}</select>
        <div class="invalid-feedback"></div>
    </div>`;

/**
 * HTML de un espacio para subir un documento.
 *
 * @param {string}  tipo        frente | reverso | selfie
 * @param {boolean} soloImagen  La selfie no admite PDF.
 * @param {string[]} extensiones
 * @returns {string}
 */
const espacioArchivo = (tipo, soloImagen, extensiones) => {
    const aceptar = soloImagen
        ? 'image/jpeg,image/png'
        : extensiones.map((e) => (e === 'pdf' ? 'application/pdf' : '.' + e)).join(',');

    return `
    <div class="reda-kyc-archivo" data-kyc-archivo="${escapar(tipo)}">
        <div class="reda-kyc-archivo__encabezado">
            <strong class="reda-kyc-archivo__titulo"></strong>
            <small class="reda-kyc-archivo__ayuda text-muted"></small>
        </div>
        <label class="reda-kyc-archivo__selector" for="kyc-documento_${escapar(tipo)}">
            <span class="reda-kyc-archivo__vista"><i class="fa fa-camera" aria-hidden="true"></i></span>
            <span class="reda-kyc-archivo__nombre">${escapar(t('Elegir o tomar foto'))}</span>
        </label>
        <input type="file" class="reda-kyc-archivo__input" id="kyc-documento_${escapar(tipo)}" name="documento_${escapar(tipo)}" accept="${escapar(aceptar)}">
        <div class="invalid-feedback"></div>
    </div>`;
};

/**
 * Abre el formulario de verificación manual.
 *
 * @param {Object}  parametros
 * @param {Object}  parametros.opciones    Respuesta de GET /kyc/manual/opciones.
 * @param {Object|null} parametros.correccion Respuesta de GET /kyc/manual/solicitud (modo corrección) o null.
 */
export const abrirFormularioManualKyc = ({ opciones, correccion = null }) => {
    (function ($) {
        "use strict";

        const esCorreccion = correccion !== null;
        const datos = esCorreccion ? (correccion.datos || {}) : {};
        const documentosExistentes = esCorreccion ? (correccion.documentos || []).map((d) => d.tipo) : [];
        const tipos = opciones.tipos_documento || [];
        const limites = opciones.archivos || {};
        const extensiones = limites.extensiones || ['jpg', 'jpeg', 'png', 'pdf'];
        const soloImagen = limites.solo_imagen || ['selfie'];
        const tamanoMaximo = (limites.tamano_maximo_kb || 5120) * 1024;
        const paises = listarPaises(opciones.paises, opciones.pais_predeterminado);
        const edadMinima = opciones.edad_minima || 18;
        const tipoInicial = datos.tipo_documento || (tipos[0] ? tipos[0].clave : '');

        $('#' + ID_MODAL).remove();

        const opcionesTipos = tipos.map((tipo) =>
            `<option value="${escapar(tipo.clave)}" ${tipo.clave === tipoInicial ? 'selected' : ''}>${escapar(tipo.etiqueta)}</option>`
        ).join('');

        const html = `
        <div class="modal fade reda-kyc-modal" id="${ID_MODAL}" tabindex="-1" role="dialog" aria-labelledby="${ID_MODAL}-titulo" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <form class="modal-content" id="${ID_MODAL}-formulario" novalidate autocomplete="off">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${ID_MODAL}-titulo">${escapar(esCorreccion ? t('Corregir mi solicitud de verificación') : t('Verificación manual de identidad'))}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="${escapar(t('Cerrar'))}"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        ${esCorreccion && correccion.correccion_solicitada ? `
                        <div class="reda-kyc-cita reda-kyc-cita--warning mb-3">
                            <strong>${escapar(t('Lo que debes corregir'))}</strong>
                            <div class="reda-kyc-cita__texto">${escapar(correccion.correccion_solicitada)}</div>
                        </div>` : `<p class="text-muted">${escapar(t('Completa tus datos tal como aparecen en tu documento y sube las fotos solicitadas. Una persona las revisará.'))}</p>`}

                        <h6 class="reda-kyc-seccion">${escapar(t('Tu documento'))}</h6>
                        <div class="form-row">
                            ${campoSeleccion({ nombre: 'tipo_documento', etiqueta: t('Tipo de documento'), opciones: opcionesTipos })}
                            ${campoSeleccion({ nombre: 'pais_expedicion', etiqueta: t('País que expidió el documento'), opciones: opcionesPaises(paises, datos.pais_expedicion || opciones.pais_predeterminado) })}
                            ${campoTexto({ nombre: 'numero_documento', etiqueta: t('Número de documento'), valor: datos.numero_documento || '', atributos: 'maxlength="30"' })}
                        </div>

                        <h6 class="reda-kyc-seccion">${escapar(t('Tus datos'))}</h6>
                        <div class="form-row">
                            ${campoTexto({ nombre: 'nombres', etiqueta: t('Nombres (como en el documento)'), valor: datos.nombres || '', atributos: 'maxlength="100" autocomplete="given-name"' })}
                            ${campoTexto({ nombre: 'apellidos', etiqueta: t('Apellidos (como en el documento)'), valor: datos.apellidos || '', atributos: 'maxlength="100" autocomplete="family-name"' })}
                            ${campoTexto({ nombre: 'fecha_nacimiento', etiqueta: t('Fecha de nacimiento'), tipo: 'date', valor: datos.fecha_nacimiento || '', atributos: `min="1900-01-02" max="${escapar(fechaHaceAnios(edadMinima))}"`, ayuda: t('Debes ser mayor de edad.') })}
                            ${campoTexto({ nombre: 'correo_contacto', etiqueta: t('Correo de contacto'), tipo: 'email', valor: datos.correo_contacto || '', requerido: false, atributos: 'maxlength="190"', ayuda: t('Solo si es distinto al de tu cuenta.') })}
                        </div>

                        <h6 class="reda-kyc-seccion">${escapar(t('Dónde vives'))}</h6>
                        <div class="form-row">
                            ${campoSeleccion({ nombre: 'pais_residencia', etiqueta: t('País de residencia'), opciones: opcionesPaises(paises, datos.pais_residencia || opciones.pais_predeterminado) })}
                            ${campoTexto({ nombre: 'ciudad_residencia', etiqueta: t('Ciudad de residencia'), valor: datos.ciudad_residencia || '', atributos: 'maxlength="120"' })}
                            ${campoTexto({ nombre: 'direccion_residencia', etiqueta: t('Dirección de residencia'), valor: datos.direccion_residencia || '', atributos: 'maxlength="255" autocomplete="street-address"', columnas: 'col-12' })}
                        </div>

                        <h6 class="reda-kyc-seccion">${escapar(t('Fotos de tus documentos'))}</h6>
                        <p class="text-muted small mb-2" id="kyc-ayuda-archivos"></p>
                        <div class="reda-kyc-archivos">
                            ${espacioArchivo('frente', soloImagen.includes('frente'), extensiones)}
                            ${espacioArchivo('reverso', soloImagen.includes('reverso'), extensiones)}
                            ${espacioArchivo('selfie', soloImagen.includes('selfie'), extensiones)}
                        </div>

                        ${esCorreccion ? '' : `
                        <div class="form-group form-check mt-3 reda-kyc-consentimiento">
                            <input type="checkbox" class="form-check-input" id="kyc-acepta_tratamiento" name="acepta_tratamiento" value="1">
                            <label class="form-check-label" for="kyc-acepta_tratamiento">${escapar(t('Acepto que Torbian trate mis datos personales y documentos únicamente para verificar mi identidad.'))}</label>
                            <div class="invalid-feedback"></div>
                        </div>`}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">${escapar(t('Cancelar'))}</button>
                        <button type="submit" class="btn btn-primary reda-kyc-enviar">${escapar(esCorreccion ? t('Enviar corrección') : t('Enviar solicitud'))}</button>
                    </div>
                </form>
            </div>
        </div>`;

        $('body').append(html);

        const $modal = $('#' + ID_MODAL);
        const $formulario = $('#' + ID_MODAL + '-formulario');
        const $boton = $formulario.find('.reda-kyc-enviar');

        /**
         * Muestra u oculta los espacios de archivo según el tipo de documento elegido y rotula cada uno.
         */
        const actualizarArchivos = () => {
            const clave = $formulario.find('[name="tipo_documento"]').val();
            const tipo = tipos.find((x) => x.clave === clave) || {};
            const requeridos = { frente: true, reverso: !!tipo.requiere_reverso, selfie: !!tipo.requiere_selfie };
            const titulos = {
                frente: clave === 'pasaporte' ? t('Página de datos del pasaporte') : t('Frente del documento'),
                reverso: t('Reverso del documento'),
                selfie: t('Selfie con el documento'),
            };
            const ayudas = {
                frente: t('Foto nítida, completa y sin reflejos.'),
                reverso: t('Foto nítida de la parte de atrás.'),
                selfie: t('Una foto tuya sosteniendo el documento junto a tu rostro.'),
            };

            Object.keys(requeridos).forEach((nombre) => {
                const $espacio = $formulario.find(`[data-kyc-archivo="${nombre}"]`);
                $espacio.toggleClass('d-none', !requeridos[nombre]);
                $espacio.find('input[type="file"]').prop('disabled', !requeridos[nombre]);
                $espacio.find('.reda-kyc-archivo__titulo').text(titulos[nombre] + (esCorreccion ? '' : ' *'));
                const existente = esCorreccion && documentosExistentes.includes(nombre);
                $espacio.find('.reda-kyc-archivo__ayuda').text(existente ? t('Ya enviaste este documento. Elige uno nuevo solo si debes reemplazarlo.') : ayudas[nombre]);
            });

            const tiposPermitidos = extensiones.map((e) => e.toUpperCase()).join(', ');
            $formulario.find('#kyc-ayuda-archivos').text(
                t('Formatos: :f. Tamaño máximo: :m por archivo. La selfie solo admite imágenes.')
                    .replace(':f', tiposPermitidos)
                    .replace(':m', formatearTamano(tamanoMaximo))
            );
        };

        /**
         * Quita todas las marcas de error del formulario.
         */
        const limpiarErrores = () => {
            $formulario.find('.is-invalid').removeClass('is-invalid');
            $formulario.find('.invalid-feedback').text('').removeClass('d-block');
        };

        /**
         * Marca un campo con su mensaje de error.
         *
         * @param {string} nombre  Atributo name del campo.
         * @param {string} mensaje
         */
        const marcarError = (nombre, mensaje) => {
            const $campo = $formulario.find(`[name="${nombre}"]`).first();
            if (!$campo.length) return;

            $campo.addClass('is-invalid');
            const $contenedor = $campo.closest('.form-group, .reda-kyc-archivo, .form-check');
            $contenedor.find('.invalid-feedback').first().text(mensaje).addClass('d-block');
        };

        /**
         * Muestra errores devueltos por el servidor ({campo: [mensajes]}) y lleva la vista al primero.
         *
         * @param {Object} errores
         */
        const mostrarErrores = (errores) => {
            Object.keys(errores || {}).forEach((nombre) => {
                const mensajes = Array.isArray(errores[nombre]) ? errores[nombre] : [errores[nombre]];
                marcarError(nombre, String(mensajes[0] || ''));
            });

            const $primero = $formulario.find('.is-invalid').first();
            if ($primero.length) {
                const $cuerpo = $modal.find('.modal-body');
                $cuerpo.animate({ scrollTop: $cuerpo.scrollTop() + $primero.closest('.form-group, .reda-kyc-archivo, .form-check').position().top - 12 }, 200);
            }
        };

        /**
         * Valida en el navegador lo básico (el servidor vuelve a validar todo).
         *
         * @returns {Object} Errores por campo.
         */
        const validarCliente = () => {
            const errores = {};
            const obligatorio = t('Este campo es obligatorio.');

            ['tipo_documento', 'pais_expedicion', 'numero_documento', 'nombres', 'apellidos', 'fecha_nacimiento',
                'pais_residencia', 'ciudad_residencia', 'direccion_residencia'].forEach((nombre) => {
                if (!String($formulario.find(`[name="${nombre}"]`).val() || '').trim()) {
                    errores[nombre] = obligatorio;
                }
            });

            const nacimiento = String($formulario.find('[name="fecha_nacimiento"]').val() || '');
            if (nacimiento && nacimiento > fechaHaceAnios(edadMinima)) {
                errores.fecha_nacimiento = t('Debes ser mayor de edad (mínimo :n años).').replace(':n', edadMinima);
            }

            const correo = String($formulario.find('[name="correo_contacto"]').val() || '').trim();
            if (correo && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(correo)) {
                errores.correo_contacto = t('El correo electrónico no es válido.');
            }

            ['frente', 'reverso', 'selfie'].forEach((tipo) => {
                const $entrada = $formulario.find(`[name="documento_${tipo}"]`);
                if ($entrada.prop('disabled')) return;

                const archivo = $entrada[0].files && $entrada[0].files[0];
                const clave = `documento_${tipo}`;

                if (!archivo) {
                    if (!(esCorreccion && documentosExistentes.includes(tipo))) {
                        errores[clave] = t('Este documento es obligatorio.');
                    }
                    return;
                }

                const extension = (archivo.name.split('.').pop() || '').toLowerCase();
                if (!extensiones.includes(extension) || (soloImagen.includes(tipo) && extension === 'pdf')) {
                    errores[clave] = t('Formato no permitido.');
                } else if (archivo.size > tamanoMaximo) {
                    errores[clave] = t('El archivo supera el tamaño máximo (:m).').replace(':m', formatearTamano(tamanoMaximo));
                }
            });

            if (!esCorreccion && !$formulario.find('[name="acepta_tratamiento"]').prop('checked')) {
                errores.acepta_tratamiento = t('Debes aceptar el tratamiento de tus datos personales para continuar.');
            }

            return errores;
        };

        /**
         * Arma los datos a enviar: campos de texto y solo los archivos elegidos de los espacios activos.
         *
         * @returns {FormData}
         */
        const construirDatos = () => {
            const datosEnvio = new FormData();

            ['tipo_documento', 'numero_documento', 'pais_expedicion', 'nombres', 'apellidos', 'fecha_nacimiento',
                'pais_residencia', 'ciudad_residencia', 'direccion_residencia', 'correo_contacto'].forEach((nombre) => {
                datosEnvio.append(nombre, String($formulario.find(`[name="${nombre}"]`).val() || '').trim());
            });

            if (!esCorreccion) {
                datosEnvio.append('acepta_tratamiento', '1');
            }

            ['frente', 'reverso', 'selfie'].forEach((tipo) => {
                const $entrada = $formulario.find(`[name="documento_${tipo}"]`);
                const archivo = !$entrada.prop('disabled') && $entrada[0].files && $entrada[0].files[0];
                if (archivo) {
                    datosEnvio.append(`documento_${tipo}`, archivo);
                }
            });

            return datosEnvio;
        };

        // Vista previa del archivo elegido (miniatura si es imagen, icono si es PDF).
        $formulario.on('change', '.reda-kyc-archivo__input', function () {
            const $espacio = $(this).closest('.reda-kyc-archivo');
            const $vista = $espacio.find('.reda-kyc-archivo__vista');
            const $nombre = $espacio.find('.reda-kyc-archivo__nombre');
            const anterior = $vista.data('url');
            if (anterior) {
                URL.revokeObjectURL(anterior);
                $vista.removeData('url');
            }

            const archivo = this.files && this.files[0];
            $(this).removeClass('is-invalid');
            $espacio.find('.invalid-feedback').text('').removeClass('d-block');

            if (!archivo) {
                $vista.html('<i class="fa fa-camera" aria-hidden="true"></i>');
                $nombre.text(t('Elegir o tomar foto'));
                return;
            }

            if (/^image\//.test(archivo.type)) {
                const url = URL.createObjectURL(archivo);
                $vista.data('url', url).html(`<img src="${escapar(url)}" alt="">`);
            } else {
                $vista.html('<i class="fa fa-file-pdf-o" aria-hidden="true"></i>');
            }
            $nombre.text(`${archivo.name} (${formatearTamano(archivo.size)})`);
        });

        $formulario.on('change', '[name="tipo_documento"]', actualizarArchivos);
        $formulario.on('input change', '.reda-kyc-input, [name="acepta_tratamiento"]', function () {
            $(this).removeClass('is-invalid').closest('.form-group, .form-check').find('.invalid-feedback').first().text('').removeClass('d-block');
        });

        $formulario.on('submit', async function (evento) {
            evento.preventDefault();
            limpiarErrores();

            const errores = validarCliente();
            if (Object.keys(errores).length) {
                mostrarErrores(errores);
                return;
            }

            $boton.prop('disabled', true);
            window.RedaNotificaciones.esperar();

            const respuesta = await enviarSolicitudManualKyc(construirDatos(), esCorreccion);

            $boton.prop('disabled', false);

            if (respuesta.success) {
                $modal.modal('hide');
                window.RedaNotificaciones.notificar(
                    t('Solicitud enviada'),
                    mensajeSeguro(respuesta.mensaje_usuario || t('Recibimos tu solicitud. Te avisaremos cuando tenga una decisión.')),
                    'exito',
                    true
                );
                return;
            }

            if (respuesta.code === 422 && respuesta.respuesta && typeof respuesta.respuesta === 'object') {
                mostrarErrores(respuesta.respuesta);
            }

            // Si el estado cambió mientras se completaba el formulario (409), se recarga para mostrar el real.
            if (respuesta.code === 409) {
                $modal.modal('hide');
            }

            window.RedaNotificaciones.notificar(t('Error'), mensajeSeguro(respuesta.mensaje_usuario), 'error', respuesta.code === 409);
        });

        // Bootstrap 4 quita "modal-open" al cerrar la notificación aunque este formulario siga abierto.
        $(document).off('hidden.bs.modal.redaKyc').on('hidden.bs.modal.redaKyc', '#modal-notificacion', function () {
            if ($('#' + ID_MODAL).hasClass('show')) {
                $('body').addClass('modal-open');
            }
        });

        $modal.on('hidden.bs.modal', function () {
            $modal.find('.reda-kyc-archivo__vista').each(function () {
                const url = $(this).data('url');
                if (url) URL.revokeObjectURL(url);
            });
            $modal.remove();
        });

        actualizarArchivos();
        $modal.modal('show');
    })(jQuery);
};
