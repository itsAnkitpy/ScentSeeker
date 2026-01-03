<?php

namespace Tests\Feature\Api;

use App\Models\Perfume;
use App\Models\Price;
use App\Models\PriceAlert;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceAlertApiTest extends TestCase
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

    public function test_authenticated_user_can_list_price_alerts(): void
    {
        PriceAlert::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/v1/price-alerts');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_authenticated_user_can_create_price_alert(): void
    {
        $perfume = Perfume::factory()->create();
        $seller = Seller::factory()->create();
        Price::factory()->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
            'price' => 8000,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/price-alerts', [
                'perfume_id' => $perfume->id,
                'target_price' => 6000,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.target_price', '6000.00')
            ->assertJsonPath('message', 'Price alert created successfully');

        $this->assertDatabaseHas('price_alerts', [
            'user_id' => $this->user->id,
            'perfume_id' => $perfume->id,
            'target_price' => 6000,
        ]);
    }

    public function test_authenticated_user_can_create_price_alert_with_size(): void
    {
        $perfume = Perfume::factory()->create();
        $seller = Seller::factory()->create();
        Price::factory()->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
            'price' => 8000,
            'size_ml' => 100,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/price-alerts', [
                'perfume_id' => $perfume->id,
                'size_ml' => 100,
                'target_price' => 6000,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.size_ml', 100);

        $this->assertDatabaseHas('price_alerts', [
            'user_id' => $this->user->id,
            'perfume_id' => $perfume->id,
            'size_ml' => 100,
        ]);
    }

    public function test_user_cannot_create_duplicate_alert_for_same_perfume_and_size(): void
    {
        $perfume = Perfume::factory()->create();

        // Create first alert
        PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'perfume_id' => $perfume->id,
            'size_ml' => null,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/price-alerts', [
                'perfume_id' => $perfume->id,
                'target_price' => 5000,
            ]);

        $response->assertStatus(422);
    }

    public function test_user_can_update_alert_target_price(): void
    {
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'target_price' => 5000,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/v1/price-alerts/{$alert->id}", [
                'target_price' => 4000,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.target_price', '4000.00');
    }

    public function test_user_can_toggle_alert_active_status(): void
    {
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/v1/price-alerts/{$alert->id}", [
                'is_active' => false,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.is_active', false);
    }

    public function test_user_can_delete_price_alert(): void
    {
        $alert = PriceAlert::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/v1/price-alerts/{$alert->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('price_alerts', ['id' => $alert->id]);
    }

    public function test_check_returns_alert_status(): void
    {
        $perfume = Perfume::factory()->create();
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'perfume_id' => $perfume->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/v1/price-alerts/check?perfume_id={$perfume->id}");

        $response->assertStatus(200)
            ->assertJsonPath('has_alert', true)
            ->assertJsonPath('alert.id', $alert->id);
    }

    public function test_unauthenticated_user_cannot_access_price_alerts(): void
    {
        $response = $this->getJson('/api/v1/price-alerts');

        $response->assertStatus(401);
    }

    public function test_user_cannot_access_other_users_alert(): void
    {
        $otherUser = User::factory()->create();
        $alert = PriceAlert::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/v1/price-alerts/{$alert->id}");

        $response->assertStatus(403);
    }
}
