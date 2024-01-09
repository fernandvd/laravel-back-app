<?php

namespace App\Contracts;

interface JwtGeneratorInterface 
{
    public static function signature(JwtTokenInterface $token): string;

    public static function token(JwtSubjectInterface $user): string;
}