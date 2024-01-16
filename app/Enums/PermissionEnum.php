<?php

namespace App\Enums;

enum PermissionEnum: string {
    case UPDATE_ARTICLE = 'UPDATE-ARTICLE';
    case DELETE_ARTICLE = 'DELETE-ARTICLE';
}