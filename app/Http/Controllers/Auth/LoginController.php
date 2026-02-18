<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\CartManagerController;
use App\Mail\SendOtpMail;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\UserSession;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; 
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/panel';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $seoSettings = getSeoMetas('login');
        $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('site.login_page_title');
        $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('site.login_page_title');
        $pageRobot = getPageRobot('login');

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
        ];

        return view(getTemplate() . '.auth.login', $data);
    }

public function login(Request $request)
{
    try {
        $rules = [
            'email' => 'required|email|exists:users,email',
            'password' => 'required_if:otp,null|min:6',
        ];

        if (!empty(getGeneralSecuritySettings('captcha_for_login')) && !$request->has('otp')) {
            $rules['captcha'] = 'required|captcha';
        }

        $this->validate($request, $rules);

        // If OTP is present, verify it
        if ($request->has('otp')) {
            return $this->verifyOtp($request);
        }

        // Verify email and password before sending OTP
        $credentials = $request->only('email', 'password');
        
        if (!Auth::validate($credentials)) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => trans('auth.failed'), // "These credentials do not match our records."
            ]);
        }

        // Credentials are correct, proceed to send OTP
        return $this->sendOtp($request);

    } catch (\Illuminate\Validation\ValidationException $e) {
        throw $e; // Let Laravel handle validation errors normally
        
    } catch (\Exception $e) {
        Log::error('Login error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->withInput()->withErrors([
            'email' => trans('auth.login_system_error')
        ]);
    }
}

