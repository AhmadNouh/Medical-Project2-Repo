<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRegistryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $roleName = $this->getRoleNames()->first() ?? 'N/A';

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone ?? 'لا يوجد',
            'role'       => $roleName,
            
            'status'     => $roleName === 'doctor' ? ($this->doctorProfile?->status ?? 'pending') : ($this->status ?? 'active'),

            // إضافة بيانات مخصصة ديناميكياً بحسب نوع المستخدم إذا كانت العلاقات محملة
            // 'extra_info' => $this->when($roleName === 'doctor' && $this->relationLoaded('doctor'), function() {
            //     return [
            //         'specialty'      => $this->doctor?->specialty,
            //         'clinic_address' => $this->doctor?->clinic_address,
            //     ];
            // }),
            
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}