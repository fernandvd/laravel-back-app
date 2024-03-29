<?php

namespace Tests\Feature\Api\Profile;

use App\Models\{User};
use Tests\TestCase;

class FollowProfileTest extends TestCase 
{

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->user = $user;
    }

    public function testFollowProfile(): void 
    {
        $follower = User::factory()->create();

        $response = $this->actingAs($follower, 'api')
            ->postJson("/api/profiles/{$this->user->username}/follow");

        $response->assertOk()
            ->assertJsonPath("profile.following", true);

        $this->assertTrue($this->user->followers->contains($follower));

        $this->actingAs($follower, "api")
            ->postJson("/api/profiles/{$this->user->username}/follow")->assertOk();

        $this->assertDatabaseCount("author_follower", 1);
    }

    public function testFollowProfileWithoutAuth(): void 
    {
        $this->postJson("/api/profiles/{$this->user->username}/follow")->assertUnauthorized();
    }

    public function testFollowNonExistentProfile(): void 
    {
        $this->actingAs($this->user, 'api')
            ->postJson("/api/profiles/non-existent/follow")->assertNotFound();
    }
}
