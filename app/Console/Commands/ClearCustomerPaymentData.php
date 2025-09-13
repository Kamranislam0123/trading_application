<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Customer;
use App\Model\SalePayment;
use App\Model\TransactionLog;
use App\Model\SalesOrder;
use Illuminate\Support\Facades\DB;

class ClearCustomerPaymentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:customer-payment-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all customer payment related data including customers, sales orders, sale payments, and transaction logs';

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
        $this->info('Starting to clear customer payment related data...');

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

            // Clear sales_orders table
            $this->info('Clearing sales_orders table...');
            $salesOrderCount = SalesOrder::count();
            SalesOrder::truncate();
            $this->info("Cleared {$salesOrderCount} records from sales_orders table.");

            // Clear customers table
            $this->info('Clearing customers table...');
            $customerCount = Customer::count();
            Customer::truncate();
            $this->info("Cleared {$customerCount} records from customers table.");

            // Commit transaction
            DB::commit();

            $this->info('Successfully cleared all customer payment related data!');
            $this->info('Cleared tables: customers, sales_orders, sale_payments, transaction_logs');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();
            $this->error('Error occurred while clearing data: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}