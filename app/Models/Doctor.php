<?php

namespace App\Models;

use App\Enums\DoctorAccountStatus;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = ['user_id' , 'syndicate_number' , 'work_place_name' , 'address' , 'landline_phone' , 'status'];

    protected $casts = [
        'status' => DoctorAccountStatus::class, // give me the status with enum where casts convert from strings and integers of real data types such arrays or enums or datetime 
    ];

    
    // relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
