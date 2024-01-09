<?php

namespace App\Jwt;

use App\Contracts\{JwtTokenInterface, JwtParserInterface};
use App\Exceptions\JwtParseException;


class Parser implements JwtParserInterface
{
    public static function parse(string $token): JwtTokenInterface
    {
        $parts = explode('.', $token);

        if (count($parts)!==3) {
            throw new JwtParseException('JwtToken parts count dows not match.');
        }

        $base64Decoded = array_map(function ($part) {
            $decoded = base64_decode($part, true);

            if ($decoded ===false) {
                throw new JwtParseException("JwtToken parts base 64 decode error.");
            }

            return $decoded;
        }, $parts);

        [$jsonHeader, $jsonPayload, $signature] = $base64Decoded;

        $jsonDecoded = array_map(fn (string $part) => 
        json_decode($part, true, 512, JSON_THROW_ON_ERROR), [$jsonHeader, $jsonPayload]);

        [$header, $payload] = $jsonDecoded;

        return new Token($header, $payload, $signature);
    }
}
