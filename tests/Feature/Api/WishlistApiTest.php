<?php

namespace Tests\Feature\Api;

use App\Models\Perfume;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_authenticated_user_can_list_wishlists(): void
    {
        // Create wishlists with unique names
        Wishlist::factory()->create(['user_id' => $this->user->id, 'name' => 'Wishlist 1']);
        Wishlist::factory()->create(['user_id' => $this->user->id, 'name' => 'Wishlist 2']);
        Wishlist::factory()->create(['user_id' => $this->user->id, 'name' => 'Wishlist 3']);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/v1/wishlists');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_authenticated_user_can_create_wishlist(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/wishlists', [
                'name' => 'My Birthday List',
                'is_public' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'My Birthday List')
            ->assertJsonPath('data.is_public', true);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'name' => 'My Birthday List',
        ]);
    }

    public function test_authenticated_user_can_add_item_to_wishlist(): void
    {
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id, 'name' => 'Test List']);
        $perfume = Perfume::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/v1/wishlists/{$wishlist->id}/items", [
                'perfume_id' => $perfume->id,
                'notes' => 'Want this for my anniversary',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Added to wishlist');

        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'perfume_id' => $perfume->id,
        ]);
    }

    public function test_authenticated_user_can_remove_item_from_wishlist(): void
    {
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id, 'name' => 'Test List']);
        $perfume = Perfume::factory()->create();

        // Add item first
        $wishlist->items()->create(['perfume_id' => $perfume->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/v1/wishlists/{$wishlist->id}/items/{$perfume->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Removed from wishlist');

        $this->assertDatabaseMissing('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'perfume_id' => $perfume->id,
        ]);
    }

    public function test_toggle_adds_perfume_to_default_wishlist(): void
    {
        $perfume = Perfume::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/wishlist/toggle', [
                'perfume_id' => $perfume->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('in_wishlist', true);
    }

    public function test_toggle_removes_perfume_when_already_in_wishlist(): void
    {
        $perfume = Perfume::factory()->create();

        // First toggle to add
        $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/wishlist/toggle', ['perfume_id' => $perfume->id]);

        // Second toggle to remove
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/wishlist/toggle', [
                'perfume_id' => $perfume->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('in_wishlist', false);
    }

    public function test_check_returns_wishlist_status(): void
    {
        $perfume = Perfume::factory()->create();

        // Add using toggle (creates default wishlist)
        $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/wishlist/toggle', ['perfume_id' => $perfume->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/v1/wishlist/check?perfume_id={$perfume->id}");

        $response->assertStatus(200)
            ->assertJsonPath('in_wishlist', true);
    }

    public function test_unauthenticated_user_cannot_access_wishlists(): void
    {
        $response = $this->getJson('/api/v1/wishlists');

        $response->assertStatus(401);
    }

    public function test_user_cannot_modify_other_users_wishlist(): void
    {
        $otherUser = User::factory()->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $otherUser->id, 'name' => 'Private List']);
        $perfume = Perfume::factory()->create();

        // Try to add item to other user's wishlist
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/v1/wishlists/{$wishlist->id}/items", [
                'perfume_id' => $perfume->id,
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_wishlist(): void
    {
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id, 'name' => 'Delete Me']);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/v1/wishlists/{$wishlist->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('wishlists', ['id' => $wishlist->id]);
    }
}
