<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

//#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens , HasFactory, Notifiable , HasRoles;

    protected $fillable = ['name', 'email', 'password' , 'user_type' , 'department' , 'phone'];
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Filter using Scope
    public function scopeFilter(Builder $query , array $filters) : Builder {

    // 1. الفلترة بحسب الدور
    if(!empty($filters['role'])){
        $query->role($filters['role']);
    }

    // 2. البحث النصي الذكي
    if(!empty($filters['search'])){
        $search = $filters['search'];
        $query->where(function(Builder $q) use ($search){
            $q->where('name' , 'like' , "%{$search}%")
              ->orWhere('email' , 'like' , "%{$search}%")
              ->orWhere('phone' , 'like' , "%{$search}%");
        }); 
    }

    // 3. الفلترة بحسب الحالة (خارج شرط الـ search ليعمل بشكل مستقل)
    if(!empty($filters['status'])){
        $status = $filters['status'];
        $role = $filters['role'] ?? null; // استخدام ?? null لتفادي الأخطاء

        if($role == 'doctor'){
            //  تم استخدام doctorProfile المكتوبة في الأسفل
            $query->whereHas('doctorProfile' , function(Builder $q) use ($status){
                $q->where('status' , $status); 
            });
        }
    }

    return $query;
}
    // relationship 

    // with doctors table 
    public function doctorProfile()
    {
        return $this->hasOne(Doctor::class, 'user_id'); 
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
