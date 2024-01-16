<?php

namespace App\Enums;

enum RolEnum: string {
    case ADMIN = 'ADMIN';
    case EDITOR = 'EDITOR';
    case CLIENT = 'CLIENT';

    public function label(): string 
    {
        return match ($this) {
            static::ADMIN => 'Adminstrators',
            static::EDITOR => 'Editors',
            static::CLIENT => 'Clients',
        };
    }
}
