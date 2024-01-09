<?php

namespace App\Contracts;

interface JwtParserInterface
{
    public static function parse(string $token): JwtTokenInterface;
}