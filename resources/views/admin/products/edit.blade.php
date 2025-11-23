@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Product</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <a href="{{ route('admin.products.index') }}">
                        <div class="text-tiny">Products</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Edit product</div></li>
            </ul>
        </div>

        <form class="tf-section-2" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $product->id }}">
            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product name" name="name" value="{{ $product->name }}" required>
                    <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                </fieldset>
                @error('name') <span class="alert alert-danger text-centre">{{ $message }}</span> @enderror

                <fieldset class="name">
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product slug" name="slug" value="{{ $product->slug }}" required>
                </fieldset>
                @error('slug') <span class="alert alert-danger text-centre">{{ $message }}</span> @enderror

                <div class="gap22 cols">
                    <fieldset class="category">
                        <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                        <div class="select">
                            <select name="category_id" required>
                                <option value="">Choose category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? "selected":""}}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                    @error('category_id') <span class="alert alert-danger text-centre">{{ $message }}</span> @enderror

                    <fieldset class="brand">
                        <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                        <div class="select">
                            <select name="brand_id" required>
                                <option value="">Choose brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? "selected":""}}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                    @error('brand_id') <span class="alert alert-danger text-centre">{{ $message }}</span> @enderror
                </div>

                <fieldset class="shortdescription">
                    <div class="body-title mb-10">Short Description <span class="tf-color-1">*</span></div>
                    <textarea class="mb-10 ht-150" name="short_description" required>{{ $product->short_description }}</textarea>
                </fieldset>
                @error('short_description') <span class="alert alert-danger text-centre">{{ $message }}</span> @enderror

                <fieldset class="description">
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span></div>
                    <textarea class="mb-10" name="description" required>{{ $product->description }}</textarea>
                </fieldset>
                @error('description') <span class="alert alert-danger text-centre">{{ $message }}</span> @enderror
            </div>

            <div class="wg-box">
                 {{-- ================= IMAGES SECTION ================= --}}

                <!-- ==============================
                MAIN IMAGE UPLOAD SECTION
                ============================== -->
                <fieldset style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
                <legend><strong>Main Product Image</strong></legend>

                <!-- Main Image Input -->
                <div class="form-group">
                    <label for="mainImage">Select Main Image</label>
                    <input type="file" id="mainImage" name="image" accept="image/*" />

                    <!-- Preview for Main Image -->
                    <div id="mainImagePreview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                        {{-- Show existing main image --}}
                        @if($product->image)
                        <img src="{{ asset($product->image) }}" alt="Current Main Image" 
                            style="width:120px; height:120px; object-fit:cover; border:1px solid #ccc; border-radius:8px;">
                        @endif
                    </div>
                </div>
                </fieldset>

                <!-- ==============================
                GALLERY IMAGES UPLOAD SECTION
                ============================== -->
                <fieldset style="border: 1px solid #ccc; padding: 15px;">
                <legend><strong>Gallery Images</strong></legend>

                <!-- Gallery Images Input -->
                <div class="form-group">
                    <label for="galleryImages">Select Gallery Images</label>
                    <input type="file" id="galleryImages" name="images[]" multiple accept="image/*" />

                    <!-- Preview for Gallery Images -->
                    <div id="galleryPreview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                        {{-- Show existing gallery images --}}
                        @if($product->images)
                        @foreach(json_decode($product->images, true) as $gallery)
                            <img src="{{ asset($gallery) }}" alt="Gallery Image" 
                                style="width:120px; height:120px; object-fit:cover; border:1px solid #ccc; border-radius:8px;">
                        @endforeach
                        @endif
                    </div>
                </div>
                </fieldset>
            @error('images') <span class="alert alert-danger">{{ $message }}</span> @enderror
          

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Regular Price <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter regular price" name="regular_price" value="{{ $product->regular_price }}" required>
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title mb-10">Sale Price <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter sale price" name="sale_price" value="{{ $product->sale_price }}" required>
                    </fieldset>
                </div>

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">SKU <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter SKU" name="SKU" value="{{ $product->SKU }}" required>
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title mb-10">Quantity <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter quantity" name="quantity" value="{{ $product->quantity }}" required>
                    </fieldset>
                </div>

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Stock</div>
                        <div class="select mb-10">
                            <select name="stock_status">
                                <option value="instock" {{ $product->stock_status == "instock" ? "selected":""}}>In Stock</option>
                                <option value="outofstock" {{ $product->stock_status == "outofstock" ? "selected":""}}>Out of Stock</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title mb-10">Featured</div>
                        <div class="select mb-10">
                            <select name="featured">
                                <option value="0" {{ $product->featured == "0" ? "selected":""}}>No</option>
                                <option value="1" {{ $product->featured == "1" ? "selected":""}}>Yes</option>
                            </select>
                        </div>
                    </fieldset>
                </div>

                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Update product</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- ==============================
 JAVASCRIPT PREVIEW HANDLER
============================== -->
<script>
  // ======== MAIN IMAGE PREVIEW ========
  const mainImageInput = document.getElementById('mainImage');
  const mainPreview = document.getElementById('mainImagePreview');

  mainImageInput.addEventListener('change', function() {
    mainPreview.innerHTML = ''; // Clear previous preview
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.style.width = '120px';
        img.style.height = '120px';
        img.style.objectFit = 'cover';
        img.style.border = '1px solid #ccc';
        img.style.borderRadius = '8px';
        mainPreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  });


  // ======== GALLERY IMAGES PREVIEW ========
  const galleryInput = document.getElementById('galleryImages');
  const galleryPreview = document.getElementById('galleryPreview');

  galleryInput.addEventListener('change', function() {
    galleryPreview.innerHTML = ''; // Clear previous previews
    const files = Array.from(this.files);

    files.forEach(file => {
      const reader = new FileReader();
      reader.onload = function(e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.style.width = '120px';
        img.style.height = '120px';
        img.style.objectFit = 'cover';
        img.style.border = '1px solid #ccc';
        img.style.borderRadius = '8px';
        galleryPreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  });

  
        // Slug auto-generation
        $("input[name='name']").on("change keyup", function() {
            $("input[name='slug']").val(StringToSlug($(this).val()));
        });


    function StringToSlug(Text) {
        return Text.toLowerCase()
            .replace(/[^\w ]+/g, '')   // remove non-word characters
            .replace(/ +/g, '-')       // replace spaces with -
            .replace(/-+/g, '-');      // collapse multiple dashes
    }

</script>
@endpush