private function sendOtp(Request $request)
{
    try {
        // No need to validate credentials again - already done in login()
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput()->withErrors([
                'email' => trans('auth.failed')
            ]);
        }

        // Check if OTP was recently sent
        $otpData = session("otp:{$user->id}");
        if ($otpData && now()->lt($otpData['expires_at']->subMinutes(15))) {
            return back()->withInput()->withErrors([
                'email' => trans('auth.otp_already_sent')
            ]);
        }

        // Generate secure OTP
        $otp = random_int(100000, 999999);
        
        // Store in session
        session([
            "otp:{$user->id}" => [
                'hashed_otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(15)
            ]
        ]);

        // Try to send email
        try {
            Mail::to($user->email)->queue(new SendOtpMail([
                'otp' => $otp,
                'email' => $user->email
            ]));

            Log::info('OTP sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

        } catch (\Swift_TransportException $e) {
            Log::error('SMTP transport error sending OTP', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            session()->forget("otp:{$user->id}");

            return back()->withInput()->withErrors([
                'email' => trans('auth.email_service_error')
            ]);

        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            Log::error('Mailer transport error sending OTP', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            session()->forget("otp:{$user->id}");

            return back()->withInput()->withErrors([
                'email' => trans('auth.email_service_error')
            ]);

        } catch (\Exception $e) {
            Log::error('Unexpected error sending OTP email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->forget("otp:{$user->id}");

            return back()->withInput()->withErrors([
                'email' => trans('auth.otp_send_failed')
            ]);
        }

        return view('web.default.auth.otp-verify', [
            'email' => $request->email,
            'remember' => $request->boolean('remember')
        ]);

    } catch (\Exception $e) {
        Log::error('Send OTP process error', [
            'email' => $request->email,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        if (isset($user)) {
            session()->forget("otp:{$user->id}");
        }

        return back()->withInput()->withErrors([
            'email' => trans('auth.otp_process_error')
        ]);
    }
}

private function verifyOtp(Request $request)
{
    try {
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withInput()->withErrors([
                'email' => trans('auth.user_not_found')
            ]);
        }

        // Check rate limiting FIRST
        $cacheKey = "otp_attempts:{$user->id}";
        $lockoutKey = "otp_lockout:{$user->id}";
        
        // Check if user is locked out
        if (Cache::has($lockoutKey)) {
            $lockoutTime = Cache::get($lockoutKey);
            $remainingMinutes = now()->diffInMinutes($lockoutTime, false);
            
            return view('web.default.auth.otp-verify', [
                'email' => $request->email,
                'remember' => $request->boolean('remember'),
                'locked_out' => true,
                'remaining_minutes' => abs($remainingMinutes)
            ])->withErrors([
                'otp' => trans('auth.too_many_attempts')
            ]);
        }

        // Get OTP data from session
        $otpData = session("otp:{$user->id}");
        
        if (!$otpData || now()->gt($otpData['expires_at'])) {
            return view('web.default.auth.otp-verify', [
                'email' => $request->email,
                'remember' => $request->boolean('remember'),
                'expired' => true
            ])->withErrors([
                'otp' => trans('auth.invalid_or_expired_otp')
            ]);
        }

        // Get current attempts
        $attempts = Cache::get($cacheKey, 0);
        
        // Verify OTP
        if (!Hash::check($request->otp, $otpData['hashed_otp'])) {
            $attempts++;
            
            // Lock out after 5 attempts
            if ($attempts >= 5) {
                Cache::put($lockoutKey, now()->addMinutes(5), now()->addMinutes(5));
                Cache::forget($cacheKey);
                
                return view('web.default.auth.otp-verify', [
                    'email' => $request->email,
                    'remember' => $request->boolean('remember'),
                    'locked_out' => true,
                    'remaining_minutes' => 5
                ])->withErrors([
                    'otp' => trans('auth.too_many_attempts')
                ]);
            }
            
            // Increment attempts
            Cache::put($cacheKey, $attempts, now()->addMinutes(5));
            
            return view('web.default.auth.otp-verify', [
                'email' => $request->email,
                'remember' => $request->boolean('remember'),
                'remaining_attempts' => 5 - $attempts
            ])->withErrors([
                'otp' => trans('auth.invalid_otp_attempts', ['remaining' => 5 - $attempts])
            ]);
        }

        // Success - clear session and cache
        session()->forget("otp:{$user->id}");
        Cache::forget($cacheKey);
        Cache::forget($lockoutKey);
        
        Auth::login($user, $request->boolean('remember'));
        
        Log::info('User logged in with OTP', ['user_id' => $user->id]);

        return $this->afterLogged($request);

    } catch (\Exception $e) {
        Log::error('OTP verification error', [
            'email' => $request->email,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return view('web.default.auth.otp-verify', [
            'email' => $request->email,
            'remember' => $request->boolean('remember')
        ])->withErrors([
            'otp' => trans('auth.otp_verification_error')
        ]);
    }
}

    public function logout(Request $request)
    {
        $user = auth()->user();

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if (!empty($user) and $user->logged_count > 0) {

            $user->update([
                'logged_count' => $user->logged_count - 1
            ]);
        }

        return redirect('/');
    }

    public function username()
    {
        $email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";

        if (empty($this->username)) {
            $this->username = 'mobile';
            if (preg_match($email_regex, request('username', null))) {
                $this->username = 'email';
            }
        }
        return $this->username;
    }

    protected function getUsername(Request $request)
    {
        $type = $request->get('type');

        if ($type == 'mobile') {
            return 'mobile';
        } else {
            return 'email';
        }
    }

    protected function getUsernameValue(Request $request)
    {
        $type = $request->get('type');
        $data = $request->all();

        if ($type == 'mobile') {
            return ltrim($data['country_code'], '+') . ltrim($data['mobile'], '0');
        } else {
            return $request->get('email');
        }
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = [
            $this->getUsername($request) => $this->getUsernameValue($request),
            'password' => $request->get('password')
        ];
        $remember = true;

        /*if (!empty($request->get('remember')) and $request->get('remember') == true) {
            $remember = true;
        }*/

        return $this->guard()->attempt($credentials, $remember);
    }

    public function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->getUsername($request) => [trans('validation.password_or_username')],
        ]);
    }

    protected function sendBanResponse(Request $request, $user)
    {
        throw ValidationException::withMessages([
            $this->getUsername($request) => [trans('auth.ban_msg', ['date' => dateTimeFormat($user->ban_end_at, 'j M Y')])],
        ]);
    }

    protected function sendNotActiveResponse($user)
    {
        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('auth.login_failed_your_account_is_not_verified'),
            'status' => 'error'
        ];

        return redirect('/login')->with(['toast' => $toastData]);
    }

    protected function sendMaximumActiveSessionResponse()
    {
        $toastData = [
            'title' => trans('update.login_failed'),
            'msg' => trans('update.device_limit_reached_please_try_again'),
            'status' => 'error'
        ];

        return redirect('/login')->with(['login_failed_active_session' => $toastData]);
    }

    public function afterLogged(Request $request, $verify = false)
    {
        $user = auth()->user();

        if ($user->ban) {
            $time = time();
            $endBan = $user->ban_end_at;
            if (!empty($endBan) and $endBan > $time) {
                $this->guard()->logout();
                $request->session()->flush();
                $request->session()->regenerate();

                return $this->sendBanResponse($request, $user);
            } elseif (!empty($endBan) and $endBan < $time) {
                $user->update([
                    'ban' => false,
                    'ban_start_at' => null,
                    'ban_end_at' => null,
                ]);
            }
        }

        if ($user->status != User::$active and !$verify) {
            $this->guard()->logout();
            $request->session()->flush();
            $request->session()->regenerate();

            $verificationController = new VerificationController();
            $checkConfirmed = $verificationController->checkConfirmed($user, $this->username(), $request->get('username'));

            if ($checkConfirmed['status'] == 'send') {
                return redirect('/verification');
            }
        } elseif ($verify) {
            session()->forget('verificationId');

            $user->update([
                'status' => User::$active,
            ]);

            $registerReward = RewardAccounting::calculateScore(Reward::REGISTER);
            RewardAccounting::makeRewardAccounting($user->id, $registerReward, Reward::REGISTER, $user->id, true);
        }

        if ($user->status != User::$active) {
            $this->guard()->logout();
            $request->session()->flush();
            $request->session()->regenerate();

            return $this->sendNotActiveResponse($user);
        }

        $checkLoginDeviceLimit = $this->checkLoginDeviceLimit($user);
        if ($checkLoginDeviceLimit != "ok") {
            $this->guard()->logout();
            $request->session()->flush();
            $request->session()->regenerate();

            return $this->sendMaximumActiveSessionResponse();
        }

        $user->update([
            'logged_count' => (int)$user->logged_count + 1
        ]);

        $cartManagerController = new CartManagerController();
        $cartManagerController->storeCookieCartsToDB();

        if ($user->isAdmin()) {
            return redirect(getAdminPanelUrl() . '');
        } else {
            return redirect('/panel');
        }
    }

    private function checkLoginDeviceLimit($user)
    {
        $securitySettings = getGeneralSecuritySettings();

        if (!empty($securitySettings) and !empty($securitySettings['login_device_limit'])) {
            $limitCount = !empty($securitySettings['number_of_allowed_devices']) ? $securitySettings['number_of_allowed_devices'] : 1;

            $count = $user->logged_count;

            if ($count >= $limitCount) {
                return "no";
            }
        }

        return 'ok';
    }
}
