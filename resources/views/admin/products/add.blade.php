@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add Product</h3>
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
                <li><div class="text-tiny">Add product</div></li>
            </ul>
        </div>

        <form class="tf-section-2 form-add-product"
              method="POST"
              enctype="multipart/form-data"
              action="{{ route('admin.products.store') }}">
            @csrf

            {{-- ================= MAIN INFO ================= --}}
            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product name"
                           name="name" value="{{ old('name') }}" required>
                    <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                </fieldset>
                @error('name') <span class="alert alert-danger">{{ $message }}</span> @enderror

                <fieldset class="name">
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product slug"
                           name="slug" value="{{ old('slug') }}" required>
                </fieldset>
                @error('slug') <span class="alert alert-danger">{{ $message }}</span> @enderror

                <div class="gap22 cols">
                    <fieldset class="category">
                        <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                        <div class="select">
                            <select name="category_id" required>
                                <option value="">Choose category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                    @error('category_id') <span class="alert alert-danger">{{ $message }}</span> @enderror

                    <fieldset class="brand">
                        <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                        <div class="select">
                            <select name="brand_id" required>
                                <option value="">Choose brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                    @error('brand_id') <span class="alert alert-danger">{{ $message }}</span> @enderror
                </div>

                <fieldset class="shortdescription">
                    <div class="body-title mb-10">Short Description <span class="tf-color-1">*</span></div>
                    <textarea class="mb-10 ht-150" name="short_description" required>{{ old('short_description') }}</textarea>
                </fieldset>
                @error('short_description') <span class="alert alert-danger">{{ $message }}</span> @enderror

                <fieldset class="description">
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span></div>
                    <textarea class="mb-10" name="description" required>{{ old('description') }}</textarea>
                </fieldset>
                @error('description') <span class="alert alert-danger">{{ $message }}</span> @enderror
            </div>

            {{-- ================= IMAGES AND PRODUCT DETAILS SECTION ================= --}}
            <div class="wg-box">
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
                    <div id="mainImagePreview" 
                        style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
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
                    <div id="galleryPreview" 
                        style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                    </div>
                </div>
                </fieldset>
            @error('images') <span class="alert alert-danger">{{ $message }}</span> @enderror
           
            {{-- ================= PRODUCT DETAILS ================= --}}
            <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Regular Price <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" name="regular_price"
                               placeholder="Enter regular price" value="{{ old('regular_price') }}" required>
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title mb-10">Sale Price <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" name="sale_price"
                               placeholder="Enter sale price" value="{{ old('sale_price') }}" required>
                    </fieldset>
                </div>

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">SKU <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" name="SKU"
                               placeholder="Enter SKU" value="{{ old('SKU') }}" required>
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title mb-10">Quantity <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" name="quantity"
                               placeholder="Enter quantity" value="{{ old('quantity') }}" required>
                    </fieldset>
                </div>

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Stock</div>
                        <div class="select mb-10">
                            <select name="stock_status">
                                <option value="instock">In Stock</option>
                                <option value="outofstock">Out of Stock</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title mb-10">Featured</div>
                        <div class="select mb-10">
                            <select name="featured">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </fieldset>
                </div>

                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Add product</button>
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


