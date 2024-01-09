<?php

namespace Tests\Feature\Api\Profile;

use App\Models\{User};
use Tests\TestCase;

class UnFollowProfileTest extends TestCase 
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->user = $user;
    }

    public function testUnfollowProfile(): void 
    {
        $follower = User::factory()
            ->hasAttached($this->user, [], 'authors')
            ->create();

        $response = $this->actingAs($follower, 'api')
            ->deleteJson("/api/profiles/{$this->user->username}/follow");
        $response->assertOk()
            ->assertJsonPath("profile.following", false);

        $this->assertTrue($this->user->followers->doesntContain($follower));

        $this->actingAs($follower, "api")
            ->deleteJson("/api/profiles/{$this->user->username}/follow")
            ->assertOk();
    }

    public function testUnfollowProfileWithoutAuth(): void 
    {
        $this->deleteJson("/api/profiles/{$this->user->username}/follow")
            ->assertUnauthorized();
    }

    public function testUnfollowProfileNonExistentProfile(): void 
    {
        $this->actingAs($this->user, 'api')
            ->deleteJson("/api/profiles/non-existent/follow")
            ->assertNotFound();
    }
}
