<?php

namespace App\Services;

use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthDoctorService
{
    public function loginDoctorByEmail(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return ['status' => 'invalid_credentails'];
        }

        if(in_array($user['user_type'], ['doctor' , 'pharmacist'])){
        
            $doctorProfile = $user->doctorProfile;

            if(!$doctorProfile){
                return ['status' => 'account_not_found'];
            }
            
            if($doctorProfile->status !== 'active'){
                return ['status' => 'not_active'];
            }

        }

        $token = $user->createToken('medical_token')->plainTextToken;

        return [
            'status' => 'success',
            'user' => $user,
            'token' => $token
        ];
    }

    public function registerDoctor(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            $user = User::create([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'password'   => Hash::make($data['password']),
                'user_type'  => $data['user_type'], // doctor أو pharmacist
                'department' => $data['department'], // مثل dental أو general_pharmacy
                'phone'      => $data['phone'],
            ]);

            $user->assignRole($data['user_type']);

            $user->doctorProfile()->create([
                'syndicate_number' => $data['syndicate_number'],
                'work_place_name'  => $data['work_place_name'], 
                'address'          => $data['address'],
                'landline_phone'   => $data['landline_phone'] ?? null,
                'status'           => 'pending', 
            ]);

            $token = $user->createToken('medical-token')->plainTextToken;

            return [
                'user'  => $user->load('doctorProfile'),
                'token' => $token
            ];
        });
    }

}