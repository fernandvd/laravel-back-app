<?php

namespace Tests\Feature\Api\Auth;

use App\Jwt;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterTest extends TestCase 
{
    use WithFaker;

    public function testRegisterUser(): void 
    {
        $username = $this->faker->userName();
        $email = $this->faker->safeEmail();

        $response = $this->postJson("/api/users", [
            'user' => [
                'username' => $username,
                'email' => $email,
                'password' => $this->faker->password(8),
            ],
        ]);

        $response->assertCreated()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('user', fn (AssertableJson $item) => 
                    $item->whereType('token', 'string')
                        ->whereAll([
                            'username' => $username,
                            'email' => $email,
                            'bio' => null,
                            'image' => null,
                        ])
                )
                        );
        $token = Jwt\Parser::parse($response['user']['token']);

        $this->assertTrue(Jwt\Validator::validate($token));
    }

    public function testRegisterUserValidationUnique(): void 
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/users', [
            'user' => [
                'username' => $user->username,
                'email' => $user->email,
                'password' => $this->faker->password(8),
            ],
        ]);

        $response->assertUnprocessable()
            ->assertInvalid(['username', 'email']);
    }

}

