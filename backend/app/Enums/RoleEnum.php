<?php

namespace App\Enums;

enum RoleEnum: string
{
    case GUEST = 'guest';
    case LISTENER = 'listener';
    case ARTIST = 'artist';
    case ADMIN = 'admin';
}
