<?php

if (!function_exists('reda_money_format')) {
    /**
     * Formatea un valor monetario con separador de miles (.) y decimales (,)
     * @param string $symbol Símbolo de la moneda
     * @param float $value Valor numérico
     * @return string
     */
    function reda_money_format($symbol, $value)
    {
        $formattedValue = number_format($value, 2, ',', '.');
        $symbolPosition = reda_currency_symbol_position();
        
        if ($symbolPosition == "before") {
            return $symbol . ' ' . $formattedValue;
        } else {
            return $formattedValue . ' ' . $symbol;
        }
    }
}

if (!function_exists('reda_number_format')) {
    /**
     * Formatea un número con separador de miles (.) y decimales (,)
     * @param float $number
     * @param int $decimal
     * @return string
     */
    function reda_number_format($number, $decimal)
    {
        return number_format($number, $decimal, ',', '.');
    }
}

if (!function_exists('reda_currency_symbol_position')) {
    /**
     * Obtiene la posición del símbolo de moneda desde la configuración
     * @return string
     */
    function reda_currency_symbol_position()
    {
        $position = settings('money_format');
        return !empty($position) ? $position : 'after';
    }
}

if (!function_exists('reda_get_inbox_unread_count')) {
    /**
     * Obtiene el conteo de mensajes no leídos de forma segura.
     * @return int
     */
    function reda_get_inbox_unread_count()
    {
        if (!auth()->check()) {
            return 0;
        }
        return DB::table(DB::raw("(SELECT * from messages where receiver_id=".auth()->id()." and `read`=0 ORDER by id DESC) as msg"))
            ->groupBy('booking_id')
            ->get()->count();
    }
}

if (!function_exists('reda_get_profile_src')) {
    /**
     * Obtiene la ruta de la imagen de perfil a partir del directorio public/.
     * @param mixed $model Modelo User o Admin
     * @param string $type Tipo de usuario ('user' o 'admin')
     * @return string Ruta de la imagen
     */
    function reda_get_profile_src($model, $type = 'user')
    {
        if (!$model) return '/public/img/unnamed.png';
        
        $profileImage = $model->profile_image ?? '';
        $id = $model->id;
        
        if ($profileImage == '') {
            return ($type == 'admin') 
                ? '/public/images/user_pic.jpg' 
                : '/public/images/default-profile.png';
        }
        
        return '/public/images/profile/' . $id . '/' . $profileImage;
    }
}

if (!function_exists('reda_obtener_desglose_huespedes')) {
    /**
     * Obtiene el desglose de huéspedes (Adultos y Niños) para una reservación.
     * Consulta prioritariamente la tabla auxiliar 'reserva_huespedes', luego 'booking_details',
     * y como fallback seguro para reservas históricas asigna todos los 'guest' a Adultos.
     *
     * @param \App\Models\Bookings|int|object $booking Instancia de reserva o su ID
     * @return array ['adultos' => int, 'ninos' => int, 'total' => int, 'texto' => string]
     */
    function reda_obtener_desglose_huespedes($booking)
    {
        if (is_numeric($booking)) {
            $booking = \App\Models\Bookings::with('booking_details')->find($booking);
        }

        if (!$booking) {
            return [
                'adultos' => 1,
                'ninos' => 0,
                'total' => 1,
                'texto' => '1 ' . __('Adulto')
            ];
        }

        $adultos = null;
        $ninos = null;

        // 1. Intentar consultar desde la tabla auxiliar 'reserva_huespedes' si existe
        if (\Illuminate\Support\Facades\Schema::hasTable('reserva_huespedes') && isset($booking->id)) {
            $registro = \Reda\RedaAlojamiento\Models\Reserva\ReservaHuesped::where('reserva_id', $booking->id)->first();
            if ($registro) {
                $adultos = (int) $registro->adultos;
                $ninos = (int) $registro->ninos;
            }
        }

        // 2. Si no se encontró en tabla auxiliar, consultar en la relación 'booking_details'
        if ($adultos === null && isset($booking->booking_details)) {
            $detalleAdultos = $booking->booking_details->firstWhere('field', 'adultos');
            $detalleNinos = $booking->booking_details->firstWhere('field', 'ninos');

            if ($detalleAdultos) {
                $adultos = (int) $detalleAdultos->value;
            }
            if ($detalleNinos) {
                $ninos = (int) $detalleNinos->value;
            }
        }

        // 3. Si la relación no estaba cargada pero tenemos el id del booking
        if ($adultos === null && isset($booking->id)) {
            $detalleAdultos = \App\Models\BookingDetails::where('booking_id', $booking->id)->where('field', 'adultos')->first();
            $detalleNinos = \App\Models\BookingDetails::where('booking_id', $booking->id)->where('field', 'ninos')->first();

            if ($detalleAdultos) {
                $adultos = (int) $detalleAdultos->value;
            }
            if ($detalleNinos) {
                $ninos = (int) $detalleNinos->value;
            }
        }

        // 4. Fallback retrocompatible: Si la reserva no posee desglose explícito, todos los 'guest' son Adultos
        $totalGuest = isset($booking->guest) ? (int) $booking->guest : 1;
        if ($adultos === null) {
            $adultos = max(1, $totalGuest);
            $ninos = 0;
        }
        if ($ninos === null) {
            $ninos = 0;
        }

        $total = $adultos + $ninos;

        // Construcción de texto descriptivo en español
        $textoAdultos = "{$adultos} " . ($adultos == 1 ? __('Adulto') : __('Adultos'));
        $textoNinos = $ninos > 0 ? ", {$ninos} " . ($ninos == 1 ? __('Niño') : __('Niños')) : '';
        $textoCompleto = $textoAdultos . $textoNinos;

        return [
            'adultos' => $adultos,
            'ninos' => $ninos,
            'total' => $total,
            'texto' => $textoCompleto
        ];
    }
}

