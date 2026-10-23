<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderExpiryService;

class CancelUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel orders that have not been paid within 6 hours of creation.';

    /**
     * Execute the console command.
     *
     * @param OrderExpiryService $expiryService
     * @return int
     */
    public function handle(OrderExpiryService $expiryService)
    {
        $this->info('Checking for unpaid orders older than 6 hours...');

        $cancelled = $expiryService->cancelAllExpiredOrders();

        $this->info("Successfully cancelled {$cancelled} expired unpaid order(s).");

        return Command::SUCCESS;
    }
}
