<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordOtpMail;
use App\Services\AuthService;
use App\Services\ResetPasswordService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $authService;
    protected $resetPass;

    public function __construct(AuthService $authService , ResetPasswordService $resetPass)
    {
        $this->authService = $authService;
        $this->resetPass = $resetPass;
    }

    // Auth Functions
    public function loginByEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $result = $this->authService->loginByEmail($request->only('email', 'password'));

        if (!$result) {
            return $this->unauthorizedResponse('بيانات الدخول التي أدخلتها غير صحيحة');
        }

        return $this->successResponse($result, 'تم تسجيل الدخول بنجاح');
    }

    public function loginByPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'    => ['required','string', 'regex:/^\+[1-9]\d{1,14}$/'],
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $result = $this->authService->loginByPhone($request->only('phone', 'password'));

        if (!$result) {
            return $this->unauthorizedResponse('بيانات الدخول التي أدخلتها غير صحيحة');
        }

        return $this->successResponse($result, 'تم تسجيل الدخول بنجاح');
    }


    public function createAccountByAdmin(Request $request)
    {
        if (!Auth::check()  || Auth::user()->user_type !== 'owner') {
            return $this->errorResponse('عذراً، لا تمتلك الصلاحية لإنشاء حسابات هنا', 403);
        }

        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|unique:users',
            'password'   => 'required|string|min:6',
            'user_type'  => 'required|string|in:employee,owner,delivery,doctor,manager,technician',
            'department' => 'required|string|in:management,delivery,maintenance,admin',
            'phone'      => ['required','string', 'regex:/^\+[1-9]\d{1,14}$/', 'unique:users,phone']
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $newUser = $this->authService->registerUser($validator->validated());

        return $this->successResponse($newUser, 'تم إنشاء حساب المستخدم بنجاح ', 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
    }

    // Reset Password Functions

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $code = $this->resetPass->sendResetOtp($request->email);

        if (!$code) {
            return $this->notFoundResponse('البريد غير موجود');
        }

        return $this->successResponse(
            ['otp_code' => $code], 
            'تم إرسال كود التحقق إلى بريدك الإلكتروني بنجاح (صالح لمدة 15 دقيقة)'
        );
    }
  

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|numeric'
        ]);

        $isValid = $this->resetPass->verifyOtp($request->email, $request->code);

        if (!$isValid) {
            return $this->errorResponse('كود التحقق غير صحيح أو انتهت صلاحيته', 422);
        }

        return $this->successResponse(null, 'الكود صحيح، يمكنك الآن إعادة تعيين كلمة المرور');
    }


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'code'     => 'required|numeric',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $isUpdated = $this->resetPass->resetPassword($request->all());

        if (!$isUpdated) {
            return $this->errorResponse('فشلت العملية، كود التحقق غير صالح أو انتهت مدته', 422);
        }

        return $this->successResponse(null, 'تم تغيير كلمة المرور بنجاح، يمكنك الآن تسجيل الدخول بها');
    }

}