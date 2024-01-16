<?php

namespace Tests\Feature\Api\Auth;


use App\Jwt;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginTest extends TestCase 
{
    use WithFaker;

    public function testLoginUser(): void 
    {
        $password = $this->faker->password(8);

        $user = User::factory()->state(['password' => Hash::make($password)])->create();

        $response = $this->postJson("/api/users/login", [
            "user" => [
                "email" => $user->email,
                "password" => $password,
            ],
        ]);

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('user', fn (AssertableJson $item) => 
                    $item->whereType('token', 'string')
                        ->whereAll([
                            'username' => $user->username,
                            'email' => $user->email,
                            'bio' => $user->bio,
                            'image' => $user->image,
                            'roles' => [],
                        ])
                )
        );

        $token = Jwt\Parser::parse($response['user']['token']);
        $this->assertTrue(Jwt\Validator::validate($token));
    }

    public function testLoginUserFail(): void 
    {
        $password = 'knownPassword';

        $user = User::factory()->state(['password' => Hash::make($password)])->create();

        $response = $this->postJson("/api/users/login", [
            'user' => [
                'email' => $user->email,
                "password" => 'differentPassword',
            ],
        ]);

        $response->assertBadRequest()->assertInvalid('user');
    }

}

