<?php

if (! function_exists('nbrCalculation')) {
    function nbrCalculation(){
        if (auth()->user()->role == 1){
            return 1;
        }else{
            return .40;
        }
    }
}
if (! function_exists('nbrSellCalculation')) {
    function nbrSellCalculation($amount = 0){
        if (auth()->user()->role == 1){
            return 0;
        }else{
            return (10 / 100) * $amount;
        }
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


