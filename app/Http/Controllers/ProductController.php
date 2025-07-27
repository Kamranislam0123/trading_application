<?php

namespace App\Http\Controllers;

use App\Model\Product;
use App\Model\ProductItem;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index() {
        $products = Product::with('productItem')->get();

        return view('purchase.product.all', compact('products'));
    }

    public function add() {
        $productItems = ProductItem::orderBy('name')->get();

        return view('purchase.product.add', compact('productItems'));
    }

    public function addPost(Request $request) {
        $request->validate([
            'product_item' => 'required',
            'name' => 'required|string|max:255|unique:products',
            'status' => 'required'
        ]);

        // $image = 'img/no_image.png';

        // if ($request->image) {
        //     // Upload Image
        //     $file = $request->file('image');
        //     $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
        //     $destinationPath = 'public/uploads/product';
        //     $file->move($destinationPath, $filename);

        //     $image = 'uploads/product/'.$filename;
        // }

        $product = new Product();
        $product->product_item_id = $request->product_item;
        $product->name = $request->name;
        // $product->code = $request->code;
        // $product->image = $image;
        // $product->description = $request->description;
        $product->status = $request->status;
        $product->save();

        return redirect()->route('product')->with('message', 'Product add successfully.');
    }

    public function edit(Product $product) {
        $productItems = ProductItem::orderBy('name')->get();

        return view('purchase.product.edit', compact('productItems', 'product'));
    }

    public function editPost(Product $product, Request $request) {
        $request->validate([
            'product_item' => 'required',
            'name' => 'required|string|max:255|unique:products,name,'. $product->id,
            'status' => 'required',
            Rule::unique('posts')->ignore($product->id),
        ]);

        // $image = $product->image;

        // if ($request->image) {
        //     // Previous Photo
        //     if ($product->image != 'img/no_image.png') {
        //         $previousPhoto = public_path($product->image);
        //         unlink($previousPhoto);
        //     }

        //     // Upload Image
        //     $file = $request->file('image');
        //     $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
        //     $destinationPath = 'public/uploads/product';
        //     $file->move($destinationPath, $filename);

        //     $image = 'uploads/product/'.$filename;
        // }

        $product->product_item_id = $request->product_item;
        $product->name = $request->name;
        $product->unit_id = $request->unit;
        // $product->image = $image;
        // $product->catalog = $request->catalog;
        $product->status = $request->status;
        $product->save();

        return redirect()->route('product')->with('message', 'Product edit successfully.');
    }

    public function productDatatable() {
        $query = Product::with('unit','productItem');

        return DataTables::eloquent($query)
            ->addColumn('action', function(Product $product) {
                return '<a class="btn btn-info btn-sm" href="'.route("product.edit", ["product" => $product->id]).'">Edit</a> ';
            })
            ->addColumn('product_item', function(Product $product) {
                return  $product->productItem->name??'';
            })
            ->addColumn('unit', function(Product $product) {
                return  $product->unit->name??'';
            })
            ->addColumn('status', function(Product $product) {
                if ($product->status == 1)
                    return '<span class="label label-success">Active</span>';
                else
                    return '<span class="label label-danger">Inactive</span>';
            })
            ->rawColumns(['action','status'])
            ->toJson();
    }

}
