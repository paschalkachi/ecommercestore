<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        return view("admin.index");
    }

    // View brands function
    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    // Add brands function
    public function add_brands()
    {
        return view('admin.brands.add');
    }

    // Store brand
    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug'.$request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;

            $this->GenerateBrandThumbnail($image, $file_name);
            $brand->image = $file_name;
        }

        $brand->save();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand has been added successfully');
    }

    // Edit brand function
    public function brand_edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    // Update brand function
    public function brand_update(Request $request, $id)
{
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:brands,slug,' . $id,
        'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
    ]);

    $brand = Brand::findOrFail($id);
    $brand->name = $request->name;
    $brand->slug = Str::slug($request->name);

    if ($request->hasFile('image')) {
        if (File::exists(public_path('uploads/brands/' . $brand->image))) {
            File::delete(public_path('uploads/brands/' . $brand->image));
        }

        $image = $request->file('image');
        $file_extension = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;

        $this->GenerateBrandThumbnail($image, $file_name);
        $brand->image = $file_name;
    }

    $brand->save();

    return redirect()->route('admin.brands.index')
        ->with('success', 'Brand has been updated successfully');
}

    

    // Generate brand thumbnail
    public function GenerateBrandThumbnail($image, $image_name)
    {
        $destinationPath = public_path('/uploads/brands/');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top')
            ->resize(124, 124, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($destinationPath . '/' . $image_name);
    }

    // Delete Brand Function
    public function brand_delete($brand_id)
    {
        $brand = Brand::findOrFail($brand_id);
        if (File::exists(public_path('uploads/brands/' . $brand->image))) {
            File::delete(public_path('uploads/brands/' . $brand->image));
        }
        $brand->delete();
        return redirect()->route('admin.brands.index')
            ->with('status', 'Brand has been deleted successfully');
    }

    // View categories function
    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    //Add Category function
    public function category_add()
    {
        return view('admin.categories.add');
    }

    //Store Category function
    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;

            $this->GenerateCategoryThumbnails($image, $file_name);
            $category->image = $file_name;
        }

        $category->save();

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category has been added successfully');
    }

    // Generate Category Thumbnail
    public function GenerateCategoryThumbnails($image, $image_name)
    {
        $destinationPath = public_path('/uploads/categories/');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top')
            ->resize(124, 124, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($destinationPath . '/' . $image_name);
    }

    // category edit function
    public function category_edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

     // Update category function
    public function category_update(Request $request, $id)
{
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:categories,slug,' . $id,
        'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
    ]);

    $category = Category::findOrFail($id);
    $category->name = $request->name;
    $category->slug = Str::slug($request->name);

    if ($request->hasFile('image')) {
        if (File::exists(public_path('uploads/categories/' . $category->image))) {
            File::delete(public_path('uploads/categories/' . $category->image));
        }

        $image = $request->file('image');
        $file_extension = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;

        $this->GenerateBrandThumbnail($image, $file_name);
        $category->image = $file_name;
    }

    $category->save();

    return redirect()->route('admin.categories.index')
        ->with('status', 'category has been updated successfully');
}

