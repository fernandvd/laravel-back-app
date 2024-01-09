<?php

namespace Tests\Feature\Api\User;

use App\Models\{User};
use App\Jwt;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShowUserTest extends TestCase 
{
    public function testShowUser(): void 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, "api")
            ->getJson("/api/user");

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('user', fn (AssertableJson $item) =>
                    $item->whereType('token', 'string')
                        ->whereAll([
                            'username' => $user->username,
                            'email' => $user->email,
                            'bio' => $user->bio,
                            'image' => $user->image,
                        ])
                )
                        );
        $token = Jwt\Parser::parse($response['user']['token']);
        $this->assertTrue(Jwt\Validator::validate($token));
    }

    public function testShowUserWithoutAuth(): void 
    {
        $this->getJson("/api/user")->assertUnauthorized();
    }
}