<?php

namespace App\Http\Controllers;

use App\Model\ProductColor;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    public function index()
    {
        $product_colors = ProductColor::all();

        return view('purchase.product_color.all', compact('product_colors'));
    }

    public function add()
    {
        return view('purchase.product_color.add');
    }

    public function addPost(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $product_color = new ProductColor();
        $product_color->name = $request->name;
        $product_color->description = $request->description;
        $product_color->status = $request->status;
        $product_color->save();

        return redirect()->route('product_color')->with('message', 'Product color add successfully.');
    }

    public function edit(ProductColor $product_color)
    {
        return view('purchase.product_color.edit', compact('product_color'));
    }

    public function editPost(ProductColor $product_color, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $product_color->name = $request->name;
        $product_color->description = $request->description;
        $product_color->status = $request->status;
        $product_color->save();

        return redirect()->route('product_color')->with('message', 'Product color edit successfully.');
    }
}
