<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $result = $this->authService->login($request->only('email', 'password'));

        if (!$result) {
            return $this->unauthorizedResponse('بيانات الدخول التي أدخلتها غير صحيحة');
        }

        return $this->successResponse($result, 'تم تسجيل الدخول بنجاح');
    }

    // إنشاء حسابات للموظفين والمناديب والفنيين (يستخدمه صاحب العمل فقط من لوحة التحكم)
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
            'department' => 'required|string|in:medical,management,delivery,maintenance,admin'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $newUser = $this->authService->registerUser($request->all());

        return $this->successResponse($newUser, 'تم إنشاء حساب المستخدم بنجاح ', 201);
    }

    public function registerDoctor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $data = $request->all();
        $data['user_type'] = 'doctor';
        $data['department'] = 'none';

        $doctor = $this->authService->registerUser($data);

        return $this->successResponse($doctor, 'تم تسجيل حسابك كطبيب بنجاح', 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
    }
}