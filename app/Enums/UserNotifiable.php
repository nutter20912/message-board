<?php

namespace App\Enums;

use App\Models\UserLoginRecord;

enum UserNotifiable: string
{
    case UserLoginRecord = UserLoginRecord::class;
}

