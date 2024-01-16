<?php

namespace Tests\Feature\Api\User;

use App\Models\{User};
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateUserTest extends TestCase 
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->user = $user;
    }

    public function testUpdateUser(): void 
    {
        $this->assertNotEquals($username = 'new.username', $this->user->username);
        $this->assertNotEquals($email = "newEmail@example.com", $this->user->email);
        $this->assertNotEquals($bio = "New bio Information.", $this->user->bio);
        $this->assertNotEquals($image = 'https://example.com/image.png', $this->user->image);

        $this->actingAs($this->user, 'api')
            ->putJson("/api/user", ['user' => ['username' => $username]])
            ->assertOk();

        $this->actingAs($this->user, "api")
            ->putJson("/api/user", ["user" => ["email" => $email]])
            ->assertOk();
        
        $this->actingAs($this->user, "api")
            ->putJson("/api/user", ["user" => ["bio" => $bio]])
            ->assertOk();

        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/user", ["user" => ["image" => $image]]);

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('user', fn(AssertableJson $item) => 
                    $item->whereType('token', 'string')
                        ->whereAll([
                            'username' => $username,
                            'email' => $email,
                            'bio' => $bio,
                            'image' => $image,
                        ])->etc()
                )
        );
        
    }

    public function testUpdateUserValidationUnique(): void 
    {
        $annoterUser = User::factory()->create();

        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/user", [
                "user" => [
                    "username" => $annoterUser->username,
                    "email" => $annoterUser->email,
                ]
                ]);

        $response->assertUnprocessable()
            ->assertInvalid(['username', 'email']);
    }

    public function testSelfUpdateUserValidationUnique(): void 
    {
        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/user", [
                "user" => [
                    "username" => $this->user->username,
                    "email" => $this->user->email,
                ],
            ]);

        $response->assertOk();
    }

    public function testUpdateUserSetNull(): void 
    {
        $user = User::factory()
            ->state([
                'bio' => 'not-null',
                'image' => 'https://example.com/image.png',
            ])->create();

        $response = $this->actingAs($user, 'api')
            ->putJson("/api/user", [
                "user" => [
                    "bio" => null,
                    "image" => null,
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath("user.bio", null)
            ->assertJsonPath("user.image", null);

    }

    public function testUpdateUserWithoutAuth(): void 
    {
        $this->putJson("/api/user")->assertUnauthorized();
    }
}