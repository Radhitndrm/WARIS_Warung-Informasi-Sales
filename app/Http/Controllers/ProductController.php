<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();

        return view('produk.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('produk.create', compact('categories'));
    }

    public function store(Request $request)
    {
        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'is_active' => true,
        ]);

        return redirect()->route('produk')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('produk.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->route('produk')
            ->with('success', 'Produk berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('produk')
            ->with('success', 'Produk berhasil dihapus');
    }
}