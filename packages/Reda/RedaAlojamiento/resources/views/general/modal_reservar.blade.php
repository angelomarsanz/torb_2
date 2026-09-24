{{--
 * Vista modal_reservar.blade.php
 * 
 * Propósito: Define la estructura HTML de la modal de reservación del plugin RedaAlojamiento.
 * Responsabilidades:
 * - Renderiza la modal Bootstrap donde se inyecta el formulario original del core (#booking_form).
 * - Carga e integra la librería de calendarios Flatpickr (CSS/JS) desde CDN.
 * - Aplica estilos personalizados Airbnb-style para los nuevos campos de selección de fechas (Llegada y Salida).
 * - Oculta quirúrgicamente los selectores de rango originales del core (#daterange-btn) para evitar conflictos en la modal.
--}}

{{-- Estilos de Flatpickr y personalización de la modal --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Estilos personalizados para los nuevos inputs de llegada y salida en la modal */
    .reda-new-daterange-container {
        margin-top: 15px;
        margin-bottom: 15px;
    }
    .reda-new-daterange-container label {
        font-weight: 600;
        font-size: 13px;
        color: #484848;
        text-transform: uppercase;
        margin-bottom: 5px;
        display: block;
    }
    .reda-flatpickr-input {
        background-color: #fff !important;
        cursor: pointer !important;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px 12px;
        height: 45px;
        font-size: 14px;
        color: #484848;
        width: 100%;
        box-sizing: border-box;
    }
    /* Ocultar el selector de rango original dentro de la modal */
    #modalReservarBody #daterange-btn {
        display: none !important;
    }
</style>

<!-- Modal para el Formulario de Reservación -->
<div class="modal fade reda-modal-reserva" id="modalReservar" tabindex="-1" role="dialog" aria-labelledby="modalReservarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="modalReservarLabel">{{ __('Reservar') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalReservarBody">
                {{-- Aquí se inyectará el formulario mediante JS --}}
            </div>
        </div>
    </div>
</div>

{{-- Scripts de Flatpickr --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

