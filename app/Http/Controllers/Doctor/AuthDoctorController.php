<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterDoctorRequest;
use App\Services\AuthDoctorService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthDoctorController extends Controller
{
    use ApiResponseTrait;

    protected $authDocService;

    public function __construct(AuthDoctorService $authDocService)
    {
        $this->authDocService = $authDocService;
    }

    public function loginDoctorByEmail(Request $request){
        try {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $result = $this->authDocService->loginDoctorByEmail($request->only('email', 'password'));

        if ($result['status'] === 'invalid_credentails' ) {
            return $this->unauthorizedResponse('بيانات الدخول التي أدخلتها غير صحيحة');
        }

        if($result['status'] === 'account_not_found'){
            return $this->errorResponse('أنت لا تمتلك حساب طبيب أو صيدلية ، رجاءً انشأ حساب ثم أعد المحاولة' , 404);
        }

        if ($result['status'] === 'not_active'){
            return $this->errorResponse('حسابك لم يتم تفعيله بعد أو تم تعليقه' , 403);
        }

        return $this->successResponse($result, 'تم تسجيل الدخول بنجاح');

        }
       
        catch (\Exception $e) {

            return $this->errorResponse('حدث خطأ ما أثناء تسجيل الحساب، يرجى المحاولة لاحقاً' , 500 , $e->getMessage());
        }
    }

    public function loginDoctorByPhone(Request $request){
        try {
        $validator = Validator::make($request->all(), [
            'phone'    => ['required','string', 'regex:/^\+[1-9]\d{1,14}$/'],
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $result = $this->authDocService->loginDoctorByPhone($request->only('phone', 'password'));

        if ($result['status'] === 'invalid_credentails' ) {
            return $this->unauthorizedResponse('بيانات الدخول التي أدخلتها غير صحيحة');
        }

        if($result['status'] === 'account_not_found'){
            return $this->errorResponse('أنت لا تمتلك حساب طبيب أو صيدلية، رجاءً انشأ حساب ثم أعد المحاولة' , 404);
        }

        if ($result['status'] === 'not_active'){
            return $this->errorResponse('حسابك لم يتم تفعيله بعد أو تم تعليقه' , 403);
        }

        return $this->successResponse($result, 'تم تسجيل الدخول بنجاح');

        }
       
        catch (\Exception $e) {

            return $this->errorResponse('حدث خطأ ما أثناء تسجيل الحساب، يرجى المحاولة لاحقاً' , 500 , $e->getMessage());
        }
    }

    public function registerDoctor(RegisterDoctorRequest $request)
    {
        try {

            $result = $this->authDocService->registerDoctor($request->validated());

            return $this->successResponse($result , 'تم تسجيل الحساب بنجاح وهو بانتظار التفعيل' , 201);
            
        } catch (\Exception $e) {

            return $this->errorResponse('حدث خطأ ما أثناء تسجيل الحساب، يرجى المحاولة لاحقاً' , 500 , $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
    }
}
