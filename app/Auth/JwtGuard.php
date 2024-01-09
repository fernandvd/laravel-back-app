<?php


namespace App\Auth;

use App\Contracts\JwtTokenInterface;
use App\Exceptions\JwtParseException;
use App\Jwt;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JsonException;

class JwtGuard implements Guard
{
    use GuardHelpers;

    protected Request $request;

    protected string $inputKey;

    public function __construct(
        UserProvider $provider,
        Request $request,
        string $inputKey = 'token',
    )
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->inputKey = $inputKey;
    }

    public function user() {
        if (!is_null($this->user)){
            return $this->user;
        }

        $user = null;

        $token = $this->getTokenForRequest();

        if (!empty($token) && is_string($token)) {
            try {
                $jwt = Jwt\Parser::parse($token);
            } catch (JwtParseException | JsonException) {
                $jwt = null;
            }

            if ($this->validate([$this->inputKey => $jwt])) {
                $user = $this->provider->retrieveById(
                    $jwt->getSubject()
                );
            }

        }
        return $this->user = $user;
    }

    public function validate(array $credentials = [])
    {
        if (empty($credentials[$this->inputKey])) {
            return false;
        }
        $token = $credentials[$this->inputKey];

        if (!$token instanceof JwtTokenInterface) {
            return false;
        }

        return Jwt\Validator::validate($token);
    }

    public function getTokenForRequest()
    {
        $token = $this->jwtToken();
        
        if (empty($token)) {
            $token = $this->request->query($this->inputKey);
        }

        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }
        return $token;
    }

    public function jwtToken(): ?string
    {
        $header = $this->request->header('Authorization', '');

        if (Str::startsWith($header, 'Token ')) {
            return Str::substr($header, 6);
        }
        return null;
    }
}