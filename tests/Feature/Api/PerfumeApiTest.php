<?php

namespace Tests\Feature\Api;

use App\Models\Perfume;
use App\Models\Price;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerfumeApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can list perfumes.
     */
    public function test_can_list_perfumes(): void
    {
        Perfume::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/perfumes');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'brand'],
                ],
            ]);
    }

    /**
     * Test can show single perfume.
     */
    public function test_can_show_single_perfume(): void
    {
        $perfume = Perfume::factory()->create([
            'name' => 'Sauvage',
            'brand' => 'Dior',
        ]);

        $response = $this->getJson("/api/v1/perfumes/{$perfume->id}");

        $response->assertOk()
            ->assertJsonPath('data.name', 'Sauvage')
            ->assertJsonPath('data.brand', 'Dior');
    }

    /**
     * Test can search perfumes by name.
     */
    public function test_can_search_perfumes_by_name(): void
    {
        Perfume::factory()->create(['name' => 'Sauvage', 'brand' => 'Dior']);
        Perfume::factory()->create(['name' => 'Bleu', 'brand' => 'Chanel']);
        Perfume::factory()->create(['name' => 'Aventus', 'brand' => 'Creed']);

        $response = $this->getJson('/api/v1/perfumes?search=Sauvage');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Sauvage');
    }

    /**
     * Test can search perfumes by brand.
     */
    public function test_can_search_perfumes_by_brand(): void
    {
        Perfume::factory()->create(['name' => 'Sauvage', 'brand' => 'Dior']);
        Perfume::factory()->create(['name' => 'Bleu', 'brand' => 'Chanel']);

        $response = $this->getJson('/api/v1/perfumes?search=Chanel');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.brand', 'Chanel');
    }

    /**
     * Test can get perfume prices.
     */
    public function test_can_get_perfume_prices(): void
    {
        $perfume = Perfume::factory()->create();
        $seller = Seller::factory()->create();

        Price::factory()->count(2)->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
        ]);

        $response = $this->getJson("/api/v1/perfumes/{$perfume->id}/prices");

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    /**
     * Test unauthenticated user cannot create perfume.
     */
    public function test_unauthenticated_user_cannot_create_perfume(): void
    {
        $response = $this->postJson('/api/v1/perfumes', [
            'name' => 'Test Perfume',
            'brand' => 'Test Brand',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user can create perfume.
     */
    public function test_authenticated_user_can_create_perfume(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/perfumes', [
                'name' => 'New Perfume',
                'brand' => 'New Brand',
                'description' => 'A fantastic new fragrance',
                'concentration' => 'EDP',
                'gender_affinity' => 'Unisex',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Perfume')
            ->assertJsonPath('data.brand', 'New Brand');

        $this->assertDatabaseHas('perfumes', [
            'name' => 'New Perfume',
            'brand' => 'New Brand',
        ]);
    }

    /**
     * Test unauthenticated user cannot update perfume.
     */
    public function test_unauthenticated_user_cannot_update_perfume(): void
    {
        $perfume = Perfume::factory()->create();

        $response = $this->putJson("/api/v1/perfumes/{$perfume->id}", [
            'name' => 'Updated Name',
            'brand' => $perfume->brand,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user can update perfume.
     */
    public function test_authenticated_user_can_update_perfume(): void
    {
        $user = User::factory()->create();
        $perfume = Perfume::factory()->create([
            'name' => 'Original Name',
            'brand' => 'Original Brand',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/perfumes/{$perfume->id}", [
                'name' => 'Updated Name',
                'brand' => 'Updated Brand',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.brand', 'Updated Brand');

        $this->assertDatabaseHas('perfumes', [
            'id' => $perfume->id,
            'name' => 'Updated Name',
            'brand' => 'Updated Brand',
        ]);
    }

    /**
     * Test unauthenticated user cannot delete perfume.
     */
    public function test_unauthenticated_user_cannot_delete_perfume(): void
    {
        $perfume = Perfume::factory()->create();

        $response = $this->deleteJson("/api/v1/perfumes/{$perfume->id}");

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user can delete perfume.
     */
    public function test_authenticated_user_can_delete_perfume(): void
    {
        $user = User::factory()->create();
        $perfume = Perfume::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/perfumes/{$perfume->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('perfumes', [
            'id' => $perfume->id,
        ]);
    }

    /**
     * Test perfume creation fails without required fields.
     */
    public function test_perfume_creation_fails_without_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/perfumes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'brand']);
    }

    /**
     * Test returns 404 for non-existent perfume.
     */
    public function test_returns_404_for_nonexistent_perfume(): void
    {
        $response = $this->getJson('/api/v1/perfumes/99999');

        $response->assertNotFound();
    }
}
