<?php

namespace Tests\Feature\Api\Auth;

use App\Jwt;

use App\Models\User;
use Tests\TestCase;

class JwtGuardTest extends TestCase 
{
    private User $user;
    private string $token;

    protected function setUp(): void 
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->user = $user;
        $this->token = Jwt\Generator::token($user);
    }

    public function testGuardTokenParse(): void 
    {
        $this->getJson("/api/user?token=string")
            ->assertUnauthorized();
    }

    public function testGuardTokenValidation(): void 
    {
        $this->user->delete();

        $this->getJson("/api/user?token=".$this->token)
            ->assertUnauthorized();

    }

    public function testGuardWithHeaderToken(): void 
    {
        $this->assertModelExists($this->user);
        $response = $this->getJson("/api/user", [
            'Authorization' => "Token ".$this->token,
        ]);

        $response->assertOk();
    }

    public function testGuardWithQueryToken(): void 
    {
        $this->assertModelExists($this->user);
        $this->getJson("/api/user?token={$this->token}")->assertOk();
    }

    public function testGuardWithJsonBodyToken(): void 
    {
        $this->assertModelExists($this->user);
        $response = $this->json('GET', '/api/user', [
            'token' => $this->token,
        ]);

        $response->assertOk();
    }
}

