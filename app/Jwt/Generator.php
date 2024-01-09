<?php
namespace App\Jwt;

use App\Contracts\{JwtGeneratorInterface, JwtTokenInterface, JwtSubjectInterface};
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class Generator implements JwtGeneratorInterface
{
    public static function signature(JwtTokenInterface $token): string
    {
        $secret = config('app.key');

        if ($secret === null) {
            throw new InvalidArgumentException('No APP_KEY specified.');
        }

        $encodedData = self::encodeData($token);

        return hash_hmac('sha256', $encodedData, $secret);
    }

    public static function token($user): string
    {
        $now = Carbon::now();
        $expiresIn = (int) config('jwt.expiration',0);
        $expiresAt = $now->addSeconds($expiresIn);

        $token = Builder::build()
            ->subject($user)
            ->issuedAt($now->getTimestamp())
            ->expiresAt($expiresAt->getTimestamp())
            ->getToken();

        $parts = [
            self::encodeData($token),
            base64_encode(self::signature($token)),
        ];

        return implode('.', $parts);
    }

    private static function encodeData(JwtTokenInterface $token): string
    {
        $jsonParts = [
            $token->headers()->toJson(),
            $token->claims()->toJson(),
        ];

        $encoded = array_map('base64_encode', $jsonParts);

        return implode('.', $encoded);
    }
}
