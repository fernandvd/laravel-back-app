<?php

namespace App\Contracts;

interface JwtSubjectInterface
{
    public function getJwtIdentifier(): mixed;
}