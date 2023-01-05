<?php

namespace App\Enums;

enum UserRelationshipType: string
{
    case STRANGER = '0';
    case FRIEND = '1';
}
