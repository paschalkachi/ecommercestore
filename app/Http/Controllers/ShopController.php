<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
   public function index(Request $request)
{
    $size = $request->query("size") ?? 12;
    $order = $request->query("order") ?? -1;
    $if_brands = $request->query("brands") ?? '';
    $if_categories = $request->query("categories") ?? '';
    $min_price = $request->query('min') ? $request->query('min') : 1;
    $max_price = $request->query('max') ? $request->query('max') : 5000;
    switch($order)
    {
        case 1:  $o_column = "created_at"; $o_order = "DESC"; break;
        case 2:  $o_column = "created_at"; $o_order = "ASC"; break;
        case 3:  $o_column = "sale_price"; $o_order = "ASC"; break;
        case 4:  $o_column = "sale_price"; $o_order = "DESC"; break;
        default: $o_column = "id";          $o_order = "DESC";
    }

    $brands = Brand::orderBy("name", "ASC")->get();
    $categories = Category::orderBy("name", "ASC")->get();

    $products = Product::query();

    // Filter by brands if provided
    if (!empty($if_brands)) {
        $brandIds = explode(',', $if_brands);
        $products->whereIn('brand_id', $brandIds);
    }

    // Filter by categories if provided
    if (!empty($if_categories)) {
        $categoryIds = explode(',', $if_categories);
        $products->whereIn('category_id', $categoryIds);
    }

    // Filter by price range
    $products->where(function($query) use ($min_price, $max_price) {
        $query->whereBetween('regular_price', [$min_price, $max_price])
            ->orWhere(function($q) use ($min_price, $max_price) {
                $q->whereNotNull('sale_price')
                    ->whereBetween('sale_price', [$min_price, $max_price]);
            });
    });

    // Apply ordering
    $products = $products->orderBy($o_column, $o_order)
                        ->paginate($size);

    // Return to view
    return view("shop", compact(
        "products", "size", "order", "brands", "if_brands",
        "categories", "if_categories", "min_price", "max_price"
    ));
    }


    public function product_details($product_slug)
    {
        $product = Product::where("slug", $product_slug)->first();
        $rproducts = Product::where("slug", "<>", $product_slug)->get()->take(8);
        return view("details", compact("product", "rproducts"));
    }
}
