<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Model\Transaction;
use App\Model\TransactionLog;
use App\Model\BalanceTransfer;
use App\Model\Cash;
use App\Model\MobileBanking;
use App\Model\BankAccount;
use App\Model\SalePayment;
use App\Model\PurchasePayment;
use App\Model\SalesOrder;
use App\Model\PurchaseOrder;
use App\Model\EmployeeTarget;
use App\Model\Service;
use App\Model\ProductReturnOrder;
use App\Model\ManualStockOrder;

class ClearAllAmounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all-amounts {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all amount transactions and reset all financial amounts to zero or opening balances';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will permanently delete ALL financial transactions and reset all amounts. Are you sure?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting to clear all amounts and transactions...');
        
        try {
            DB::beginTransaction();

            // Clear transaction records
            $this->clearTransactions();
            
            // Clear transaction logs
            $this->clearTransactionLogs();
            
            // Clear balance transfers
            $this->clearBalanceTransfers();
            
            // Reset cash amounts
            $this->resetCashAmounts();
            
            // Reset mobile banking amounts
            $this->resetMobileBankingAmounts();
            
            // Reset bank account balances
            $this->resetBankAccountBalances();
            
            // Clear sale payments
            $this->clearSalePayments();
            
            // Clear purchase payments
            $this->clearPurchasePayments();
            
            // Clear sales orders
            $this->clearSalesOrders();
            
            // Clear purchase orders
            $this->clearPurchaseOrders();
            
            // Clear employee targets
            $this->clearEmployeeTargets();
            
            // Clear other amount-related tables
            $this->clearOtherAmountTables();

            DB::commit();
            
            $this->info('✅ All amounts and transactions have been successfully cleared!');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Error occurred: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function clearTransactions()
    {
        $count = Transaction::count();
        Transaction::truncate();
        $this->info("🗑️  Cleared {$count} transaction records");
    }

    private function clearTransactionLogs()
    {
        $count = TransactionLog::count();
        TransactionLog::truncate();
        $this->info("🗑️  Cleared {$count} transaction log records");
    }

    private function clearBalanceTransfers()
    {
        $count = BalanceTransfer::count();
        BalanceTransfer::truncate();
        $this->info("🗑️  Cleared {$count} balance transfer records");
    }

    private function resetCashAmounts()
    {
        $count = Cash::count();
        Cash::query()->update(['amount' => 0]);
        $this->info("💰 Reset {$count} cash amounts to 0");
    }

    private function resetMobileBankingAmounts()
    {
        $count = MobileBanking::count();
        MobileBanking::query()->update(['amount' => 0]);
        $this->info("📱 Reset {$count} mobile banking amounts to 0");
    }

    private function resetBankAccountBalances()
    {
        $count = BankAccount::count();
        BankAccount::query()->update(['balance' => DB::raw('opening_balance')]);
        $this->info("🏦 Reset {$count} bank account balances to opening balance");
    }

    private function clearSalePayments()
    {
        $count = SalePayment::count();
        SalePayment::truncate();
        $this->info("💳 Cleared {$count} sale payment records");
    }

    private function clearPurchasePayments()
    {
        $count = PurchasePayment::count();
        PurchasePayment::truncate();
        $this->info("💳 Cleared {$count} purchase payment records");
    }

    private function clearSalesOrders()
    {
        $count = SalesOrder::count();
        SalesOrder::truncate();
        $this->info("🛒 Cleared {$count} sales order records");
    }

    private function clearPurchaseOrders()
    {
        $count = PurchaseOrder::count();
        PurchaseOrder::truncate();
        $this->info("📦 Cleared {$count} purchase order records");
    }

    private function clearEmployeeTargets()
    {
        $count = EmployeeTarget::count();
        EmployeeTarget::query()->update(['amount' => 0]);
        $this->info("👥 Reset {$count} employee target amounts to 0");
    }

    private function clearOtherAmountTables()
    {
        // Clear services
        $serviceCount = Service::count();
        Service::query()->update([
            'quantity' => 0,
            'unit_price' => 0,
            'total' => 0
        ]);
        $this->info("🔧 Reset {$serviceCount} service amounts");

        // Clear product return orders
        $returnCount = ProductReturnOrder::count();
        ProductReturnOrder::query()->update(['total' => 0]);
        $this->info("↩️  Reset {$returnCount} product return order totals");

        // Clear manual stock orders
        $stockCount = ManualStockOrder::count();
        ManualStockOrder::query()->update(['total' => 0]);
        $this->info("📋 Reset {$stockCount} manual stock order totals");

        // Clear sales order products
        $salesProductCount = DB::table('sales_order_products')->count();
        DB::table('sales_order_products')->update([
            'quantity' => 0,
            'unit_price' => 0,
            'total' => 0
        ]);
        $this->info("🛍️  Reset {$salesProductCount} sales order product amounts");

        // Clear purchase order products
        $purchaseProductCount = DB::table('purchase_order_products')->count();
        DB::table('purchase_order_products')->update([
            'quantity' => 0,
            'unit_price' => 0,
            'selling_price' => 0,
            'total' => 0
        ]);
        $this->info("📦 Reset {$purchaseProductCount} purchase order product amounts");

        // Clear product sales order
        $productSalesCount = DB::table('product_sales_order')->count();
        DB::table('product_sales_order')->update([
            'quantity' => 0,
            'unit_price' => 0,
            'total' => 0
        ]);
        $this->info("🛒 Reset {$productSalesCount} product sales order amounts");

        // Clear purchase inventories
        $inventoryCount = DB::table('purchase_inventories')->count();
        DB::table('purchase_inventories')->update([
            'quantity' => 0,
            'unit_price' => 0,
            'avg_unit_price' => 0,
            'selling_price' => 0,
            'total' => 0
        ]);
        $this->info("📊 Reset {$inventoryCount} purchase inventory amounts");

        // Clear purchase inventory logs
        $inventoryLogCount = DB::table('purchase_inventory_logs')->count();
        DB::table('purchase_inventory_logs')->update([
            'quantity' => 0,
            'unit_price' => 0,
            'selling_price' => 0,
            'sale_total' => 0,
            'total' => 0
        ]);
        $this->info("📝 Reset {$inventoryLogCount} purchase inventory log amounts");

        // Note: client_management table doesn't exist in this database
    }
}
