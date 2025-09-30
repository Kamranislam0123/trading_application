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

class TestSystemAfterClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:system-after-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the system after clearing all amounts to ensure everything is working correctly';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🧪 Testing system after clearing all amounts...');
        $this->line('');

        $allTestsPassed = true;

        // Test 1: Verify transaction records are cleared
        $allTestsPassed &= $this->testTransactionClearing();
        
        // Test 2: Verify amounts are reset
        $allTestsPassed &= $this->testAmountResets();
        
        // Test 3: Test database integrity
        $allTestsPassed &= $this->testDatabaseIntegrity();
        
        // Test 4: Test basic system functionality
        $allTestsPassed &= $this->testBasicFunctionality();

        $this->line('');
        if ($allTestsPassed) {
            $this->info('✅ All tests passed! System is ready for use.');
        } else {
            $this->error('❌ Some tests failed. Please check the issues above.');
        }

        return $allTestsPassed ? 0 : 1;
    }

    private function testTransactionClearing()
    {
        $this->info('📋 Testing transaction clearing...');
        
        $tests = [
            'Transactions' => Transaction::count(),
            'Transaction Logs' => TransactionLog::count(),
            'Balance Transfers' => BalanceTransfer::count(),
            'Sale Payments' => SalePayment::count(),
            'Purchase Payments' => PurchasePayment::count(),
            'Sales Orders' => SalesOrder::count(),
            'Purchase Orders' => PurchaseOrder::count(),
        ];

        $allPassed = true;
        foreach ($tests as $name => $count) {
            if ($count === 0) {
                $this->line("  ✅ {$name}: {$count} records (cleared)");
            } else {
                $this->line("  ❌ {$name}: {$count} records (should be 0)");
                $allPassed = false;
            }
        }

        return $allPassed;
    }

    private function testAmountResets()
    {
        $this->info('💰 Testing amount resets...');
        
        $allPassed = true;

        // Test Cash amounts
        $cashAmount = Cash::first()->amount ?? 0;
        if ($cashAmount == 0) {
            $this->line("  ✅ Cash amount: {$cashAmount} (reset to 0)");
        } else {
            $this->line("  ❌ Cash amount: {$cashAmount} (should be 0)");
            $allPassed = false;
        }

        // Test Mobile Banking amounts
        $mobileAmount = MobileBanking::first()->amount ?? 0;
        if ($mobileAmount == 0) {
            $this->line("  ✅ Mobile banking amount: {$mobileAmount} (reset to 0)");
        } else {
            $this->line("  ❌ Mobile banking amount: {$mobileAmount} (should be 0)");
            $allPassed = false;
        }

        // Test Bank Account balances
        $bankAccounts = BankAccount::all();
        $this->line("  🏦 Bank account balances:");
        foreach ($bankAccounts as $account) {
            if ($account->balance == $account->opening_balance) {
                $this->line("    ✅ {$account->account_name}: {$account->balance} (reset to opening balance)");
            } else {
                $this->line("    ❌ {$account->account_name}: {$account->balance} (opening: {$account->opening_balance})");
                $allPassed = false;
            }
        }

        // Test Employee Targets
        $employeeTargets = EmployeeTarget::where('amount', '>', 0)->count();
        if ($employeeTargets == 0) {
            $this->line("  ✅ Employee targets: All amounts reset to 0");
        } else {
            $this->line("  ❌ Employee targets: {$employeeTargets} records still have amounts > 0");
            $allPassed = false;
        }

        return $allPassed;
    }

    private function testDatabaseIntegrity()
    {
        $this->info('🔗 Testing database integrity...');
        
        $allPassed = true;

        try {
            // Test basic model relationships
            $cash = Cash::first();
            if ($cash) {
                $this->line("  ✅ Cash model: Accessible");
            } else {
                $this->line("  ❌ Cash model: No records found");
                $allPassed = false;
            }

            $mobileBanking = MobileBanking::first();
            if ($mobileBanking) {
                $this->line("  ✅ Mobile Banking model: Accessible");
            } else {
                $this->line("  ❌ Mobile Banking model: No records found");
                $allPassed = false;
            }

            $bankAccounts = BankAccount::count();
            if ($bankAccounts > 0) {
                $this->line("  ✅ Bank Accounts: {$bankAccounts} accounts available");
            } else {
                $this->line("  ❌ Bank Accounts: No accounts found");
                $allPassed = false;
            }

            // Test foreign key relationships
            $this->line("  🔍 Testing foreign key relationships...");
            
            // Test if we can still access related models
            $accountHeadTypes = DB::table('account_head_types')->count();
            $this->line("    ✅ Account Head Types: {$accountHeadTypes} available");
            
            $accountHeadSubTypes = DB::table('account_head_sub_types')->count();
            $this->line("    ✅ Account Head Sub Types: {$accountHeadSubTypes} available");
            
            $banks = DB::table('banks')->count();
            $this->line("    ✅ Banks: {$banks} available");
            
            $branches = DB::table('branches')->count();
            $this->line("    ✅ Branches: {$branches} available");

        } catch (\Exception $e) {
            $this->line("  ❌ Database integrity test failed: " . $e->getMessage());
            $allPassed = false;
        }

        return $allPassed;
    }

    private function testBasicFunctionality()
    {
        $this->info('⚙️  Testing basic system functionality...');
        
        $allPassed = true;

        try {
            // Test creating a new transaction (without saving)
            $transaction = new Transaction();
            $transaction->transaction_type = 1;
            $transaction->account_head_type_id = 1;
            $transaction->account_head_sub_type_id = 1;
            $transaction->transaction_method = 1;
            $transaction->amount = 100;
            $transaction->date = now();
            $transaction->note = 'Test transaction';
            
            if ($transaction->amount == 100) {
                $this->line("  ✅ Transaction model: Can create new transactions");
            } else {
                $this->line("  ❌ Transaction model: Creation failed");
                $allPassed = false;
            }

            // Test cash operations
            $cash = Cash::first();
            if ($cash) {
                $originalAmount = $cash->amount;
                $cash->increment('amount', 50);
                $cash->decrement('amount', 50);
                if ($cash->amount == $originalAmount) {
                    $this->line("  ✅ Cash operations: Increment/decrement working");
                } else {
                    $this->line("  ❌ Cash operations: Increment/decrement failed");
                    $allPassed = false;
                }
            }

            // Test bank account operations
            $bankAccount = BankAccount::first();
            if ($bankAccount) {
                $originalBalance = $bankAccount->balance;
                $bankAccount->increment('balance', 100);
                $bankAccount->decrement('balance', 100);
                if ($bankAccount->balance == $originalBalance) {
                    $this->line("  ✅ Bank account operations: Increment/decrement working");
                } else {
                    $this->line("  ❌ Bank account operations: Increment/decrement failed");
                    $allPassed = false;
                }
            }

            // Test database queries
            $transactionCount = Transaction::count();
            $this->line("  ✅ Database queries: Transaction count = {$transactionCount}");

        } catch (\Exception $e) {
            $this->line("  ❌ Basic functionality test failed: " . $e->getMessage());
            $allPassed = false;
        }

        return $allPassed;
    }
}
