<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\TestController;

Route::get('data', 'TestController@index');

Auth::routes(['register' => false, 'reset' => false]);

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    // User Management
    Route::get('user', 'UserController@index')->name('user.all');
    Route::get('user/add', 'UserController@add')->name('user.add');
    Route::post('user/add', 'UserController@addPost');
    Route::get('user/edit/{user}', 'UserController@edit')->name('user.edit');
    Route::post('user/edit/{user}', 'UserController@editPost');
    Route::get('user-activity', 'UserController@userActivity')->name('user.activity');

    // Unit
    Route::get('unit', 'UnitController@index')->name('unit');
    Route::get('unit/add', 'UnitController@add')->name('unit.add');
    Route::post('unit/add', 'UnitController@addPost');
    Route::get('unit/edit/{unit}', 'UnitController@edit')->name('unit.edit');
    Route::post('unit/edit/{unit}', 'UnitController@editPost');

    // Warehouse
    Route::get('warehouse', 'WarehouseController@index')->name('warehouse')->middleware('permission:warehouse');
    Route::get('warehouse/add', 'WarehouseController@add')->name('warehouse.add')->middleware('permission:warehouse');
    Route::post('warehouse/add', 'WarehouseController@addPost')->middleware('permission:warehouse');
    Route::get('warehouse/edit/{warehouse}', 'WarehouseController@edit')->name('warehouse.edit')->middleware('permission:warehouse');
    Route::post('warehouse/edit/{warehouse}', 'WarehouseController@editPost')->middleware('permission:warehouse');

    // Company Branch
    Route::get('company-branch', 'CompanyBranchController@index')->name('company-branch');
    Route::get('company-branch/add', 'CompanyBranchController@add')->name('company-branch.add');
    Route::post('company-branch/add', 'CompanyBranchController@addPost');
    Route::get('company-branch/edit/{company_branch}', 'CompanyBranchController@edit')->name('company-branch.edit');
    Route::post('company-branch/edit/{company_branch}', 'CompanyBranchController@editPost');

    // Bank
    Route::get('bank', 'BankController@index')->name('bank')->middleware('permission:bank');
    Route::get('bank/add', 'BankController@add')->name('bank.add')->middleware('permission:bank');
    Route::post('bank/add', 'BankController@addPost')->middleware('permission:bank');
    Route::get('bank/edit/{bank}', 'BankController@edit')->name('bank.edit')->middleware('permission:bank');
    Route::post('bank/edit/{bank}', 'BankController@editPost')->middleware('permission:bank');

    // Bank Branch
    Route::get('bank-branch', 'BranchController@index')->name('branch')->middleware('permission:branch');
    Route::get('bank-branch/add', 'BranchController@add')->name('branch.add')->middleware('permission:branch');
    Route::post('bank-branch/add', 'BranchController@addPost')->middleware('permission:branch');
    Route::get('bank-branch/edit/{branch}', 'BranchController@edit')->name('branch.edit')->middleware('permission:branch');
    Route::post('bank-branch/edit/{branch}', 'BranchController@editPost')->middleware('permission:branch');

    // Bank Account
    Route::get('bank-account', 'BankAccountController@index')->name('bank_account')->middleware('permission:account');
    Route::get('bank-account/add', 'BankAccountController@add')->name('bank_account.add')->middleware('permission:account');
    Route::post('bank-account/add', 'BankAccountController@addPost')->middleware('permission:account');
    Route::get('bank-account/edit/{account}', 'BankAccountController@edit')->name('bank_account.edit')->middleware('permission:account');
    Route::post('bank-account/edit/{account}', 'BankAccountController@editPost')->middleware('permission:account');
    Route::get('bank-account/get-branches', 'BankAccountController@getBranches')->name('bank_account.get_branch');

    // Department
    Route::get('department', 'DepartmentController@index')->name('department')->middleware('permission:department');
    Route::get('department/add', 'DepartmentController@add')->name('department.add')->middleware('permission:department');
    Route::post('department/add', 'DepartmentController@addPost')->middleware('permission:department');
    Route::get('department/edit/{department}', 'DepartmentController@edit')->name('department.edit')->middleware('permission:department');
    Route::post('department/edit/{department}', 'DepartmentController@editPost')->middleware('permission:department');

    // Designation
    Route::get('designation', 'DesignationController@index')->name('designation')->middleware('permission:designation');
    Route::get('designation/add', 'DesignationController@add')->name('designation.add')->middleware('permission:designation');
    Route::post('designation/add', 'DesignationController@addPost')->middleware('permission:designation');
    Route::get('designation/edit/{designation}', 'DesignationController@edit')->name('designation.edit')->middleware('permission:designation');
    Route::post('designation/edit/{designation}', 'DesignationController@editPost')->middleware('permission:designation');


    // HR
    Route::get('employee', 'HRController@employeeIndex')->name('employee.all')->middleware('permission:employee');
    Route::get('employee/datatable', 'HRController@employeeDatatable')->name('employee.datatable')->middleware('permission:employee');
    Route::get('employee/add', 'HRController@employeeAdd')->name('employee.add')->middleware('permission:employee');
    Route::post('employee/add', 'HRController@employeeAddPost')->middleware('permission:employee');
    Route::get('employee/edit/{employee}', 'HRController@employeeEdit')->name('employee.edit')->middleware('permission:employee');
    Route::post('employee/edit/{employee}', 'HRController@employeeEditPost')->middleware('permission:employee');
    Route::get('employee/details/{employee}', 'HRController@employeeDetails')->name('employee.details')->middleware('permission:employee');
    Route::post('employee/designation/update', 'HRController@employeeDesignationUpdate')->name('employee.designation_update')->middleware('permission:employee');
    Route::get('employee/attendance', 'HRController@employeeAttendance')->name('employee.attendance')->middleware('permission:employee_attendance');
    Route::post('employee/attendance', 'HRController@employeeAttendancePost')->middleware('permission:employee_attendance');
    Route::post('employee/target-update', 'HRController@employeeTargetUpdate')->name('employee.target_update');

    Route::post('payroll/get-leave', 'HRController@getLeave')->name('employee.get_leaves')->middleware('permission:leave');
    Route::post('payroll/get-targets', 'HRController@getTargets')->name('employee.get_targets')->middleware('permission:employee');

    // Payroll - Salary Update
    Route::get('payroll/salary-update', 'PayrollController@salaryUpdateIndex')->name('payroll.salary_update.index')->middleware('permission:salary_update');
    Route::post('payroll/salary-update/update', 'PayrollController@salaryUpdatePost')->name('payroll.salary_update.post')->middleware('permission:salary_update');
    Route::get('payroll/salary-update/datatable', 'PayrollController@salaryUpdateDatatable')->name('payroll.salary_update.datatable')->middleware('permission:salary_update');

    // Payroll - Salary Process
    Route::get('payroll/salary-process', 'PayrollController@salaryProcessIndex')->name('payroll.salary_process.index')->middleware('permission:salary_process');
    Route::post('payroll/salary-process', 'PayrollController@salaryProcessPost')->middleware('permission:salary_process');

    // Payroll - Leave
    Route::get('payroll/leave', 'PayrollController@leaveIndex')->name('payroll.leave.index')->middleware('permission:leave');
    Route::post('payroll/leave', 'PayrollController@leavePost')->middleware('permission:leave');

    // Payroll - holiday
    Route::get('payroll/holiday', 'PayrollController@holidayIndex')->name('payroll.holiday.index')->middleware('permission:holiday');
    Route::get('payroll/holiday/add', 'PayrollController@holidayAdd')->name('payroll.holiday_add')->middleware('permission:holiday');
    Route::post('payroll/holiday/add', 'PayrollController@holidayPost')->middleware('permission:holiday');
    Route::get('payroll/holiday/edit/{holiday}', 'PayrollController@holidayEdit')->name('payroll.holiday_edit')->middleware('permission:holiday');
    Route::post('payroll/holiday/edit/{holiday}', 'PayrollController@holidayEditPost')->middleware('permission:holiday');
    Route::get('payroll/holiday-datatable', 'PayrollController@holidayDatatable')->name('holiday.datatable')->middleware('permission:holiday');


    // Supplier
    Route::get('supplier', 'SupplierController@index')->name('supplier')->middleware('permission:supplier');
    Route::get('supplier/add', 'SupplierController@add')->name('supplier.add')->middleware('permission:supplier');
    Route::post('supplier/add', 'SupplierController@addPost')->middleware('permission:supplier');
    Route::get('supplier/edit/{supplier}', 'SupplierController@edit')->name('supplier.edit')->middleware('permission:supplier');
    Route::post('supplier/edit/{supplier}', 'SupplierController@editPost')->middleware('permission:supplier');

    // Product color
    Route::get('product_color', 'ProductColorController@index')->name('product_color');
    Route::get('product_color/add', 'ProductColorController@add')->name('product_color.add');
    Route::post('product_color/add', 'ProductColorController@addPost');
    Route::get('product_color/edit/{product_color}', 'ProductColorController@edit')->name('product_color.edit');
    Route::post('product_color/edit/{product_color}', 'ProductColorController@editPost');

    // Product size
    Route::get('product_size', 'ProductSizeController@index')->name('product_size');
    Route::get('product_size/add', 'ProductSizeController@add')->name('product_size.add');
    Route::post('product_size/add', 'ProductSizeController@addPost');
    Route::get('product_size/edit/{product_size}', 'ProductSizeController@edit')->name('product_size.edit');
    Route::post('product_size/edit/{product_size}', 'ProductSizeController@editPost');

    // Product Item
    Route::get('product-item', 'ProductItemController@index')->name('product_item')->middleware('permission:product_item');
    Route::get('product-item/add', 'ProductItemController@add')->name('product_item.add')->middleware('permission:product_item');
    Route::post('product-item/add', 'ProductItemController@addPost')->middleware('permission:product_item');
    Route::get('product-item/edit/{productItem}', 'ProductItemController@edit')->name('product_item.edit')->middleware('permission:product_item');
    Route::post('product-item/edit/{productItem}', 'ProductItemController@editPost')->middleware('permission:product_item');

    // Product Category
    Route::get('product-category', 'ProductCategoryController@index')->name('product_category')->middleware('permission:product_item');
    Route::get('product-category/add', 'ProductCategoryController@add')->name('product_category.add')->middleware('permission:product_item');
    Route::post('product-category/add', 'ProductCategoryController@addPost')->middleware('permission:product_item');
    Route::get('product-category/edit/{product_category}', 'ProductCategoryController@edit')->name('product_category.edit')->middleware('permission:product_item');
    Route::post('product-category/edit/{product_category}', 'ProductCategoryController@editPost')->middleware('permission:product_item');

    // Product
    Route::get('product', 'ProductController@index')->name('product')->middleware('permission:product');
    Route::get('product/add', 'ProductController@add')->name('product.add')->middleware('permission:product');
    Route::post('product/add', 'ProductController@addPost')->middleware('permission:product');
    Route::get('product/edit/{product}', 'ProductController@edit')->name('product.edit')->middleware('permission:product');
    Route::post('product/edit/{product}', 'ProductController@editPost')->middleware('permission:product');
    Route::get('product-datatable', 'ProductController@productDatatable')->name('product_datatable')->middleware('permission:product');

    // Product Description
    Route::get('description', 'ProductDescriptionController@index')->name('product_descrition');
    Route::get('description/add', 'ProductDescriptionController@add')->name('product_descrition.add');
    Route::post('description/store', 'ProductDescriptionController@Store')->name('description.store');
    Route::get('description/edit/{description}', 'ProductDescriptionController@edit')->name('description.edit');
    Route::post('description/edit/{description}', 'ProductDescriptionController@Update')->name('description.update');

    // Purchase Order
    Route::get('purchase-order', 'PurchaseController@purchaseOrder')->name('purchase_order.create')->middleware('permission:purchase_order');
    Route::post('purchase-order', 'PurchaseController@purchaseOrderPost')->middleware('permission:purchase_order');
    Route::get('purchase-order-edit/{order}', 'PurchaseController@purchaseOrderEdit')->name('purchase_order.edit')->middleware('permission:purchase_order');
    Route::post('purchase-order-edit/{order}', 'PurchaseController@purchaseOrderEditPost')->middleware('permission:purchase_order');
    Route::post('product/order/delete', 'PurchaseController@purchaseDelete')->name('purchase_order.delete')->middleware('permission:purchase_order');
    Route::get('purchase-product-json', 'PurchaseController@purchaseProductJson')->name('purchase_product.json');

    // Purchase Receipt
    Route::get('purchase-receipt', 'PurchaseController@purchaseReceipt')->name('purchase_receipt.all')->middleware('permission:purchase_receipt');
    Route::get('purchase-receipt/details/{order}', 'PurchaseController@purchaseReceiptDetails')->name('purchase_receipt.details')->middleware('permission:purchase_receipt');
    Route::get('purchase-receipt/print/{order}', 'PurchaseController@purchaseReceiptPrint')->name('purchase_receipt.print')->middleware('permission:purchase_receipt');
    Route::get('purchase-receipt/datatable', 'PurchaseController@purchaseReceiptDatatable')->name('purchase_receipt.datatable')->middleware('permission:purchase_receipt');
    Route::get('purchase-receipt/qr-code/{order}', 'PurchaseController@qrCode')->name('purchase_receipt.qr_code');
    Route::get('purchase-receipt/qr-code/print/{order}', 'PurchaseController@qrCodePrint')->name('purchase_receipt.qr_code_print');
    Route::get('purchase-single-receipt/qr-code/print/{order}', 'PurchaseController@qrSingleCodePrint')->name('purchase_receipt.qr_code_single_print');
    Route::get('purchase-receipt/payment/details/{payment}', 'PurchaseController@purchasePaymentDetails')->name('purchase_receipt.payment_details')->middleware('permission:purchase_receipt');
    Route::get('purchase-receipt/payment/print/{payment}', 'PurchaseController@purchasePaymentPrint')->name('purchase_receipt.payment_print')->middleware('permission:purchase_receipt');

    // Purchase Inventory
    Route::get('purchase-inventory', 'PurchaseController@purchaseInventory')->name('purchase_inventory.all')->middleware('permission:purchase_inventory');
    Route::get('purchase-inventory/datatable', 'PurchaseController@purchaseInventoryDatatable')->name('purchase_inventory.datatable')->middleware('permission:purchase_inventory');
    Route::get('purchase-inventory/details/datatable', 'PurchaseController@purchaseInventoryDetailsDatatable')->name('purchase_inventory.details.datatable')->middleware('permission:purchase_inventory');
    Route::get('purchase-inventory/details/{purchase_inventory}', 'PurchaseController@purchaseInventoryDetails')->name('purchase_inventory.details')->middleware('permission:purchase_inventory');
    Route::get('purchase-inventory/edit/{purchase_inventory}', 'PurchaseController@purchaseInventoryEdit')->name('purchase_inventory.edit')->middleware('permission:purchase_inventory');
    Route::post('purchase-inventory/edit/{purchase_inventory}', 'PurchaseController@purchaseInventoryEditPost')->middleware('permission:purchase_inventory');
    Route::get('barcode_generate', 'PurchaseController@purchaseInventoryBarcode')->name('barcode_generate');

    // Manually Stock
    Route::get('product-stock', 'ManuallyStockController@index')->name('product_stock')->middleware('permission:customer');
    Route::get('product-stock/add', 'ManuallyStockController@add')->name('product_stock.add')->middleware('permission:customer');
    Route::post('product-stock/add', 'ManuallyStockController@addPost')->middleware('permission:customer');
    Route::get('product-stock/edit/{purchase_inventory_log}', 'ManuallyStockController@edit')->name('product_stock.edit')->middleware('permission:customer');
    Route::post('product-stock/edit/{purchase_inventory_log}', 'ManuallyStockController@editPost')->middleware('permission:customer');
    Route::get('manual-stock/details/{purchase_inventory_log}', 'ManuallyStockController@manualStockDetails')->name('manual_stock.details')->middleware('permission:customer');
    Route::get('stock-receipt-print/{purchase_inventory_log}', 'ManuallyStockController@stockReceiptPrint')->name('stock_receipt.print')->middleware('permission:customer');
    Route::get('product-stock/datatable', 'ManuallyStockController@datatable')->name('product_stock.datatable')->middleware('permission:customer');
    Route::get('stock-order/product/details', 'ManuallyStockController@stockProductDetails')->name('stock_product.details')->middleware('permission:sales_order');
    Route::get('stock-order/product/invoice/{order}', 'ManuallyStockController@stockProductInvoice')->name('stock_product.invoice');
    Route::get('stock-order/product/barcode/{order}', 'ManuallyStockController@stockProductBarcode')->name('stock_product.barcode');
    Route::get('stock-product/barcode/print/{order}', 'ManuallyStockController@stockProductBarcodePrint')->name('stock_product.barcode_print');
    Route::get('stock-order/product/invoice/print/{order}', 'ManuallyStockController@stockProductInvoicePrint')->name('stock_product_invoice.print')->middleware('permission:sales_order');
    Route::get('stock-product/invoice/all', 'ManuallyStockController@stockProductInvoiceAll')->name('stock_product_invoice.all');

    //Purchase Stock Transfer
    Route::get('purchase-stock-transfer','PurchaseController@purchaseStockTransfer')->name('purchase_stock_transfer')->middleware('permission:purchase_inventory');
    Route::get('stock-transfer/datatable', 'PurchaseController@stockTransferDatatable')->name('stock_transfer.datatable')->middleware('permission:purchase_inventory');
    Route::get('stock-transfer/invoice', 'PurchaseController@stockTransferInvoice')->name('stock_transfer.invoice')->middleware('permission:purchase_inventory');
    Route::get('stock-transfer-challan/{order}','PurchaseController@stockTransferChallan')->name('stock_transfer_challan')->middleware('permission:purchase_inventory');
    Route::get('stock-transfer-details/{order}','PurchaseController@stockTransferDetails')->name('stock_transfer_details')->middleware('permission:purchase_inventory');
    Route::get('stock-transfer-edit/{order}','PurchaseController@stockTransferEdit')->name('stock_transfer_edit')->middleware('permission:purchase_inventory');
    Route::post('stock-transfer-edit/{order}','PurchaseController@stockTransferEditPost')->middleware('permission:purchase_inventory');
    Route::get('transfer-challan-print/{order}','PurchaseController@transferChallanPrint')->name('transfer_challan.print')->middleware('permission:purchase_inventory');
    Route::post('purchase-stock-transfer','PurchaseController@purchaseStockTransferPost')->middleware('permission:purchase_inventory');

    // Supplier Payment
    Route::get('supplier-payment', 'PurchaseController@supplierPayment')->name('supplier_payment.all')->middleware('permission:supplier_payment');
    Route::get('supplier-payment/get-orders', 'PurchaseController@supplierPaymentGetOrders')->name('supplier_payment.get_orders')->middleware('permission:supplier_payment');
    Route::get('supplier-payment/order-details', 'PurchaseController@supplierPaymentOrderDetails')->name('supplier_payment.order_details')->middleware('permission:supplier_payment');
    Route::post('supplier-payment/payment', 'PurchaseController@makePayment')->name('supplier_payment.make_payment')->middleware('permission:supplier_payment');

    // Customer
    Route::get('customer', 'CustomerController@index')->name('customer')->middleware('permission:customer');
    Route::get('customer/add', 'CustomerController@add')->name('customer.add')->middleware('permission:customer');
    Route::post('customer/add', 'CustomerController@addPost')->middleware('permission:customer');
    Route::get('customer/edit/{customer}', 'CustomerController@edit')->name('customer.edit')->middleware('permission:customer');
    Route::post('customer/edit/{customer}', 'CustomerController@editPost')->middleware('permission:customer');
    Route::get('customer/datatable', 'CustomerController@datatable')->name('customer.datatable')->middleware('permission:customer');

    // Sub Customer
    Route::get('sub-customer', 'SubCustomerController@index')->name('sub_customer')->middleware('permission:sub_customer');
    Route::get('sub-customer/add', 'SubCustomerController@add')->name('sub_customer.add')->middleware('permission:sub_customer');
    Route::post('sub-customer/add', 'SubCustomerController@addPost')->middleware('permission:sub_customer');
    Route::get('sub-customer/edit/{subCustomer}', 'SubCustomerController@edit')->name('sub_customer.edit')->middleware('permission:sub_customer');
    Route::post('sub-customer/edit/{subCustomer}', 'SubCustomerController@editPost')->middleware('permission:sub_customer');
    Route::get('sub-customer/datatable', 'SubCustprofit-and-lossomerController@datatable')->name('sub_customer.datatable')->middleware('permission:sub_customer');

    // Sales Order
    Route::get('sales-order', 'SaleController@salesOrder')->name('sales_order.create')->middleware('permission:sales_order');
    Route::post('sales-order', 'SaleController@salesOrderPost')->middleware('permission:sales_order');
    Route::get('sale-order/product/details', 'SaleController@saleProductDetails')->name('sale_product.details')->middleware('permission:sales_order');
    Route::get('sale-order/product/check/details', 'SaleController@saleProductCheckDetails')->name('sale_product_check.details')->middleware('permission:sales_order');
    Route::post('sale/order/delete', 'SaleController@saleDelete')->name('sale_order.delete')->middleware('permission:sales_order');
    Route::get('customer-json', 'SaleController@customerJson')->name('customer.json')->middleware('permission:sales_order');
    Route::get('supplier-json', 'SaleController@supplierJson')->name('supplier.json')->middleware('permission:sales_order');

    // Sale Wastage
    Route::get('sales-wastage', 'SaleController@salesWastage')->name('sales_wastage.create')->middleware('permission:sales_order');
    Route::post('sales-wastage', 'SaleController@salesWastagePost')->middleware('permission:sales_order');

    // Sale Receipt
    Route::get('sale-receipt/customer', 'SaleController@saleReceiptCustomer')->name('sale_receipt.customer.all')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/warehouse-pending/customer', 'SaleController@saleReceiptCustomerWarehousePending')->name('sale_receipt.customer.warehouse_pending.all')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/details/{order}', 'SaleController@saleReceiptDetails')->name('sale_receipt.details')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/print/{order}', 'SaleController@saleReceiptPrint')->name('sale_receipt.print')->middleware('permission:sale_receipt');
    Route::get('sale-requisition/print/{order}', 'SaleController@requisitionPrint')->name('sale_receipt.flat_requisition_print');
    Route::get('sale-super-requisition/print/{order}', 'SaleController@SuperRequisitionPrint')->name('sale_receipt.super_market_requisition_print');
    Route::get('sale-receipt/wpad-print/{order}', 'SaleController@saleReceiptWpadPrint')->name('sale_receipt.wpad_print')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/challan/print/{order}', 'SaleController@saleReceiptChallanPrint')->name('sale_receipt.chalan.print')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/challan/wpad-print/{order}', 'SaleController@saleReceiptChallanWpadPrint')->name('sale_receipt.chalan.wpad_print')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/customer/datatable', 'SaleController@saleReceiptCustomerDatatable')->name('sale_receipt.customer.datatable')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/customer/warehouse-pending/datatable', 'SaleController@saleReceiptCustomerWarehousePendingDatatable')->name('sale_receipt.customer.warehouse_pending.datatable')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/supplier/datatable', 'SaleController@saleReceiptSupplierDatatable')->name('sale_receipt.supplier.datatable')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/payment/details/{payment}', 'SaleController@salePaymentDetails')->name('sale_receipt.payment_details')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/payment/print/{payment}', 'SaleController@salePaymentPrint')->name('sale_receipt.payment_print')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/edit/{order}', 'SaleController@saleReceiptEdit')->name('sale_receipt.edit');
    Route::post('sale-receipt/edit/{order}', 'SaleController@saleReceiptEditPost');
    Route::get('sale-receipt/warehouse/pending/edit/{order}', 'SaleController@saleReceiptWarehousePendingEdit')->name('sale_receipt_warehouse_pending.edit');
    Route::post('sale-receipt/warehouse/pending/edit/{order}', 'SaleController@saleReceiptWarehousePendingEditPost');

    // Sale Wastage receipt
    Route::get('sale-wastage-receipt/customer', 'SaleController@saleWastageReceiptCustomer')->name('sale_wastage_receipt.customer.all')->middleware('permission:sale_receipt');
    Route::get('sale-wastage-receipt/customer/datatable', 'SaleController@saleWastageReceiptCustomerDatatable')->name('sale_wastage_receipt.customer.datatable')->middleware('permission:sale_receipt');

    // Sales Return
    Route::get('sales_return', 'SalesReturnController@index')->name('sales_return')->middleware('permission:customer');
    Route::get('sales_return/add', 'SalesReturnController@add')->name('sales_return.add')->middleware('permission:customer');
    Route::post('sales_return/add', 'SalesReturnController@addPost')->middleware('permission:customer');
    Route::get('sales_return/edit/{purchase_inventory_log}', 'SalesReturnController@edit')->name('sales_return.edit')->middleware('permission:customer');
    Route::get('sales_return/details/{purchase_inventory_log}', 'SalesReturnController@details')->name('sales_return.details')->middleware('permission:customer');
    Route::get('sales_return/receipt/print/{purchase_inventory_log}', 'SalesReturnController@receiptPrint')->name('sale_return_receipt.print')->middleware('permission:customer');
    Route::post('sales_return/edit/{purchase_inventory_log}', 'SalesReturnController@editPost')->middleware('permission:customer');
    Route::get('sales_return/datatable', 'SalesReturnController@datatable')->name('sales_return.datatable')->middleware('permission:customer');
    Route::get('sale-return/product/details', 'SalesReturnController@saleReturnProductDetails')->name('sale_return_product.details')->middleware('permission:customer');
    Route::get('return/invoice/{order}', 'SalesReturnController@returnInvoiceDetails')->name('return_invoice.details')->middleware('permission:sales_order');
    Route::get('return/invoice/barcode/{order}', 'SalesReturnController@returnInvoiceBarcode')->name('return_invoice.barcode');
    Route::get('return/invoice/barcode/print/{order}', 'SalesReturnController@returnInvoiceBarcodePrint')->name('return_invoice.barcode_print');
    Route::get('return/invoice/print/{order}', 'SalesReturnController@returnInvoicePrint')->name('return_invoice.print')->middleware('permission:sales_order');
    Route::get('product-return/invoice/all', 'SalesReturnController@productReturnInvoiceAll')->name('product_return_invoice.all');

    // Client Payment
    Route::get('client-payment/customer', 'SaleController@clientPaymentCustomer')->name('client_payment.customer.all')->middleware('permission:customer_payment');
    Route::get('client-payment/customer/datatable', 'SaleController@clientPaymentCustomerDatatable')->name('client_payment.customer.datatable')->middleware('permission:customer_payment');
    Route::get('customer-payments/{customer_id}', 'SaleController@customerPayments')->name('customer_payments');
    Route::post('customer/clear-all-data', 'SaleController@clearAllCustomerData')->name('customer.clear_all_data')->middleware('permission:customer');
    Route::get('all-pending-cheque', 'SaleController@allPendingCheque')->name('client_payment.all_pending_check');
    Route::get('admin-pending-cheque', 'SaleController@adminPendingCheque')->name('client_payment.admin_pending_check');
    Route::get('your-choice-pending-cheque', 'SaleController@yourChoicePendingCheque')->name('client_payment.your_choice_pending_check');
    Route::get('your-choice-plus-pending-cheque', 'SaleController@yourChoicePlusPendingCheque')->name('client_payment.your_choice_plus_pending_check');
    Route::get('manually-cheque-in', 'SaleController@manuallyChequeIn')->name('manually_chequeIn');
    Route::post('manually-cheque-in', 'SaleController@manuallyChequeInPost');
    Route::post('cheque-approved', 'SaleController@chequeApproved')->name('client_cheque.approved');
    Route::post('client-payment/update-date-only', 'SaleController@updateDateOnly')->name('client_payment.update_date_only');
    Route::post('pending/cheque/delete', 'SaleController@pendingChequeDelete')->name('pending_cheque.delete');
    Route::get('customer-payments/datatable', 'SaleController@CustomerPaymentsDatatable')->name('customer_payments.datatable');
    Route::get('client-payment/supplier', 'SaleController@clientPaymentSupplier')->name('client_payment.supplier.all')->middleware('permission:customer_payment');
    Route::get('client-payment/supplier/datatable', 'SaleController@clientPaymentSupplierDatatable')->name('client_payment.supplier.datatable')->middleware('permission:customer_payment');
    Route::get('client-payment/get-orders', 'SaleController@clientPaymentGetOrders')->name('client_payment.get_orders')->middleware('permission:customer_payment');
    Route::get('client-payment/get-orders/supplier', 'SaleController@clientPaymentGetOrdersSupplier')->name('client_payment.get_orders.supplier')->middleware('permission:customer_payment');
    Route::post('client-payment/payment', 'SaleController@makePayment')->name('client_payment.make_payment')->middleware('permission:customer_payment');
    Route::post('client-voucher/delete', 'SaleController@voucherDelete')->name('payment_voucher.delete')->middleware('permission:customer_payment');

    Route::post('client-payment/voucher', 'SaleController@voucherUpdate')->name('client_payment.voucher_update')->middleware('permission:customer_payment');
    Route::post('client-payment/return-customer', 'SaleController@processCustomerReturn')->name('client_payment.return_customer')->middleware('permission:customer_payment');
    Route::get('test-due-amount-calculation/{customer_id?}', 'SaleController@testDueAmountCalculation')->name('test_due_amount_calculation');
    Route::get('test-filtering-and-status/{customer_id?}', 'SaleController@testFilteringAndStatus')->name('test_filtering_and_status');
    Route::get('test-complete-payment-flow/{customer_id?}', 'SaleController@testCompletePaymentFlow')->name('test_complete_payment_flow');
    Route::get('test-individual-payment-records/{customer_id?}', 'SaleController@testIndividualPaymentRecords')->name('test_individual_payment_records');
    Route::get('test-total-received-amount/{customer_id?}', 'SaleController@testTotalReceivedAmount')->name('test_total_received_amount');


    // Account Head Type
    Route::get('account-head/type', 'AccountsController@accountHeadType')->name('account_head.type')->middleware('permission:account_head_type');
    Route::get('account-head/type/add', 'AccountsController@accountHeadTypeAdd')->name('account_head.type.add')->middleware('permission:account_head_type');
    Route::post('account-head/type/add', 'AccountsController@accountHeadTypeAddPost')->middleware('permission:account_head_type');
    Route::get('account-head/type/edit/{type}', 'AccountsController@accountHeadTypeEdit')->name('account_head.type.edit')->middleware('permission:account_head_type');
    Route::post('account-head/type/edit/{type}', 'AccountsController@accountHeadTypeEditPost')->middleware('permission:account_head_type');

    // Account Head Sub Type
    Route::get('account-head/sub-type', 'AccountsController@accountHeadSubType')->name('account_head.sub_type')->middleware('permission:account_head_sub_type');
    Route::get('account-head/sub-type/add', 'AccountsController@accountHeadSubTypeAdd')->name('account_head.sub_type.add')->middleware('permission:account_head_sub_type');
    Route::post('account-head/sub-type/add', 'AccountsController@accountHeadSubTypeAddPost')->middleware('permission:account_head_sub_type');
    Route::get('account-head/sub-type/edit/{subType}', 'AccountsController@accountHeadSubTypeEdit')->name('account_head.sub_type.edit')->middleware('permission:account_head_sub_type');
    Route::post('account-head/sub-type/edit/{subType}', 'AccountsController@accountHeadSubTypeEditPost')->middleware('permission:account_head_sub_type');

    // Transaction
    Route::get('transaction', 'AccountsController@transactionIndex')->name('transaction.all')->middleware('permission:project_wise_transaction');
    Route::get('transaction/datatable', 'AccountsController@transactionDatatable')->name('transaction.datatable')->middleware('permission:project_wise_transaction');
    Route::get('transaction/add', 'AccountsController@transactionAdd')->name('transaction.add')->middleware('permission:project_wise_transaction');
    Route::post('transaction/add', 'AccountsController@transactionAddPost');
    Route::post('transaction/edit', 'AccountsController@transactionEditPost')->name('transaction.edit_post')->middleware('permission:project_wise_transaction');
    Route::get('transaction/details/json', 'AccountsController@transactionDetailsJson')->name('transaction.details_json')->middleware('permission:project_wise_transaction');
    Route::get('transaction/details/{transaction}', 'AccountsController@transactionDetails')->name('transaction.details')->middleware('permission:project_wise_transaction');
    Route::get('transaction/print/{transaction}', 'AccountsController@transactionPrint')->name('transaction.print')->middleware('permission:project_wise_transaction');

    // Balance Transfer
    Route::get('balance-transfer/add', 'AccountsController@balanceTransferAdd')->name('balance_transfer.add')->middleware('permission:balance_transfer');
    Route::post('balance-transfer/add', 'AccountsController@balanceTransferAddPost')->middleware('permission:balance_transfer');

    // Proposals
    Route::get('proposal-create', 'ProposalController@proposalCreate')->name('proposal.create')->middleware('permission:proposal_create');
    Route::post('proposal-create', 'ProposalController@proposalStore')->middleware('permission:proposal_create');
    Route::get('proposal-edit/{proposal}', 'ProposalController@proposalEdit')->name('proposal.edit')->middleware('permission:proposal_edit');
    Route::post('proposal-edit/{proposal}', 'ProposalController@proposalUpdate')->middleware('permission:proposal_edit');
    Route::get('proposal/details/{proposal}', 'ProposalController@proposalDetails')->name('proposal.details')->middleware('permission:proposal_create');
    Route::get('proposal/print/{proposal}', 'ProposalController@proposalPrint')->name('proposal.print')->middleware('permission:proposal_create');
    Route::get('proposals', 'ProposalController@proposals')->name('proposals')->middleware('permission:proposals');
    Route::get('proposals-datatable', 'ProposalController@proposalsDatatable')->name('proposals_datatable')->middleware('permission:proposals');
    Route::get('all-proposals', 'ProposalController@allProposals')->name('all_proposals')->middleware('permission:all_proposals');
    Route::get('all-proposals-datatable', 'ProposalController@allProposalsDatatable')->name('all_proposals_datatable')->middleware('permission:all_proposals');

    // Report
    Route::get('report/purchase', 'ReportController@purchase')->name('report.purchase')->middleware('permission:purchase_report');
    Route::get('report/sale', 'ReportController@sale')->name('report.sale')->middleware('permission:sale_report');
    Route::get('report/sale-print', 'ReportController@salePrint')->name('report.sale_print')->middleware('permission:sale_report');
    Route::get('report/balance-summary', 'ReportController@balanceSummary')->name('report.balance_summary')->middleware('permission:balance_summary');
    Route::get('report/profit-and-loss', 'ReportController@profitAndLoss')->name('report.profit_and_loss');
    Route::get('report/bill-wise-profit-loss', 'ReportController@billWiseProfitLoss')->name('report.bill_wise_profit_loss')->middleware('permission:profit_and_loss');
    Route::get('report/ledger', 'ReportController@ledger')->name('report.ledger')->middleware('permission:ledger');
    Route::get('report/cashbook', 'ReportController@cashbook')->name('report.cashbook')->middleware('permission:cashbook');
    Route::get('report/monthly-expenditure', 'ReportController@monthlyExpenditure')->name('report.monthly_expenditure')->middleware('permission:monthly_expenditure');
    Route::get('report/bank-statement', 'ReportController@bankStatement')->name('report.bank_statement')->middleware('permission:bank_statement');
    Route::get('report/cash-statement', 'ReportController@cashStatement')->name('report.cash_statement');
    Route::get('report/client-statement', 'ReportController@clientStatement')->name('report.client_statement')->middleware('permission:client_summary');
    Route::get('report/party-ledger', 'ReportController@partyLedger')->name('report.party_ledger')->middleware('permission:client_summary');
    Route::get('report/branch-wise-sale-return', 'ReportController@branchWiseSaleReturn')->name('report.branch_wise_sale_return')->middleware('permission:purchase_report');
    Route::get('report/sub-client-statement', 'ReportController@subClientStatement')->name('report.sub_client_statement')->middleware('permission:sub_client_summary');
    Route::get('report/supplier-statement', 'ReportController@supplierStatement')->name('report.supplier_statement')->middleware('permission:supplier_report');
    Route::get('report/price-with-stock', 'ReportController@priceWithStock')->name('report.price.with.stock')->middleware('permission:price_with_stock');
    Route::get('report/price-without-stock', 'ReportController@priceWithOutStock')->name('report.price.without.stock')->middleware('permission:price_without_stock');
    Route::get('report/report-receive-payment', 'ReportController@receivePayment')->name('report.receive_payment');
    Route::get('report/report-trail-balance', 'ReportController@trailBalance')->name('report.trail_balance')->middleware('permission:trail_balance');
    Route::get('report/employee-list', 'ReportController@employeeList')->name('report.employee_list');
    Route::get('report/employee-attendance', 'ReportController@employeeAttendance')->name('report.employee_attendance')->middleware('permission:employee_attendance_report');
    Route::get('report/employee-target-customer-wise', 'ReportController@employeeTargetCustomerWise')->name('report.employee_target_customer_wise')->middleware('permission:client_summary');
    Route::get('report/monthly-crm', 'ReportController@monthlyCRM')->name('report.monthly_crm');
    Route::get('report/product-in-out', 'ReportController@productInOut')->name('report.product_in_out');
    Route::get('report/branch-wise-client', 'ReportController@branchWiseClient')->name('report.branch_wise_client')->middleware('permission:client_summary');
    Route::get('report/transaction', 'ReportController@transaction')->name('report.transaction')->middleware('permission:client_summary');

    // Common
    Route::get('get-product', 'CommonController@getProduct')->name('get_products');
    Route::get('get-branch', 'CommonController@getBranch')->name('get_branch');
    Route::get('get-bank-account', 'CommonController@getBankAccount')->name('get_bank_account');
    Route::get('get-sale-order', 'CommonController@getSaleOrder')->name('get_sale_order');
    Route::get('get-customer', 'CommonController@getCustomer')->name('get_customer');
    Route::get('order-details', 'CommonController@orderDetails')->name('get_order_details');
    Route::get('get-account-head-type', 'CommonController@getAccountHeadType')->name('get_account_head_type');
    Route::get('get-account-head-sub-type', 'CommonController@getAccountHeadSubType')->name('get_account_head_sub_type');
    Route::get('get-serial-suggestion', 'CommonController@getSerialSuggestion')->name('get_serial_suggestion');
    Route::get('get-product-item-suggestion', 'CommonController@getProductItemSuggestion')->name('get_productItem_suggestion');
    Route::get('get-category-item-suggestion', 'CommonController@getCategoryItemSuggestion')->name('get_categoryItem_suggestion');
    Route::get('get-received-by-suggestion', 'CommonController@getreceivedBySuggestion')->name('get_received_by_suggestion');
    Route::get('get-address-suggestion', 'CommonController@getAddressSuggestion')->name('get_customer_address_suggestion');
    Route::get('get-mobile-suggestion', 'CommonController@getMobileSuggestion')->name('get_customer_mobile_no_suggestion');
    Route::get('get-customer-name-suggestion', 'CommonController@getCustomerSuggestion')->name('get_customer_name_suggestion');
    Route::get('get-category-item-suggestion', 'CommonController@getCategoryItemSuggestion')->name('get_categoryItem_suggestion');
    Route::get('get-customer-due', 'CommonController@getCustomerDue')->name('customer_due');
    Route::get('get-designation', 'CommonController@getDesignation')->name('get_designation');
    Route::get('get-employee-details', 'CommonController@getEmployeeDetails')->name('get_employee_details');
    Route::get('get-month', 'CommonController@getMonth')->name('get_month');
    Route::get('get_employee_target', 'CommonController@get_employee_target')->name('get_employee_target');
    Route::get('get-month-salary-sheet', 'CommonController@getMonthSalaryMonth')->name('get_month_salary_sheet');
    Route::get('get-sub-customer', 'CommonController@getSubCustomer')->name('get_sub_customer');
    Route::get('get-inventory-details', 'CommonController@getInventoryDetails')->name('get_inventory_details');
    Route::get('get-warehouse-wise-product', 'CommonController@getWarehouseWiseProduct')->name('get_warehouse_wise_product');
    Route::post('get-unit-price', 'CommonController@getUnitPrice')->name('get_unit_price');
    Route::get('cash', 'CommonController@cash')->name('cash');
    Route::post('cash', 'CommonController@cashPost');

    Route::get('get-client', 'ClientManagementController@index')->name('get_clients');

    // Terms & Condition
    Route::get('terms-condition', 'TermsConditionController@termsCondition')->name('terms_condition');
    Route::post('terms-condition', 'TermsConditionController@store');


});
// Route::get('customer2subcustomer', 'CommonController@customer2subcustomer')->name('customer2subcustomer');

// Route::get('null-sub-customers', function(){
//     $collections = App\Model\SubCustomer::whereNull('sub_customer_old_id')->get();
//     return view('test', compact('collections'));
// });
Route::get('inventory_test', 'CommonController@inventoryTest');

Route::get('/login-failed', function () {
    return view('login_failed');
})->name('login_failed');

Route::get('/payment_info', function () {
    return view('payment_info');
})->name('payment_info');

Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});
