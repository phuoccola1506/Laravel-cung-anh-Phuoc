<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    //
    public function index() {

        $hot_sale_products = Product::with('variants')
            ->where('active', 1)
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        $hot_phones = Product::with('variants')
            ->where('category_id', 1)
            ->where('active', 1)
            ->orderBy('views', 'desc')
            ->limit(12)
            ->get();

        $hot_laptops = Product::with('variants')
            ->where('category_id', 2)
            ->where('active', 1)
            ->orderBy('views', 'desc')
            ->limit(12)
            ->get();

        $categories = Category::where('active', 1)->orderBy('id')->get();

        return view('home.index', [
            'hot_sale_products' => $hot_sale_products,
            'hot_phones' => $hot_phones,
            'hot_laptops' => $hot_laptops,
            'categories' => $categories
        ]);
    }
}
