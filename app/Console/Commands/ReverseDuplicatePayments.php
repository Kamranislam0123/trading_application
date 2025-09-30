<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\SalePayment;
use App\Model\TransactionLog;
use App\Model\Cash;
use App\Model\BankAccount;
use App\Model\Customer;
use Illuminate\Support\Facades\DB;

class ReverseDuplicatePayments extends Command
{
    protected $signature = 'payment:reverse-duplicates {customer_id} {amount} {date} {--keep=1}';
    protected $description = 'Reverse duplicate payments for a customer';

    public function handle()
    {
        $customerId = $this->argument('customer_id');
        $amount = $this->argument('amount');
        $date = $this->argument('date');
        $keepCount = $this->option('keep');

        $customer = Customer::find($customerId);
        if (!$customer) {
            $this->error('Customer not found!');
            return;
        }

        // Find duplicate payments
        $duplicatePayments = SalePayment::where('customer_id', $customerId)
            ->where('amount', $amount)
            ->whereDate('date', $date)
            ->whereIn('status', [2, 3]) // Approved payments
            ->orderBy('id')
            ->get();

        if ($duplicatePayments->count() <= $keepCount) {
            $this->info('No duplicate payments found or already at minimum count.');
            return;
        }

        $this->info("Found {$duplicatePayments->count()} payments for customer: {$customer->name}");
        $this->info("Amount: {$amount}, Date: {$date}");
        $this->info("Will keep {$keepCount} payment(s) and delete " . ($duplicatePayments->count() - $keepCount) . " duplicate(s)");

        if (!$this->confirm('Do you want to proceed with the deletion?')) {
            $this->info('Operation cancelled.');
            return;
        }

        try {
            DB::beginTransaction();

            $paymentsToDelete = $duplicatePayments->skip($keepCount);
            $totalDeletedAmount = 0;

            foreach ($paymentsToDelete as $payment) {
                $this->info("Deleting payment ID: {$payment->id}");

                // Update cash/bank balances
                if ($payment->transaction_method == 1) {
                    // Cash payment
                    Cash::first()->decrement('amount', $payment->amount);
                    $this->info("  - Decremented cash by: {$payment->amount}");
                } elseif ($payment->transaction_method == 2 && $payment->bank_account_id) {
                    // Bank payment
                    BankAccount::find($payment->bank_account_id)->decrement('balance', $payment->amount);
                    $this->info("  - Decremented bank balance by: {$payment->amount}");
                }

                // Delete transaction logs
                TransactionLog::where('sale_payment_id', $payment->id)->delete();
                $this->info("  - Deleted transaction logs");

                // Delete the payment
                $payment->delete();
                $this->info("  - Deleted payment record");

                $totalDeletedAmount += $payment->amount;
            }

            DB::commit();

            $this->info("Successfully deleted " . $paymentsToDelete->count() . " duplicate payments");
            $this->info("Total amount reversed: {$totalDeletedAmount}");
            $this->info("Remaining payments: {$keepCount}");

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Error occurred: ' . $e->getMessage());
        }
    }
}
