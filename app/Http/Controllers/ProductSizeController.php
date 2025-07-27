<?php

namespace App\Http\Controllers;

use App\Model\ProductSize;
use Illuminate\Http\Request;

class ProductSizeController extends Controller
{
    public function index()
    {
        $product_sizes = ProductSize::all();

        return view('purchase.product_size.all', compact('product_sizes'));
    }

    public function add()
    {
        return view('purchase.product_size.add');
    }

    public function addPost(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $product_size = new ProductSize();
        $product_size->name = $request->name;
        $product_size->description = $request->description;
        $product_size->status = $request->status;
        $product_size->save();

        return redirect()->route('product_size')->with('message', 'Product size add successfully.');
    }

    public function edit(ProductSize $product_size)
    {
        return view('purchase.product_size.edit', compact('product_size'));
    }

    public function editPost(ProductSize $product_size, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $product_size->name = $request->name;
        $product_size->description = $request->description;
        $product_size->status = $request->status;
        $product_size->save();

        return redirect()->route('product_size')->with('message', 'Product size edit successfully.');
    }
}

