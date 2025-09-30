-- Script to identify and remove duplicate payments
-- IMPORTANT: Backup your database before running this script!

-- First, let's identify duplicate payments
-- Replace 'CUSTOMER_ID' with the actual customer ID
-- Replace 'PAYMENT_AMOUNT' with the duplicate payment amount
-- Replace 'PAYMENT_DATE' with the date of duplicate payments

-- Step 1: Identify duplicate payments
SELECT 
    sp.id,
    sp.customer_id,
    sp.amount,
    sp.date,
    sp.transaction_method,
    sp.status,
    c.name as customer_name
FROM sale_payments sp
JOIN customers c ON sp.customer_id = c.id
WHERE sp.customer_id = CUSTOMER_ID  -- Replace with actual customer ID
  AND sp.amount = PAYMENT_AMOUNT    -- Replace with duplicate amount
  AND DATE(sp.date) = 'PAYMENT_DATE' -- Replace with duplicate date
  AND sp.status IN (2, 3)  -- Only approved payments
ORDER BY sp.id;

-- Step 2: Delete duplicate payments (keep only the first one)
-- WARNING: This will permanently delete the duplicate payments
-- Make sure to backup your database first!

-- Delete duplicate payments (keeping the first one)
DELETE sp FROM sale_payments sp
WHERE sp.customer_id = CUSTOMER_ID  -- Replace with actual customer ID
  AND sp.amount = PAYMENT_AMOUNT    -- Replace with duplicate amount
  AND DATE(sp.date) = 'PAYMENT_DATE' -- Replace with duplicate date
  AND sp.status IN (2, 3)  -- Only approved payments
  AND sp.id NOT IN (
    SELECT * FROM (
      SELECT MIN(id) 
      FROM sale_payments 
      WHERE customer_id = CUSTOMER_ID
        AND amount = PAYMENT_AMOUNT
        AND DATE(date) = 'PAYMENT_DATE'
        AND status IN (2, 3)
    ) AS keep_first
  );

-- Step 3: Also delete related transaction logs
DELETE tl FROM transaction_logs tl
WHERE tl.sale_payment_id IN (
  SELECT id FROM sale_payments 
  WHERE customer_id = CUSTOMER_ID
    AND amount = PAYMENT_AMOUNT
    AND DATE(date) = 'PAYMENT_DATE'
    AND status IN (2, 3)
);

-- Step 4: Update cash/bank balances if needed
-- For cash payments (transaction_method = 1)
UPDATE cash 
SET amount = amount - (PAYMENT_AMOUNT * 4)  -- Subtract 4 duplicate payments
WHERE id = 1;

-- For bank payments (transaction_method = 2)
-- Update the specific bank account balance
-- UPDATE bank_accounts 
-- SET balance = balance - (PAYMENT_AMOUNT * 4)  -- Subtract 4 duplicate payments
-- WHERE id = BANK_ACCOUNT_ID;  -- Replace with actual bank account ID
