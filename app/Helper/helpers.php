<?php

if (! function_exists('nbrCalculation')) {
    function nbrCalculation(){
        // Always return 1 to show actual amounts without conversion
        // This ensures consistent financial data across all user roles
        return 1;
    }
}
if (! function_exists('nbrSellCalculation')) {
    function nbrSellCalculation($amount = 0){
        // Always return 0 to use actual prices without markup
        // This ensures consistent pricing across all user roles
        return 0;
    }
}
if (! function_exists('getSalePriceInventoryLog')) {
    function getSalePriceInventoryLog($orderId,$productItemId){

        return \App\Model\PurchaseInventoryLog::where('sales_order_id',$orderId)
            ->where('product_item_id',$productItemId)->first();

    }
}
if (! function_exists('getSaleReceiptTotal')) {
    function getSaleReceiptTotal($order){
        $totalAmount = 0;
        foreach($order->products as $key => $item){
            if(auth()->user()->role == 2){
                $totalAmount = ((($item->buy_price + nbrSellCalculation($item->buy_price)) * $item->quantity) + $order->transport_cost + $order->vat - $order->discount - $order->return_amount) + $order->paid;
            } else{
                $totalAmount = $order->total;

            }
        }
        return $totalAmount;
    }
}


