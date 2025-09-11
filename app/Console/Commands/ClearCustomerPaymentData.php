<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\SalePayment;
use App\Model\Customer;
use Illuminate\Support\Facades\DB;

class ClearCustomerPaymentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:customer-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear customer payment table and customer table data';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting to clear customer payment and customer data...');
        
        try {
            // Start database transaction
            DB::beginTransaction();
            
            // Clear sale_payments table
            $this->info('Clearing sale_payments table...');
            $salePaymentCount = SalePayment::count();
            SalePayment::truncate();
            $this->info("Cleared {$salePaymentCount} records from sale_payments table.");
            
            // Clear customers table
            $this->info('Clearing customers table...');
            $customerCount = Customer::count();
            Customer::truncate();
            $this->info("Cleared {$customerCount} records from customers table.");
            
            // Commit transaction
            DB::commit();
            
            $this->info('Successfully cleared all customer payment and customer data!');
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();
            $this->error('Error occurred while clearing data: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
