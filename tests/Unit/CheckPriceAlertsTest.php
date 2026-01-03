<?php

namespace Tests\Unit;

use App\Models\Perfume;
use App\Models\Price;
use App\Models\PriceAlert;
use App\Models\Seller;
use App\Models\User;
use App\Notifications\PriceDropNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CheckPriceAlertsTest extends TestCase
{
    use RefreshDatabase;

    public function test_alerts_trigger_when_price_drops_below_target(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $perfume = Perfume::factory()->create();
        $seller = Seller::factory()->create();

        // Create a price that's below the target
        Price::factory()->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
            'price' => 5000, // Current price
        ]);

        // Create alert with target higher than current price - explicitly set all fields
        $alert = PriceAlert::create([
            'user_id' => $user->id,
            'perfume_id' => $perfume->id,
            'target_price' => 6000, // Target is higher, so should trigger (5000 <= 6000)
            'current_lowest_price' => 8000,
            'is_active' => true,
            'triggered_at' => null,
            'notification_sent_at' => null,
        ]);

        // Run the command
        $this->artisan('price-alerts:check')
            ->assertExitCode(0);

        // Refresh alert from database
        $alert->refresh();

        // Assert alert was triggered
        $this->assertNotNull($alert->triggered_at);
        $this->assertNotNull($alert->notification_sent_at);
    }

    public function test_notification_sent_when_alert_triggers(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $perfume = Perfume::factory()->create();
        $seller = Seller::factory()->create();

        Price::factory()->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
            'price' => 4500,
        ]);

        PriceAlert::create([
            'user_id' => $user->id,
            'perfume_id' => $perfume->id,
            'target_price' => 5000,
            'current_lowest_price' => 6000,
            'is_active' => true,
            'triggered_at' => null,
            'notification_sent_at' => null,
        ]);

        $this->artisan('price-alerts:check');

        Notification::assertSentTo($user, PriceDropNotification::class);
    }

    public function test_inactive_alerts_are_not_checked(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $perfume = Perfume::factory()->create();
        $seller = Seller::factory()->create();

        Price::factory()->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
            'price' => 4500,
        ]);

        PriceAlert::create([
            'user_id' => $user->id,
            'perfume_id' => $perfume->id,
            'target_price' => 5000,
            'current_lowest_price' => 6000,
            'is_active' => false, // Inactive
            'triggered_at' => null,
            'notification_sent_at' => null,
        ]);

        $this->artisan('price-alerts:check');

        Notification::assertNotSentTo($user, PriceDropNotification::class);
    }

    public function test_already_triggered_alerts_are_not_checked_again(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $perfume = Perfume::factory()->create();
        $seller = Seller::factory()->create();

        Price::factory()->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
            'price' => 4500,
        ]);

        PriceAlert::create([
            'user_id' => $user->id,
            'perfume_id' => $perfume->id,
            'target_price' => 5000,
            'current_lowest_price' => 4500,
            'is_active' => true,
            'triggered_at' => now()->subHour(), // Already triggered
            'notification_sent_at' => now()->subHour(), // Notified less than 24 hours ago
        ]);

        $this->artisan('price-alerts:check');

        Notification::assertNotSentTo($user, PriceDropNotification::class);
    }

    public function test_alert_not_triggered_when_price_above_target(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $perfume = Perfume::factory()->create();
        $seller = Seller::factory()->create();

        Price::factory()->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
            'price' => 7000, // Current price is above target
        ]);

        $alert = PriceAlert::create([
            'user_id' => $user->id,
            'perfume_id' => $perfume->id,
            'target_price' => 5000, // Target is lower (7000 > 5000)
            'current_lowest_price' => 8000,
            'is_active' => true,
            'triggered_at' => null,
            'notification_sent_at' => null,
        ]);

        $this->artisan('price-alerts:check');

        $alert->refresh();

        // Alert should NOT be triggered
        $this->assertNull($alert->triggered_at);
        Notification::assertNotSentTo($user, PriceDropNotification::class);
    }

    public function test_size_specific_alert_only_checks_matching_size_prices(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $perfume = Perfume::factory()->create();
        $seller = Seller::factory()->create();

        // Create 50ml price at 3000 (below target)
        Price::factory()->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
            'price' => 3000,
            'size_ml' => 50,
        ]);

        // Create 100ml price at 8000 (above target)
        Price::factory()->create([
            'perfume_id' => $perfume->id,
            'seller_id' => $seller->id,
            'price' => 8000,
            'size_ml' => 100,
        ]);

        // Alert specifically for 100ml
        $alert = PriceAlert::create([
            'user_id' => $user->id,
            'perfume_id' => $perfume->id,
            'size_ml' => 100,
            'target_price' => 6000,
            'current_lowest_price' => 9000,
            'is_active' => true,
            'triggered_at' => null,
            'notification_sent_at' => null,
        ]);

        $this->artisan('price-alerts:check');

        $alert->refresh();

        // Should NOT trigger because 100ml price (8000) is above target (6000)
        $this->assertNull($alert->triggered_at);
        Notification::assertNotSentTo($user, PriceDropNotification::class);
    }
}
