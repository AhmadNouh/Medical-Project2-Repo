<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        $token = $user->createToken('user_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function registerUser(array $data)
    {
        $user = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'user_type'  => $data['user_type'],
            'department' => $data['department'], 
        ]);

        if (in_array($data['user_type'], ['employee' , 'owner' , 'delivery' , 'doctor' , 'manager'])) {
            $user->assignRole($data['user_type']);
        }
 
        return $user;
    }
}