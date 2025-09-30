<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Model\SalesOrder;
use App\Model\SalePayment;
use App\Model\Customer;
use App\Model\ProductItem;
use App\Model\SalesOrderProduct;
use Carbon\Carbon;

class TestDashboardData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:dashboard-data {--clear : Clear test data after testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add test data to verify dashboard functionality after clearing all amounts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🧪 Adding test data for dashboard verification...');
        
        try {
            DB::beginTransaction();

            // Get first customer and product for testing
            $customer = Customer::first();
            $product = ProductItem::first();
            
            if (!$customer) {
                $this->error('❌ No customers found. Please add a customer first.');
                return 1;
            }
            
            if (!$product) {
                $this->error('❌ No products found. Please add a product first.');
                return 1;
            }

            $this->line("Using customer: {$customer->name}");
            $this->line("Using product: {$product->name}");

            // Create test sales order for today
            $salesOrder = new SalesOrder();
            $salesOrder->customer_id = $customer->id;
            $salesOrder->date = Carbon::today();
            $salesOrder->sub_total = 1000;
            $salesOrder->vat_percentage = 0;
            $salesOrder->vat = 0;
            $salesOrder->discount_percentage = 0;
            $salesOrder->discount = 0;
            $salesOrder->transport_cost = 0;
            $salesOrder->return_amount = 0;
            $salesOrder->total = 1000;
            $salesOrder->paid = 500; // Partially paid
            $salesOrder->due = 500; // 500 due
            $salesOrder->previous_due = 0;
            $salesOrder->current_due = 500;
            $salesOrder->company_branch_id = 1;
            $salesOrder->sale_type = 1;
            $salesOrder->status = 1;
            $salesOrder->save();

            $this->line("✅ Created sales order #{$salesOrder->id} with total: ৳1000, paid: ৳500, due: ৳500");

            // Create sales order product
            $salesOrderProduct = new SalesOrderProduct();
            $salesOrderProduct->sales_order_id = $salesOrder->id;
            $salesOrderProduct->product_item_id = $product->id;
            $salesOrderProduct->quantity = 2;
            $salesOrderProduct->unit_price = 500;
            $salesOrderProduct->total = 1000;
            $salesOrderProduct->save();

            $this->line("✅ Added product to sales order");

            // Create a sale payment for today (received amount)
            $salePayment = new SalePayment();
            $salePayment->customer_id = $customer->id;
            $salePayment->sales_order_id = $salesOrder->id;
            $salePayment->date = Carbon::today();
            $salePayment->amount = 500;
            $salePayment->type = 1; // Received
            $salePayment->transaction_method = 1; // Cash
            $salePayment->status = 2; // Approved
            $salePayment->company_branch_id = 1;
            $salePayment->save();

            $this->line("✅ Created sale payment of ৳500");

            DB::commit();

            $this->line('');
            $this->info('✅ Test data created successfully!');
            $this->line('');
            $this->line('Dashboard should now show:');
            $this->line('- Today\'s Total Collection: ৳500 (due amount)');
            $this->line('- Today\'s Sales: ৳1000');
            $this->line('- Today\'s Due: ৳500');
            $this->line('');
            $this->line('You can now check the dashboard to verify it\'s working.');
            
            if ($this->option('clear')) {
                $this->line('');
                $this->info('Clearing test data...');
                $salesOrder->delete();
                $this->line('✅ Test data cleared');
            } else {
                $this->line('');
                $this->line('To clear this test data later, run:');
                $this->line('php artisan test:dashboard-data --clear');
            }

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Error creating test data: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
