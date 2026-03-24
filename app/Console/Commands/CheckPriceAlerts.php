<?php

namespace App\Console\Commands;

use App\Models\PriceAlert;
use App\Models\Price;
use App\Notifications\PriceDropNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPriceAlerts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'price-alerts:check';

    /**
     * The console command description.
     */
    protected $description = 'Check all active price alerts and send notifications for triggered alerts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking price alerts...');

        $alerts = PriceAlert::with(['perfume', 'user'])
            ->active()
            ->readyForCheck()
            ->get();

        $this->info("Found {$alerts->count()} alerts to check.");

        $triggered = 0;

        foreach ($alerts as $alert) {
            // Get current lowest price for this perfume (only in-stock, filtered by size if specified)
            $priceQuery = Price::where('perfume_id', $alert->perfume_id)
                ->where('stock_status', 'In Stock');
            if ($alert->size_ml) {
                $priceQuery->where('size_ml', $alert->size_ml);
            }
            $currentLowest = $priceQuery->min('price');

            if ($currentLowest === null) {
                continue;
            }

            // Update the cached current lowest price
            $alert->update(['current_lowest_price' => $currentLowest]);

            // Check if alert should trigger
            if ($alert->shouldTrigger($currentLowest)) {
                $this->line("  ✓ Alert #{$alert->id}: {$alert->perfume->name} dropped to ₹{$currentLowest} (target: ₹{$alert->target_price})");

                try {
                    // Send notification
                    $alert->user->notify(new PriceDropNotification($alert, $currentLowest));

                    // Mark as triggered
                    $alert->markAsTriggered();

                    $triggered++;
                } catch (\Exception $e) {
                    Log::error("Failed to send price alert notification: {$e->getMessage()}", [
                        'alert_id' => $alert->id,
                        'user_id' => $alert->user_id,
                    ]);
                    $this->error("  ✗ Failed to send notification for alert #{$alert->id}");
                }
            }
        }

        $this->info("Triggered {$triggered} alerts.");

        return Command::SUCCESS;
    }
}
