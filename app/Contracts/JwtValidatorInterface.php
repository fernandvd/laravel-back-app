<?php

namespace App\Contracts;

interface JwtValidatorInterface
{
    public static function validate(JwtTokenInterface $token): bool;
}