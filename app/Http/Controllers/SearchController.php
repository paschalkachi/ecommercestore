<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle product search via AJAX
     */
    public function index(Request $request)
    {
        // accept both frontend param names
        $keyword = $request->get('search-keyword', $request->get('query', ''));
        
        // Return HTML response that theme.js expects
        $html = '<div class="search-result">';
        
        if (empty($keyword)) {
            $html .= '<p class="text-center py-3">Enter a search term</p>';
        } else {
            // Query your products
            $products = Product::where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
                ->limit(10)
                ->get();
            
            if ($products->count() > 0) {
                $html .= '<ul class="search-products">';
                foreach ($products as $product) {
                    $price = $product->sale_price ?? $product->regular_price ?? 0;
                    // determine image URL - stored path is usually under public/uploads
                    $imgPath = $product->image ? asset($product->image) : asset('assets/images/no-image.png');

                    $html .= '<li class="search-products__item" style="display:flex;align-items:center;gap:10px;">';
                    $html .= '<a href="' . route('shop.product.details', $product->slug) . '" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">';
                    $html .= '<img src="' . $imgPath . '" alt="' . e($product->name) . '" style="width:48px;height:48px;object-fit:cover;border-radius:4px;"/>';
                    $html .= '<span class="search-products__meta">' . e($product->name) . '<br/><small>$' . number_format($price, 2) . '</small></span>';
                    $html .= '</a>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
            } else {
                $html .= '<p class="text-center py-3">No products found for "' . htmlspecialchars($keyword) . '"</p>';
            }
        }
        
        $html .= '</div>';
        
        // If the client expects JSON (admin JS sends dataType: 'json'), return structured JSON
        if ($request->wantsJson() || $request->ajax() || $request->get('format') === 'json') {
            $items = $products->map(function ($product) {
                $imageUrl = $product->image ? asset($product->image) : asset('assets/images/no-image.png');
                $imageName = $product->image ? basename($product->image) : null;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $imageName,
                    'image_url' => $imageUrl,
                    'price' => $product->sale_price ?? $product->regular_price ?? 0,
                ];
            });

            return response()->json($items);
        }

        return response($html, 200)->header('Content-Type', 'text/html; charset=utf-8');
    }
}
