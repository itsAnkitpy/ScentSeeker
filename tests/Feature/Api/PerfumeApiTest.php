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
     * Test admin user can create perfume.
     */
    public function test_admin_user_can_create_perfume(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')
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
     * Test non-admin user cannot create perfume.
     */
    public function test_non_admin_user_cannot_create_perfume(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/perfumes', [
                'name' => 'New Perfume',
                'brand' => 'New Brand',
            ]);

        $response->assertStatus(403);
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
     * Test admin user can update perfume.
     */
    public function test_admin_user_can_update_perfume(): void
    {
        $admin = User::factory()->admin()->create();
        $perfume = Perfume::factory()->create([
            'name' => 'Original Name',
            'brand' => 'Original Brand',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
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
     * Test non-admin user cannot update perfume.
     */
    public function test_non_admin_user_cannot_update_perfume(): void
    {
        $user = User::factory()->create();
        $perfume = Perfume::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/perfumes/{$perfume->id}", [
                'name' => 'Hacked Name',
            ]);

        $response->assertStatus(403);
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
     * Test admin user can delete perfume.
     */
    public function test_admin_user_can_delete_perfume(): void
    {
        $admin = User::factory()->admin()->create();
        $perfume = Perfume::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/perfumes/{$perfume->id}");

        $response->assertNoContent();

        // Perfume is soft-deleted, not hard-deleted
        $this->assertSoftDeleted('perfumes', [
            'id' => $perfume->id,
        ]);
    }

    /**
     * Test non-admin user cannot delete perfume.
     */
    public function test_non_admin_user_cannot_delete_perfume(): void
    {
        $user = User::factory()->create();
        $perfume = Perfume::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/perfumes/{$perfume->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('perfumes', [
            'id' => $perfume->id,
        ]);
    }

    /**
     * Test perfume creation fails without required fields.
     */
    public function test_perfume_creation_fails_without_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')
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

    // --- Pagination Tests ---

    public function test_pagination_returns_correct_page(): void
    {
        Perfume::factory()->count(20)->create();

        $response = $this->getJson('/api/v1/perfumes?page=2');

        $response->assertOk()
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonCount(5, 'data'); // 20 items, 15 per page = 5 on page 2
    }

    public function test_pagination_returns_empty_for_out_of_range_page(): void
    {
        Perfume::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/perfumes?page=999');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    // --- Sorting Tests ---

    public function test_sort_by_name_ascending(): void
    {
        Perfume::factory()->create(['name' => 'Zebra']);
        Perfume::factory()->create(['name' => 'Alpha']);
        Perfume::factory()->create(['name' => 'Middle']);

        $response = $this->getJson('/api/v1/perfumes?sort=name_asc');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertEquals(['Alpha', 'Middle', 'Zebra'], $names);
    }

    public function test_sort_by_name_descending(): void
    {
        Perfume::factory()->create(['name' => 'Alpha']);
        Perfume::factory()->create(['name' => 'Zebra']);

        $response = $this->getJson('/api/v1/perfumes?sort=name_desc');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertEquals(['Zebra', 'Alpha'], $names);
    }

    public function test_sort_by_price_low_to_high(): void
    {
        $expensive = Perfume::factory()->create(['name' => 'Expensive']);
        $cheap = Perfume::factory()->create(['name' => 'Cheap']);
        $seller = Seller::factory()->create();

        Price::factory()->create(['perfume_id' => $expensive->id, 'seller_id' => $seller->id, 'price' => 9000, 'stock_status' => 'In Stock']);
        Price::factory()->create(['perfume_id' => $cheap->id, 'seller_id' => $seller->id, 'price' => 1000, 'stock_status' => 'In Stock']);

        $response = $this->getJson('/api/v1/perfumes?sort=price_low_to_high');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertEquals('Cheap', $names[0]);
    }

    public function test_sort_by_newest(): void
    {
        $old = Perfume::factory()->create(['name' => 'Old', 'created_at' => now()->subDays(5)]);
        $new = Perfume::factory()->create(['name' => 'New', 'created_at' => now()]);

        $response = $this->getJson('/api/v1/perfumes?sort=newest');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertEquals('New', $names[0]);
    }

    // --- Advanced Filter Tests ---

    public function test_filter_by_brand(): void
    {
        Perfume::factory()->create(['brand' => 'Dior']);
        Perfume::factory()->create(['brand' => 'Chanel']);
        Perfume::factory()->create(['brand' => 'Dior']);

        $response = $this->getJson('/api/v1/perfumes?brands=Dior');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_filter_by_multiple_brands(): void
    {
        Perfume::factory()->create(['brand' => 'Dior']);
        Perfume::factory()->create(['brand' => 'Chanel']);
        Perfume::factory()->create(['brand' => 'Creed']);

        $response = $this->getJson('/api/v1/perfumes?brands=Dior,Chanel');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_filter_by_concentration(): void
    {
        Perfume::factory()->create(['concentration' => 'EDP']);
        Perfume::factory()->create(['concentration' => 'EDT']);
        Perfume::factory()->create(['concentration' => 'EDP']);

        $response = $this->getJson('/api/v1/perfumes?concentrations=EDP');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_filter_by_gender(): void
    {
        Perfume::factory()->create(['gender_affinity' => 'Male']);
        Perfume::factory()->create(['gender_affinity' => 'Female']);
        Perfume::factory()->create(['gender_affinity' => 'Unisex']);

        $response = $this->getJson('/api/v1/perfumes?genders=Male');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_filter_by_price_range(): void
    {
        $perfume1 = Perfume::factory()->create();
        $perfume2 = Perfume::factory()->create();
        $seller = Seller::factory()->create();

        Price::factory()->create(['perfume_id' => $perfume1->id, 'seller_id' => $seller->id, 'price' => 500, 'stock_status' => 'In Stock']);
        Price::factory()->create(['perfume_id' => $perfume2->id, 'seller_id' => $seller->id, 'price' => 5000, 'stock_status' => 'In Stock']);

        $response = $this->getJson('/api/v1/perfumes?min_price=400&max_price=1000');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_filter_by_size(): void
    {
        $perfume1 = Perfume::factory()->create();
        $perfume2 = Perfume::factory()->create();
        $seller = Seller::factory()->create();

        Price::factory()->create(['perfume_id' => $perfume1->id, 'seller_id' => $seller->id, 'size_ml' => 50]);
        Price::factory()->create(['perfume_id' => $perfume2->id, 'seller_id' => $seller->id, 'size_ml' => 100]);

        $response = $this->getJson('/api/v1/perfumes?sizes=100');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_filter_by_notes(): void
    {
        Perfume::factory()->create(['notes' => ['bergamot', 'vanilla', 'musk']]);
        Perfume::factory()->create(['notes' => ['rose', 'jasmine']]);
        Perfume::factory()->create(['notes' => ['bergamot', 'cedar']]);

        $response = $this->getJson('/api/v1/perfumes?notes=bergamot');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_filters_endpoint_returns_available_options(): void
    {
        $seller = Seller::factory()->create();
        $perfume = Perfume::factory()->create(['brand' => 'TestBrand', 'concentration' => 'EDP', 'gender_affinity' => 'Male']);
        Price::factory()->create(['perfume_id' => $perfume->id, 'seller_id' => $seller->id, 'size_ml' => 100, 'price' => 5000]);

        $response = $this->getJson('/api/v1/perfumes/filters');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['brands', 'concentrations', 'genders', 'sizes', 'price_range' => ['min', 'max']],
            ]);
    }
}
