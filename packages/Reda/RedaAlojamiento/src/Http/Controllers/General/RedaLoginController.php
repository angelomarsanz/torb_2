<?php

/**
 * Controlador RedaLoginController
 *
 * Propósito: Gestiona y adapta la autenticación de usuarios para exigir la verificación
 * previa del correo electrónico antes de permitir el acceso al sistema, sin modificar los
 * controladores originales de la aplicación base.
 *
 * Responsabilidades:
 * - Sobrescribe la acción `authenticate` para comprobar las credenciales del usuario
 *   y validar si su correo se encuentra confirmado en `users_verification` (email = 'yes').
 * - Si las credenciales son correctas pero el correo no ha sido verificado, bloquea el inicio
 *   de sesión y redirige a la vista de login con la variable flash `correo_no_verificado`
 *   para desplegar el modal interactivo de aviso y corrección.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Http\Controllers\General
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Http\Controllers\General;

use App\Http\Controllers\LoginController;
use App\Models\{Settings, User, UsersVerification};
use App\Rules\GoogleReCaptcha;
use Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RedaLoginController extends LoginController
{
    /**
     * Autentica al usuario en el sistema validando credenciales y la confirmación obligatoria
     * de su dirección de correo electrónico.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function authenticate(Request $request)
    {
        $rules = [
            'email'    => 'required|email|max:200',
            'password' => 'required',
        ];

        $fieldNames = [
            'email'    => 'Email',
            'password' => 'Password',
        ];

        if (!empty(settings('recaptcha_preference')) && !empty(settings('recaptcha_key'))) {
            if (str_contains(settings('recaptcha_preference'), 'user_login')) {
                $captchaRule = ['g-recaptcha-response' => ['required', new GoogleReCaptcha]];
                $captchaFieldname = ['g-recaptcha-response' => 'Google reCaptcha'];

                $rules = array_merge($rules, $captchaRule);
                $fieldNames = array_merge($fieldNames, $captchaFieldname);
            }
        }

        $remember = ($request->remember_me) ? true : false;

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (n_as_k_c()) {
            Session::flush();
            return view('vendor.installer.errors.user');
        }

        $user = User::where('email', $request->email)->first();

        if (!empty($user)) {
            if ($user->status != 'Inactive') {
                if (Hash::check($request->password, $user->password)) {
                    // Verificación de correo en users_verification
                    $verificacion = UsersVerification::where('user_id', $user->id)->first();
                    $estaVerificado = ($verificacion && strtolower($verificacion->email) === 'yes');

                    if (!$estaVerificado) {
                        return redirect('login')
                            ->withInput($request->only('email'))
                            ->with('correo_no_verificado', $user->email);
                    }

                    if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
                        self::addFavourite();
                        return redirect()->intended('dashboard');
                    } else {
                        Common::one_time_message('error', __('Unable to login with provided information.'));
                        return redirect('login');
                    }
                } else {
                    Common::one_time_message('error', __('Unable to login with provided information.'));
                    return redirect('login');
                }
            } elseif ($user->status == 'Inactive') {
                Common::one_time_message('error', __("User is inactive. Please try agin!"));
                return redirect('login');
            } else {
                Common::one_time_message('error', __('Unable to login with provided information.'));
                return redirect('login');
            }
        } else {
            Common::one_time_message('error', __('There isn’t an account associated with this email address.'));
            return redirect('login');
        }
    }
}
