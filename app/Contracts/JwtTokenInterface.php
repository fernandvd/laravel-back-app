<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface JwtTokenInterface
{
    public function getDefaultHeaders(): array;

    public function headers(): Collection;

    public function claims(): Collection;

    public function getUserSignature(): ?string;

    public function putToPayload(string $key, mixed $value): void;

    public function putToHeader(string $key, mixed $value): void;

    public function setUserSignature(string $signature): void;

    public function getSubject(): mixed;

    public function getExpiration(): int;

}