// Delete Category Function
    public function category_delete($category_id)
    {
        $category = Category::findOrFail($category_id);
        if (File::exists(public_path('uploads/categories/' . $category->image))) {
            File::delete(public_path('uploads/categories/' . $category->image));
        }
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('status', 'Category has been deleted successfully');
    }

    // View products function
    public function products(){
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

      //Add products function
    public function product_add()
    {
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.products.add', compact('categories','brands'));
    }

   // Store Product function

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'short_description' => 'required|string',
            'description' => 'required|string',
            'regular_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'SKU' => 'required|string|max:50',
            'quantity' => 'required|integer',
            'stock_status' => 'required|string',
            'featured' => 'required|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $product = new Product();

        // ✅ Handle main product image
        if ($request->hasFile('image')) {
            $mainImage = $request->file('image');
            $mainImageName = time() . '_' . $mainImage->getClientOriginalName();

            // Move original image to uploads/products/
            $mainImage->move(public_path('uploads/products'), $mainImageName);

            // Generate thumbnails
            $this->GenerateProductThumbnails(public_path('uploads/products/' . $mainImageName), $mainImageName);

            $product->image = 'uploads/products/' . $mainImageName;
        }

        // ✅ Handle gallery images (if any)
        $galleryPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $galleryImage) {
                $galleryName = time() . '_' . $galleryImage->getClientOriginalName();

                // Move to uploads/products/
                $galleryImage->move(public_path('uploads/products'), $galleryName);

                // Generate thumbnail
                $this->GenerateProductThumbnails(public_path('uploads/products/' . $galleryName), $galleryName);

                $galleryPaths[] = 'uploads/products/' . $galleryName;
            }
        }

        // ✅ Save product details
        $product->fill([
            'name' => $request->name,
            'slug' => $request->slug,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'regular_price' => $request->regular_price,
            'sale_price' => $request->sale_price,
            'SKU' => $request->SKU,
            'quantity' => $request->quantity,
            'stock_status' => $request->stock_status,
            'featured' => $request->featured,
            'images' => json_encode($galleryPaths),
        ]);

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully!');
    }
    // public function product_store(Request $request, $id)
    // {
    //     $product = Product::findOrFail($id);
    //     // ✅ Validate inputs
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'slug' => 'required|string|max:255|unique:products,slug',
    //         'category_id' => 'required|exists:categories,id',
    //         'brand_id' => 'required|exists:brands,id',
    //         'short_description' => 'required|string',
    //         'description' => 'required|string',
    //         'regular_price' => 'required|numeric',
    //         'sale_price' => 'required|numeric',
    //         'SKU' => 'required|string|max:50',
    //         'quantity' => 'required|integer',
    //         'stock_status' => 'required|string',
    //         'featured' => 'required|boolean',
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
    //         'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096'
    //     ]);

    //     // ✅ Handle main image upload
    

    //         // Upload new main image
    //         $mainImage = $request->file('image');
    //         $mainImageName = time() . '_' . $mainImage->getClientOriginalName();
    //         $mainImage->move(public_path('uploads/products'), $mainImageName);

    //         // Generate thumbnail
    //         $this->GenerateProductThumbnails(public_path('uploads/products/' . $mainImageName), $mainImageName);

    //         // Save path
    //         $product->image = 'uploads/products/' . $mainImageName;
        

    //     // ✅ Handle gallery images
    //     $galleryPaths = json_encode($product->images, true) ?? [];

    
    //         // Delete old gallery images + thumbs
    //       if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $galleryImage) {
    //             $galleryName = time() . '_' . $galleryImage->getClientOriginalName();
    //             $galleryImage->move(public_path('uploads/products'), $galleryName);

    //             // Create thumbnail
    //             $this->GenerateProductThumbnails(public_path('uploads/products/thumbnails' . $galleryName), $galleryName);

    //             $galleryPaths[] = 'uploads/products/thumbnails' . $galleryName;
    //         }
            
    //     }


    //     // ✅ Store main image
    //     // $mainImageName = null;
    //     // if ($request->hasFile('image')) {
    //     //     $mainImage = $request->file('image');
    //     //     $mainImageName = time().'_'.$mainImage->getClientOriginalName();
    //     //     $mainImage->move(public_path('uploads/products'), $mainImageName);
    //     //     // $product->image = $mainImage->getClientOriginalName();
    //     //     $product->image = 'uploads/products/' . $mainImageName;

    //     //     // ✅ Generate thumbnail (pass file, not path)
    //     //     $this->GenerateProductThumbnails(public_path('uploads/products/thumbnails' . $mainImageName), $mainImageName);

    //     // }

    //     // ✅ Store gallery images
    //     // $galleryPaths = [];
    //     // if ($request->hasFile('images')) {
    //     //     foreach ($request->file('images') as $galleryImage) {
    //     //         $galleryName = time().'_'.$galleryImage->getClientOriginalName();
    //     //         $galleryImage->move(public_path('uploads/products'), $galleryName);
    //     //         $galleryPaths[] = 'uploads/products/'.$galleryName;

    //     //         // ✅ Generate thumbnails for gallery
    //     //        $this->GenerateProductThumbnails(public_path('uploads/products/' . $galleryName), $galleryName);

    //     //     }
    //     // }

    //     // ✅ Save product to database
    //     $product = new Product();
    //     $product->name = $request->name;
    //     $product->slug = $request->slug;
    //     $product->category_id = $request->category_id;
    //     $product->brand_id = $request->brand_id;
    //     $product->short_description = $request->short_description;
    //     $product->description = $request->description;
    //     $product->regular_price = $request->regular_price;
    //     $product->sale_price = $request->sale_price;
    //     $product->SKU = $request->SKU;
    //     $product->quantity = $request->quantity;
    //     $product->stock_status = $request->stock_status;
    //     $product->featured = $request->featured;
    //     $product->image = $mainImageName ? 'uploads/products/'.$mainImageName : null;
    //     $product->images = json_encode($galleryPaths);
    //     $product->save();

    //     // ✅ Redirect with success
    //     return redirect()->route('admin.products.index')->with('success', 'Product added successfully!');
    // }


    public function GenerateProductThumbnails($imageInput, $imageName)
    {
        $destinationPath = public_path('uploads/products');
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');

        // Ensure folders exist
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        if (!File::exists($destinationPathThumbnail)) {
            File::makeDirectory($destinationPathThumbnail, 0755, true);
        }

        // Create Image Manager
        $manager = new ImageManager(new Driver());

        // Handle both UploadedFile and string path
        if ($imageInput instanceof \Illuminate\Http\UploadedFile) {
            $img = $manager->read($imageInput->getRealPath());
        } else {
            $img = $manager->read($imageInput);
        }

        // Save resized full image (main product image)
        $img->cover(540, 689, 'top')
            ->save($destinationPath . '/' . $imageName);

        // Save thumbnail (small version)
        $thumbnailPath = $destinationPathThumbnail . '/' . $imageName;
    $img->resize(300, 300, function ($constraint) {
        $constraint->aspectRatio();
    })->save($thumbnailPath, 90); // quality 90%

    }

    // product edit function
        public function product_edit($id)
        {
            $product =Product::findOrFail($id);
            $categories = Category::select('id','name')->orderBy('name')->get();
            $brands = Brand::select('id','name')->orderBy('name')->get();
            return view('admin.products.edit', compact('product','categories','brands'));
        }

    // Update product function
    public function product_update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'short_description' => 'required|string',
            'description' => 'required|string',
            'regular_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'SKU' => 'required|string|max:50',
            'quantity' => 'required|integer',
            'stock_status' => 'required|string',
            'featured' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096'
        ]);

        // ✅ Handle main image upload
        if ($request->hasFile('image')) {
            // Delete old files
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            $oldThumb = public_path('uploads/products/thumbnails/' . basename($product->image));
            if (file_exists($oldThumb)) {
                unlink($oldThumb);
            }

            // Upload new main image
            $mainImage = $request->file('image');
            $mainImageName = time() . '_' . $mainImage->getClientOriginalName();
            $mainImage->move(public_path('uploads/products'), $mainImageName);

            // Generate thumbnail
            $this->GenerateProductThumbnails(public_path('uploads/products/' . $mainImageName), $mainImageName);

            // Save path
            $product->image = 'uploads/products/' . $mainImageName;
        }

        // ✅ Handle gallery images
        $galleryPaths = json_decode($product->images, true) ?? [];

        if ($request->hasFile('images')) {
            // Delete old gallery images + thumbs
            foreach ($galleryPaths as $oldPath) {
                if (file_exists(public_path($oldPath))) {
                    unlink(public_path($oldPath));
                }
                $oldThumb = public_path('uploads/products/' . basename($oldPath));
                if (file_exists($oldThumb)) {
                    unlink($oldThumb);
                }
            }

            $galleryPaths = []; // reset to new list
            foreach ($request->file('images') as $galleryImage) {
                $galleryName = time() . '_' . $galleryImage->getClientOriginalName();
                $galleryImage->move(public_path('uploads/products'), $galleryName);

                // Create thumbnail
                $this->GenerateProductThumbnails(public_path('uploads/products/thumbnails' . $galleryName), $galleryName);

                $galleryPaths[] = 'uploads/products/thumbnails' . $galleryName;
            }
        }

        // ✅ Update text fields
        $product->fill([
            'name' => $request->name,
            'slug' => $request->slug,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'regular_price' => $request->regular_price,
            'sale_price' => $request->sale_price,
            'SKU' => $request->SKU,
            'quantity' => $request->quantity,
            'stock_status' => $request->stock_status,
            'featured' => $request->featured,
        ]);

        // ✅ Only update gallery if new ones exist
        if ($request->hasFile('images')) {
            $product->images = json_encode($galleryPaths);
        }

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    // Delete Products function
    public function product_delete($id)
    {
        $product = Product::findorFail($id);
        if(File::exists(public_path('uploads/products').'/'. $product->image))
        {
            File::delete(public_path('uploads/products'. $product->image));
        }

        if(File::exists(public_path('uploads/products/thumbnails').'/'. $product->image))
        {
            File::delete(public_path('uploads/products/thumbnails'. $product->image));
        }


            // Delete old gallery images + thumbs
            $galleryPaths = json_decode($product->images, true) ?? [];
            foreach ($galleryPaths as $oldPath) {
                if (file_exists(public_path($oldPath))) {
                    unlink(public_path($oldPath));
                }
                $oldThumb = public_path('uploads/products/' . basename($oldPath));
                if (file_exists($oldThumb)) {
                    unlink($oldThumb);
                }
            }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success','');
    }


    //function to view coupons 
    public function coupons(){
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons.index', compact('coupons'));
    }

    // function to add coupons
    public function coupon_add(){
        return view('admin.coupons.add');
    }

    public function coupon_store(Request $request)
    {
        $request->validate([
            'code'=>'required',
            'type'=>'required',
            'value'=>'required|numeric',
            'cart_value'=>'required|numeric',
            'expiry_date'=>'required|date',
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;;
        $coupon->save();
        return redirect()->route('admin.coupons.index')
            ->with('status', 'Coupon has been added successfully');
    }

    // coupon edit function
    public function coupon_edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    // Update coupon function
    public function coupon_update(Request $request, $id)
    {
        $request->validate([
            'code'=>'required',
            'type'=>'required',
            'value'=>'required|numeric',
            'cart_value'=>'required|numeric',
            'expiry_date'=>'required|date',
        ]);

        $coupon = Coupon::findOrFail($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;;
        $coupon->save();
        return redirect()->route('admin.coupons.index')
            ->with('status', 'Coupon has been updated successfully');  
    }

    // coupon delete function
    public function coupon_delete($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return redirect()->route('admin.coupons.index')
            ->with('status', 'Coupon has been deleted successfully');
    }

}
    

    


