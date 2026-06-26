<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDoctorAccountStatusRequest;
use App\Models\Doctor;
use App\Traits\ApiResponseTrait;

class ManageDoctorController extends Controller
{
    use ApiResponseTrait;

    public function updateDoctorAccountStatus(UpdateDoctorAccountStatusRequest $request , Doctor $doctor){

        $doctor->update([
            'status' => $request->validated()['status']
        ]);

        $doctor->load('user');

        return $this->successResponse(
            [
                'id' => $doctor->user_id,
                'name' => $doctor->user->name,
                'status' => $doctor->status->value
            ] ,
            "Doctor status updated successfully to: {$doctor->status->value}"   
        );

    }
}
