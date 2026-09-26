<?php

/**
 * Controlador RedaUsuarioController
 *
 * Propósito: Gestiona y adapta los flujos de registro de usuarios y confirmación
 * de correo electrónico dentro del plugin REDA Alojamiento, sin modificar los
 * controladores originales del proyecto base.
 *
 * Responsabilidades:
 * - Sobrescribe la acción `create` para registrar al usuario, enviar el correo de verificación
 *   y redirigir a login sin iniciar sesión automáticamente hasta que se confirme la cuenta.
 * - Sobrescribe la acción `confirmEmail` para permitir la activación de la cuenta desde el enlace
 *   del correo sin requerir una sesión activa previa, activando el estado del usuario y marcando
 *   la verificación del correo en 'yes'.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Http\Controllers\General
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Http\Controllers\General;

use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;
use App\Models\{User, UserDetails, UsersVerification, PasswordResets};
use App\Rules\GoogleReCaptcha;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RedaUsuarioController extends UserController
{
    /**
     * Registra un nuevo usuario en la base de datos, envía el correo de verificación
     * y redirige a la vista de inicio de sesión sin iniciar sesión de manera automática.
     *
     * @param  \Illuminate\Http\Request              $request
     * @param  \App\Http\Controllers\EmailController $email_controller
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request, EmailController $email_controller)
    {
        $rules = [
            'first_name'      => 'required|max:255',
            'last_name'       => 'required|max:255',
            'email'           => 'required|max:255|email|unique:users',
            'password'        => 'required|min:6',
            'date_of_birth'   => 'check_age',
            'birthday_day'    => 'required',
            'birthday_month'  => 'required',
            'birthday_year'   => 'required',
        ];

        $messages = [
            'required'                => __(':attribute is required.'),
            'birthday_day.required'   => __('Birth date field is required.'),
            'birthday_month.required' => __('Birth month field is required.'),
            'birthday_year.required'  => __('Birth year field is required.'),
        ];

        $fieldNames = [
            'first_name' => 'First name',
            'last_name'  => 'Last name',
            'email'      => 'Email',
            'password'   => 'Password',
        ];

        if (!empty(settings('recaptcha_preference')) && !empty(settings('recaptcha_key'))) {
            if (str_contains(settings('recaptcha_preference'), 'user_reg')) {
                $captchaRule = ['g-recaptcha-response' => ['required', new GoogleReCaptcha]];
                $captchaMessage = ['g-recaptcha-response.required' => __('The google recaptcha is required.')];
                $captchaFieldname = ['g-recaptcha-response' => 'Google reCaptcha'];

                $rules = array_merge($rules, $captchaRule);
                $messages = array_merge($messages, $captchaMessage);
                $fieldNames = array_merge($fieldNames, $captchaFieldname);
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = new User;
        $user->first_name      = strip_tags($request->first_name);
        $user->last_name       = strip_tags($request->last_name);
        $user->email           = $request->email;
        $user->password        = bcrypt($request->password);
        $user->status          = 'Active';
        $formattedPhone        = str_replace('+' . $request->carrier_code, "", $request->formatted_phone);
        $user->phone           = !empty($request->phone) ? preg_replace("/[\s-]+/", "", $formattedPhone) : null;
        $user->default_country = isset($request->default_country) ? $request->default_country : null;
        $user->carrier_code    = isset($request->carrier_code) ? $request->carrier_code : null;
        $user->formatted_phone = isset($request->formatted_phone) ? $request->formatted_phone : null;
        $user->save();

        $user_details          = new UserDetails;
        $user_details->user_id = $user->id;
        $user_details->field   = 'date_of_birth';
        $user_details->value   = $request->birthday_year . '-' . $request->birthday_month . '-' . $request->birthday_day;
        $user_details->save();

        $user_verification          = new UsersVerification;
        $user_verification->user_id = $user->id;
        $user_verification->email   = 'no';
        $user_verification->save();

        $this->wallet($user->id);

        $errorMessage = '';
        try {
            $email_controller->welcome_email($user);
        } catch (\Exception $e) {
            Log::error("RedaUsuarioController: Error al enviar correo de bienvenida: " . $e->getMessage());
            $errorMessage = ' ' . __('Email was not sent due to :x', ['x' => $e->getMessage()]);
        }

        $mensajeRegistro = __('Se ha registrado exitosamente. Se ha enviado un correo de verificación a :email. Por favor confirme su correo antes de iniciar sesión.', ['email' => $user->email]);
        if (!empty($errorMessage)) {
            $mensajeRegistro .= ' ' . $errorMessage;
        }

        $this->helper->one_time_message('success', $mensajeRegistro);

        return redirect('login')->with('correo_registrado_pendiente', $user->email);
    }

    /**
     * Procesa la confirmación de la dirección de correo electrónico mediante el token
     * recibido desde el enlace del email, sin requerir sesión activa previa.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmEmail(Request $request)
    {
        $codigoToken = $request->code;
        $password_resets = PasswordResets::whereToken($codigoToken);

        if ($password_resets->count()) {
            $password_result = $password_resets->first();
            $datetime1 = new DateTime();
            $datetime2 = new DateTime($password_result->created_at);
            $interval  = $datetime1->diff($datetime2);
            $hours     = $interval->format('%h');
            $days      = $interval->format('%a');

            if ($days > 0 || $hours >= 24) {
                $password_resets->delete();
                $this->helper->one_time_message('danger', __('El enlace de confirmación ha expirado. Por favor intente iniciar sesión para solicitar uno nuevo.'));
                return redirect('login');
            }

            $user = User::whereEmail($password_result->email)->first();
            if ($user) {
                $user->status = "Active";
                $user->save();

                $user_verification = UsersVerification::where('user_id', $user->id)->first();
                if (!$user_verification) {
                    $user_verification = new UsersVerification;
                    $user_verification->user_id = $user->id;
                }
                $user_verification->email = 'yes';
                $user_verification->save();

                $password_resets->delete();

                return redirect('login')
                    ->with('correo_confirmado_exitoso', true)
                    ->with('email_confirmado', $user->email);
            }
        }

        $this->helper->one_time_message('danger', __('Enlace de confirmación inválido o ya utilizado.'));
        return redirect('login');
    }
}
