@extends('layouts.app')
@section('content')
<style>
  .filled-heart{
    color: orange;
  }
</style>

<main class="pt-90">
  <div class="mb-md-1 pb-md-3"></div>
  <section class="product-single container pt-4 pt-xl-5">
    <div class="row" style="margin-top: 50px;">
      
      <!-- Product Images Column -->
      <div class="col-lg-7">
        <div class="product-single__media" data-media-type="vertical-thumbnail">
          <div class="product-single__image">
            <div class="swiper-container">
              <div class="swiper-wrapper">
                <!-- Main Product Image -->
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy" class="h-auto" src="{{ asset($product->image) }}" width="674" height="674" alt="" />
                  <a data-fancybox="gallery" href="{{ asset($product->image) }}" data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_zoom" />
                    </svg>
                  </a>
                </div>

                <!-- Gallery Images -->
                @php
                  $galleryImages = is_array($product->images) ? $product->images : [];
                @endphp

                @foreach($galleryImages as $gallery)
                  <div class="swiper-slide product-single__image-item">
                    <img loading="lazy" class="h-auto" src="{{ asset($gallery) }}" width="674" height="674" alt="{{ $product->name }}" />
                    <a data-fancybox="gallery" href="{{ asset($gallery) }}" data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_zoom" />
                      </svg>
                    </a>
                  </div>
                @endforeach
              </div>

              <div class="swiper-button-prev">
                <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_prev_sm" />
                </svg>
              </div>
              <div class="swiper-button-next">
                <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_next_sm" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Thumbnail Images -->
          <div class="product-single__thumbnail">
            <div class="swiper-container">
              <div class="swiper-wrapper">
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy" class="h-auto" src="{{ asset($product->image) }}" width="104" height="104" alt="" />
                </div>

                @foreach($galleryImages as $gallery)
                  <div class="swiper-slide product-single__image-item">
                    <img loading="lazy" class="h-auto" src="{{ asset($gallery) }}" width="104" height="104" alt="{{ $product->name }}" />
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product Info Column -->
      <div class="col-lg-5">
        <h1 class="product-single__name">{{ $product->name }}</h1>

        <!-- Price -->
        <div class="product-single__price">
          <span class="current-price">
            @if($product->sale_price)
              <s>${{ $product->regular_price }}</s> ${{ $product->sale_price }}
            @else
              ${{ $product->regular_price }}
            @endif
          </span>
        </div>

        <!-- Short Description -->
        <div class="product-single__short-desc">
          <p>{{ $product->short_description }}</p>
        </div>

        <!-- Add to Cart -->
        @if(Cart::instance('cart')->content()->where('id',$product->id)->count() > 0)
          <a href="{{ route('cart.index') }}" class="btn btn-warning mb-3">Go to Cart</a>
        @else
          <form name="addtocart-form" method="post" action="{{ route('cart.add') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $product->id }}">
            <input type="hidden" name="name" value="{{ $product->name }}">
            <input type="hidden" name="price" value="{{ $product->sale_price ?: $product->regular_price }}">
            <div class="product-single__addtocart">
              <input type="number" name="quantity" value="1" min="1" class="form-control mb-2" style="width: 80px;">
              <button type="submit" class="btn btn-primary btn-addtocart" data-aside="cartDrawer">Add to Cart</button>
            </div>
          </form>
        @endif
      </div>
    </div>
  </section>

  <!-- Related Products -->
  <section class="products-carousel container">
    <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Related <strong>Products</strong></h2>
    <div id="related_products" class="position-relative" style="margin-top: 50px">
      <div class="swiper-container js-swiper-slider" data-settings='{
        "autoplay": false,
        "slidesPerView": 4,
        "slidesPerGroup": 4,
        "loop": true
      }'>
        <div class="swiper-wrapper">
          @foreach ($rproducts as $rproduct)
            @php
              $rgalleryImages = is_array($rproduct->images) ? $rproduct->images : [];
            @endphp
            <div class="swiper-slide product-card">
              <div class="pc__img-wrapper">
                <a href="{{ route('shop.product.details',['product_slug'=>$rproduct->slug]) }}">
                  <img loading="lazy" src="{{ asset($rproduct->image) }}" width="330" height="400" alt="{{ $rproduct->name }}" class="pc__img">

                  @foreach($rgalleryImages as $gallery)
                    <img loading="lazy" src="{{ asset($gallery) }}" width="330" height="400" alt="{{ $rproduct->name }}" class="pc__img pc__img-second">
                  @endforeach
                </a>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>
</main>
@endsection
