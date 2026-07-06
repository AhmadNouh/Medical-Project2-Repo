<?php

namespace App\Enums;

enum DoctorAccountStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SUSPEND = 'suspended';
}