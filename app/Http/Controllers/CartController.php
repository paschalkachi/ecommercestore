<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
  public function index()
  {
      $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
  }

  public function add_to_cart(Request $request)
  {
    // dd($request->all());
    Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
     return redirect()->back();
  }


  // function to increase the increase the cart quantity
  public function increase_cart_quantity($rowId)
  { 
    $product = Cart::instance('cart')->get($rowId);
    $qty = $product->qty + 1;
    Cart::instance('cart')->update($rowId, $qty);
    return redirect()->back();
  }

  // function to increase the decrease the cart quantity
  public function decrease_cart_quantity($rowId)
  { 
    $product = Cart::instance('cart')->get($rowId);
    $qty = $product->qty - 1;
    Cart::instance('cart')->update($rowId, $qty);
    return redirect()->back();
  }

  // function to remove a single cart item
  public function remove_item($rowId){
    Cart::instance('cart')->remove($rowId);
    return redirect()->back();
  }

  // function to empty the cart
  public function empty_cart()
  { 
    Cart::instance('cart')->destroy();
    return redirect()->back();
  }

  // function to apply coupon
  public function apply_coupon_code(Request $request)
  {   
      $coupon_code = $request->coupon_code;

      if(isset($coupon_code)){

          // Convert subtotal FIRST
          $cart_subtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));

          // Apply coupon logic
          $coupon = Coupon::where('code', $coupon_code)
              ->where('expiry_date', '>=', Carbon::today())
              ->where('cart_value', '<=', $cart_subtotal)
              ->first();

          if(!$coupon){
              return redirect()->back()->with('error', 'Invalid coupon code');
          }

          Session::put('coupon', [
              'code' => $coupon->code,
              'type' => $coupon->type,
              'value' => $coupon->value,
              'cart_value' => $coupon->cart_value,
          ]);

          $this->calculateDiscount();

          return redirect()->back()->with('success','Coupon has been applied');
      }

      return redirect()->back()->with('error', 'Please enter a valid coupon code');
  }


  public function calculateDiscount()
  {
      $discount = 0;
      $cart_subtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
      if(Session::has('coupon')){
      if(Session::get('coupon')['type'] == 'fixed'){
          $discount = Session::get('coupon')['value'];
      }
      else{
          $cart_subtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
          $discount = ($cart_subtotal * Session::get('coupon')['value']) / 100;
      }

      $subtotalAfterDiscount = $cart_subtotal - $discount;
      $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax'))/100;
      $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

      Session::put('discounts',[
          'discount' => number_format(floatVal($discount),2,'.',''),
          'subtotal' => number_format(floatVal($subtotalAfterDiscount),2,'.',''),
          'tax' => number_format(floatVal($taxAfterDiscount),2,'.',''),
          'total' => number_format(floatVal($totalAfterDiscount),2,'.',''),
      ]);
      }
  }

  public function remove_coupon_code()
  {
    Session::forget('coupon');
    Session::forget('discounts');
    return back()->with('success','Coupon has been removed');
  }
}
