<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
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

  // function to view checkout page
    public function checkout()
    {
        if(!Auth::check())
        {
            return redirect()->route('login');
        }
        else{
        $address = Address::where('user_id',Auth::user()->id)->where('isdefault',1)->first();
        return view('checkout',compact('address'));
        }
    }

    // function to place an order
    public function place_an_order(Request $request)
    {
      $user_id = Auth::user()->id;
      $address = Address::where('user_id',$user_id)->where('isdefault',true)->first();

      if(!$address)
      {
        $request->validate([
          'name' => 'required|max:100',
          'phone' => 'required|numeric|digits:10',
          'zip' => 'required|numeric|digits:6',
          'state' => 'required',
          'city' => 'required',
          'address' => 'required',
          'locality' => 'required',
          'landmark' => 'required',
        ]);

        $address = new Address();
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->zip = $request->zip;
        $address->state = $request->state;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->locality = $request->locality;
        $address->landmark = $request->landmark;
        $address->country = 'Nigeria';
        $address->user_id = $user_id;
        $address->isdefault = true;
        $address->save();
      }

      $this->setAmountforCheckout();

      $order = new Order();
      $order->user_Id = $user_id;
      $order->sub_total = Session::get('checkout')['sub_total'];
      $order->discount = Session::get('checkout')['discount'];
      $order->tax = Session::get('checkout')['tax'];
      $order->total = Session::get('checkout')['total'];
      $order->name = $address->name;
      $order->phone = $address->phone;
      $order->locality = $address->locality;
      $order->address = $address->address;
      $order->city = $address->city;
      $order->state = $address->state;
      $order->country = $address->country;
      $order->landmark = $address->landmark;
      $order->zip = $address->zip;
      $order->save();

      foreach(Cart::instance('cart')->content() as $item)
      {
        $orderItem = new Order();
        $orderItem->product_id = $item->id;
        $orderItem->order_id = $order->id;
        $orderItem->price_id = $item->price;
        $orderItem->quantity = $item->qty;
        $orderItem->save();
      }

      if($request->mode == "card")
      {
        //
      }

      elseif($request->mode == "paypal")
      {
        //
      }

     elseif($request->mode == "cod")
    {
      $transaction = new Transaction();
      $transaction->user_id = $user_id;
      $transaction->order_id = $order->id;
      $transaction->mode = $request->mode;
      $transaction->status = "pending";
      $transaction->save();
    }

      Cart::instance('cart')->destroy();
      Session::forget('checkout');
      Session::forget('coupon');
      Session::forget('discounts');
      Session::put('order_id',$order->id);
      return redirect()->route('cart.order-confirmation');
    }

    public function setAmountforCheckout()
{
    if (Cart::instance('cart')->content()->count() == 0) {
        Session::forget('checkout');
        return;
    }

    $cartSubtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
    $taxRate = config('cart.tax'); // e.g. 7.5
    $tax = ($cartSubtotal * $taxRate) / 100;
    $total = $cartSubtotal + $tax;

    // If coupon applied, use discount values
    if (Session::has('coupon') && Session::has('discounts')) {
        Session::put('checkout', [
            'discount'  => Session::get('discounts')['discount'],
            'sub_total' => Session::get('discounts')['subtotal'], 
            'tax'       => Session::get('discounts')['tax'],
            'total'     => Session::get('discounts')['total'],
        ]);

    } else {
        // Normal checkout without coupon
        Session::put('checkout', [
            'discount'  => 0,
            'sub_total' => number_format($cartSubtotal, 2, '.', ''),
            'tax'       => number_format($tax, 2, '.', ''),
            'total'     => number_format($total, 2, '.', ''),
        ]);
    }
}


    // function to confirm order
    public function order_confirmation()
    {
      if(Session::has('order_id'))
      {
        $order = Order::find(Session::get('order_id'));
        return view('order-confirmation',compact('order'));
      }
      return redirect()->route('cart.index');
      
    }
}


