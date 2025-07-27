<?php

namespace App\Http\Controllers;

use App\Model\Product;
use App\Model\ProductDescription;
use App\Model\ProductItem;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class ProductDescriptionController extends Controller
{
    public function index() {
        $description = ProductDescription::with('productItem','product')->get();

        return view('purchase.product_description.all', compact('description'));
    }
    public function add(){
        $items = ProductItem::all();
        return view('purchase.product_description.add', compact('items'));
    }
    public function Store(Request $request){
        $request->validate([
            'product_item' => 'required|numeric',
            'product' => 'required|numeric|max:255',
            'description' => 'required|string|max:255',
            'status' => 'required'
        ]);


        $description = new ProductDescription();
        $description->product_item_id = $request->product_item;
        $description->product_id = $request->product;
        $description->description = $request->description;
        $description->status = $request->status;
        $description->save();

        return redirect()->route('product_descrition')->with('message', 'Description add successfully.');
    }
    public function edit(ProductDescription $description){
        $productItems = ProductItem::all();
        return view('purchase.product_description.edit', compact('description','productItems'));
    }
    public function Update(ProductDescription $description, Request $request) {
        $request->validate([
            'product_item' => 'required|numeric',
            'product' => 'required|numeric|max:255',
            'description' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $description->product_item_id = $request->product_item;
        $description->product_id = $request->product;
        $description->description = $request->description;
        $description->status = $request->status;
        $description->save();

        return redirect()->route('product_descrition')->with('message', 'Description Update successfully.');
    }
}
