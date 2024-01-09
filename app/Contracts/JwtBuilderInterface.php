<?php

namespace App\Contracts;

interface JwtBuilderInterface
{
    /**
     * Start building JwtToken.
     * 
     * @return \App\Contracts\JwtBuilderInterface
     */
    public static function build(): JwtBuilderInterface;

    /**
     * Add issued at (iat) claim to the payload.
     * 
     * @param int $timestamp
     * @return \App\Contracts\JwtBuilderInterface
     */
    public function issuedAt(int $timestamp): JwtBuilderInterface;

    /**
     * add expires at (exp) claim to the payload
     * 
     * @param int $timestamp
     * @return \App\Contracts\JwtBuilderInterface
     */
    public function expiresAt(int $timestamp): JwtBuilderInterface;

    public function subject(mixed $identifier): JwtBuilderInterface;

    public function withClaim(string $key, mixed $value = null ): JwtBuilderInterface;

    public function withHeader(string $key, mixed $value = null ): JwtBuilderInterface;

    public function getToken(): JwtTokenInterface;
}

