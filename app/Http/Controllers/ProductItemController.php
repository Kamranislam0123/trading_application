<?php

namespace App\Http\Controllers;

use App\Model\ProductItem;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class ProductItemController extends Controller
{
    public function index() {
        $productItems = ProductItem::all();
//        foreach ($productItems as $productItem){
//            $productItem->type = 1;
//            $productItem->save();
//        }
        return view('purchase.product_item.all', compact('productItems'));
    }

    public function add() {
        return view('purchase.product_item.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $productItem = new ProductItem();
        $productItem->name = $request->name;
        $productItem->unit_id = $request->unit;
        $productItem->description = $request->description;
        $productItem->status = $request->status;
        $productItem->save();

        return redirect()->route('product_item')->with('message', 'Product item add successfully.');
    }

    public function edit(ProductItem $productItem) {
        return view('purchase.product_item.edit', compact('productItem'));
    }

    public function editPost(ProductItem $productItem, Request $request) {

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable',
            'status' => 'required'
        ]);
        //$image = '';
        if ($request->image) {
            // Upload Image
            $file = $request->file('image');
            $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/product_image';
            $file->move($destinationPath, $filename);

            $image = 'uploads/product_image/' . $filename;
        }
        //dd($productItem->image);

        $productItem->name = $request->name;
        $productItem->unit_id = $request->unit;
        $productItem->description = $request->description;
        $productItem->image = $image;
        $productItem->status = $request->status;
        $productItem->save();

        return redirect()->route('product_item')->with('message', 'Product item edit successfully.');
    }
}
