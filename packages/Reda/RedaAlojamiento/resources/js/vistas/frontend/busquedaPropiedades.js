/**
 * Script busquedaPropiedades.js
 * 
 * Resumen:
 * Gestiona el control y selección de fechas en el formulario de búsqueda de propiedades
 * (#front-search-form) tanto en la vista principal (Home) como en el buscador.
 * 
 * Responsabilidades:
 * - Oculta los campos de fecha originales (#startDate, #endDate) y su contenedor (#daterange-btn).
 * - Inhibe y neutraliza la inicialización de la librería daterangepicker original del core para evitar
 *   que sobreescriba y sincronice involuntariamente la fecha de inicio con la final y viceversa.
 * - Inyecta dinámicamente nuevos campos de fecha independientes (#new_startDate, #new_endDate).
 * - Integra Flatpickr con localización en español para seleccionar rangos de fecha de forma
 *   independiente (Check-In y Check-Out) manteniendo la integridad del rango.
 * - Sincroniza en tiempo real los valores seleccionados con los campos originales (#startDate, #endDate)
 *   para garantizar que el envío del formulario procese correctamente los parámetros de búsqueda.
 */

(function($) {
    "use strict";

    const BusquedaPropiedades = {
        config: {
            formularioId: '#front-search-form',
            contenedorRangoOriginalId: '#daterange-btn',
            inputInicioOriginalId: '#startDate',
            inputFinOriginalId: '#endDate',
            nuevoContenedorId: '#reda-front-daterange',
            nuevoInputInicioId: '#new_startDate',
            nuevoInputFinId: '#new_endDate'
        },

        instanciaFlatpickrInicio: null,
        instanciaFlatpickrFin: null,

        /**
         * Inicializa la lógica de búsqueda de propiedades si el formulario existe en el DOM.
         */
        init: function() {
            if (!$(this.config.formularioId).length) return;

            console.log('REDA: Inicializando control de fechas Flatpickr en formulario de búsqueda de propiedades');

            this.neutralizarDaterangepickerOriginal();
            this.inyectarNuevosCamposFecha();
            this.inicializarFlatpickr();
            this.vincularEventos();
        },

        /**
         * Neutraliza e inhibe la librería daterangepicker original del core para evitar que
         * sincronice involuntariamente las fechas de inicio y fin con el mismo valor.
         */
        neutralizarDaterangepickerOriginal: function() {
            const $formulario = $(this.config.formularioId);
            const $btnRango = $formulario.find(this.config.contenedorRangoOriginalId);

            if ($btnRango.length) {
                $btnRango.off('.daterangepicker');
                if ($btnRango.data('daterangepicker')) {
                    console.log('REDA: Desactivando daterangepicker en formulario de búsqueda');
                    $btnRango.data('daterangepicker').remove();
                }
            }

            // Ocultamos el contenedor original
            $btnRango.addClass('d-none').hide();

            // Quitamos el atributo 'required' de los inputs originales ocultos para evitar
            // que el navegador bloquee el envío del formulario con error de elemento no enfocable
            $formulario.find(this.config.inputInicioOriginalId).removeAttr('required');
            $formulario.find(this.config.inputFinOriginalId).removeAttr('required');

            // Interceptar window.dateRangeBtn para que no reactive daterangepicker en este formulario
            if (typeof window.dateRangeBtn === 'function' && !window.dateRangeBtn._redaWrapped) {
                const fnOriginal = window.dateRangeBtn;
                window.dateRangeBtn = function() {
                    if ($('#front-search-form').length) {
                        $('#front-search-form #daterange-btn').off('.daterangepicker');
                        if ($('#front-search-form #daterange-btn').data('daterangepicker')) {
                            $('#front-search-form #daterange-btn').data('daterangepicker').remove();
                        }
                        return;
                    }
                    return fnOriginal.apply(this, arguments);
                };
                window.dateRangeBtn._redaWrapped = true;
            }
        },

        /**
         * Inyecta los nuevos inputs de fecha si aún no existen en el formulario.
         */
        inyectarNuevosCamposFecha: function() {
            const $formulario = $(this.config.formularioId);
            if ($formulario.find(this.config.nuevoContenedorId).length) return;

            const $btnRango = $formulario.find(this.config.contenedorRangoOriginalId);
            if (!$btnRango.length) return;

            const etiquetaLlegada = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Llegada"]) || "Llegada";
            const etiquetaSalida = (window.RedaAlojamientoJson && window.RedaAlojamientoJson["Salida"]) || "Salida";

            const htmlNuevosCampos = `
                <div class="d-flex reda-new-daterange-container-front" id="reda-front-daterange">
                    <div class="input-group mr-2 pt-4">
                        <input class="form-control p-3 border-right-0 border text-14 reda-flatpickr-input" 
                               id="new_startDate" 
                               placeholder="${etiquetaLlegada}" 
                               type="text" 
                               readonly 
                               required>
                        <span class="input-group-append">
                            <div class="input-group-text bg-white border-left-0 cursor-pointer">
                                <i class="fa fa-calendar success-text text-14"></i>
                            </div>
                        </span>
                    </div>

                    <div class="input-group ml-2 pt-4">
                        <input class="form-control p-3 border-right-0 border text-14 reda-flatpickr-input" 
                               id="new_endDate" 
                               placeholder="${etiquetaSalida}" 
                               type="text" 
                               readonly 
                               required>
                        <span class="input-group-append">
                            <div class="input-group-text bg-white border-left-0 cursor-pointer">
                                <i class="fa fa-calendar success-text text-14"></i>
                            </div>
                        </span>
                    </div>
                </div>
            `;

            $btnRango.after(htmlNuevosCampos);
        },

        /**
         * Inicializa Flatpickr en los nuevos inputs con sincronización hacia los campos originales.
         */
        inicializarFlatpickr: function() {
            const self = this;
            const $inputInicioOrig = $(this.config.inputInicioOriginalId);
            const $inputFinOrig = $(this.config.inputFinOriginalId);

            const valorInicialInicio = $inputInicioOrig.val();
            const valorInicialFin = $inputFinOrig.val();

            // Destruir instancias previas si existen
            if (this.instanciaFlatpickrInicio && typeof this.instanciaFlatpickrInicio.destroy === 'function') {
                this.instanciaFlatpickrInicio.destroy();
            }
            if (this.instanciaFlatpickrFin && typeof this.instanciaFlatpickrFin.destroy === 'function') {
                this.instanciaFlatpickrFin.destroy();
            }

            if (typeof flatpickr === 'undefined') {
                setTimeout(function() {
                    self.inicializarFlatpickr();
                }, 100);
                return;
            }

            // Inicializar Fecha de Llegada (Check-In)
            this.instanciaFlatpickrInicio = flatpickr(this.config.nuevoInputInicioId, {
                dateFormat: "d-m-Y",
                minDate: "today",
                locale: "es",
                defaultDate: valorInicialInicio || "",
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates[0]) {
                        console.log('REDA Flatpickr (Búsqueda): Llegada seleccionada:', dateStr);
                        $inputInicioOrig.val(dateStr);

                        // Configurar la fecha mínima de salida (Checkout) para el día siguiente
                        const diaSiguiente = new Date(selectedDates[0]);
                        diaSiguiente.setDate(diaSiguiente.getDate() + 1);

                        if (self.instanciaFlatpickrFin) {
                            self.instanciaFlatpickrFin.set("minDate", diaSiguiente);

                            // Si la fecha de salida es menor o igual a la de llegada, limpiarla
                            if (self.instanciaFlatpickrFin.selectedDates[0] && self.instanciaFlatpickrFin.selectedDates[0] <= selectedDates[0]) {
                                self.instanciaFlatpickrFin.clear();
                                $inputFinOrig.val("");
                            }
                        }
                    } else {
                        $inputInicioOrig.val("");
                    }
                }
            });

            // Determinar fecha mínima inicial de salida basada en la llegada seleccionada
            const fechaMinimaSalida = (this.instanciaFlatpickrInicio && this.instanciaFlatpickrInicio.selectedDates[0])
                ? new Date(this.instanciaFlatpickrInicio.selectedDates[0].getTime() + 86400000)
                : "today";

            // Inicializar Fecha de Salida (Check-Out)
            this.instanciaFlatpickrFin = flatpickr(this.config.nuevoInputFinId, {
                dateFormat: "d-m-Y",
                minDate: fechaMinimaSalida,
                locale: "es",
                defaultDate: valorInicialFin || "",
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates[0]) {
                        console.log('REDA Flatpickr (Búsqueda): Salida seleccionada:', dateStr);
                        $inputFinOrig.val(dateStr);
                    } else {
                        $inputFinOrig.val("");
                    }
                }
            });

            // Si ya existía fecha de inicio seleccionada, sincronizar minDate de salida
            if (this.instanciaFlatpickrInicio && this.instanciaFlatpickrInicio.selectedDates[0]) {
                const diaSiguiente = new Date(this.instanciaFlatpickrInicio.selectedDates[0]);
                diaSiguiente.setDate(diaSiguiente.getDate() + 1);
                if (this.instanciaFlatpickrFin) {
                    this.instanciaFlatpickrFin.set("minDate", diaSiguiente);
                }
            }
        },

        /**
         * Vincula eventos de clic y asegura que las fechas estén sincronizadas antes de enviar.
         */
        vincularEventos: function() {
            const self = this;

            // Al hacer clic en el ícono del calendario, abrir el Flatpickr correspondiente
            $(document).on('click', `${this.config.nuevoContenedorId} .input-group-append`, function(e) {
                e.preventDefault();
                const $input = $(this).closest('.input-group').find('.reda-flatpickr-input');
                if ($input.attr('id') === 'new_startDate' && self.instanciaFlatpickrInicio) {
                    self.instanciaFlatpickrInicio.open();
                } else if ($input.attr('id') === 'new_endDate' && self.instanciaFlatpickrFin) {
                    self.instanciaFlatpickrFin.open();
                }
            });

            // Asegurar sincronización al enviar el formulario
            $(document).on('submit', this.config.formularioId, function() {
                const valInicio = $(self.config.nuevoInputInicioId).val();
                const valFin = $(self.config.nuevoInputFinId).val();

                if (valInicio) {
                    $(self.config.inputInicioOriginalId).val(valInicio);
                }
                if (valFin) {
                    $(self.config.inputFinOriginalId).val(valFin);
                }
            });

            // Re-ejecutar neutralización preventiva por si scripts asíncronos del core (ej: front.min.js) se cargan después
            setTimeout(() => self.neutralizarDaterangepickerOriginal(), 300);
            setTimeout(() => self.neutralizarDaterangepickerOriginal(), 800);
            setTimeout(() => self.neutralizarDaterangepickerOriginal(), 1500);
            setTimeout(() => self.neutralizarDaterangepickerOriginal(), 3000);

            // Re-verificar si se abre cualquier modal que contenga el formulario
            $(document).on('shown.bs.modal', function() {
                if ($(self.config.formularioId).length) {
                    self.init();
                }
            });
        }
    };

    $(function() {
        BusquedaPropiedades.init();
    });

})(jQuery);
