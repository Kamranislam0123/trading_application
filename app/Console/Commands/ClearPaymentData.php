<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\SalePayment;
use App\Model\TransactionLog;
use Illuminate\Support\Facades\DB;

class ClearPaymentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:payment-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all payment details data including sale payments and transaction logs';

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
        $this->info('Starting to clear payment details data...');

        try {
            DB::beginTransaction();

            // Clear sale_payments table
            $this->info('Clearing sale_payments table...');
            $salePaymentCount = SalePayment::count();
            SalePayment::truncate();
            $this->info("Cleared {$salePaymentCount} records from sale_payments table.");

            // Clear transaction_logs table
            $this->info('Clearing transaction_logs table...');
            $transactionLogCount = TransactionLog::count();
            TransactionLog::truncate();
            $this->info("Cleared {$transactionLogCount} records from transaction_logs table.");

            // Commit transaction
            DB::commit();

            $this->info('Successfully cleared all payment details data!');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();
            $this->error('Error occurred while clearing data: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}