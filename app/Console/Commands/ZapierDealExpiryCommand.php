<?php

namespace App\Console\Commands;

use App\Models\ProductDealPrice;
use App\Services\ZapierService;
use Illuminate\Console\Command;

class ZapierDealExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zapier:deal-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zapier deal expiry webhook';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ProductDealPrice::where('expiry_date', '<', now())
            ->where('expiry_notification', '===', false)
            ->chunk(100, function($deals) {
                foreach($deals as $deal) {
                    $deal->update(['expiry_notification' => true]);
                    ZapierService::dealExpires($deal->id);
                }
            });
    }
}
