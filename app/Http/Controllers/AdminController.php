<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
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
            'slug' => 'required|unique:brands,slug.'.$request->id,
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


}


