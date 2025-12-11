<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Services\PaystackService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as FacadesLog;
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
        Cart::instance('cart')
            ->add($request->id, $request->name, $request->quantity, $request->price)
            ->associate('App\Models\Product');

        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = max(1, $product->qty - 1);
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;

        if (!$coupon_code) {
            return back()->with('error', 'Please enter a valid coupon code.');
        }

        $cart_subtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));

        $coupon = Coupon::where('code', $coupon_code)
            ->where('expiry_date', '>=', Carbon::today())
            ->where('cart_value', '<=', $cart_subtotal)
            ->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        Session::put('coupon', [
            'code'       => $coupon->code,
            'type'       => $coupon->type,
            'value'      => $coupon->value,
            'cart_value' => $coupon->cart_value,
        ]);

        $this->calculateDiscount();

        return back()->with('success', 'Coupon applied successfully.');
    }

    public function calculateDiscount()
    {
        if (!Session::has('coupon')) return;

        $cart_subtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
        $discount = 0;

        if (Session::get('coupon')['type'] == 'fixed') {
            $discount = Session::get('coupon')['value'];
        } else {
            $discount = ($cart_subtotal * Session::get('coupon')['value']) / 100;
        }

        $subtotalAfterDiscount = $cart_subtotal - $discount;
        $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
        $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

        Session::put('discounts', [
            'discount' => number_format($discount, 2, '.', ''),
            'subtotal' => number_format($subtotalAfterDiscount, 2, '.', ''),
            'tax'      => number_format($taxAfterDiscount, 2, '.', ''),
            'total'    => number_format($totalAfterDiscount, 2, '.', ''),
        ]);
    }

    public function remove_coupon_code()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return back()->with('success', 'Coupon removed.');
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $address = Address::where('user_id', Auth::id())->where('isdefault', true)->first();

        return view('checkout', compact('address'));
    }

    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

  public function place_an_order(Request $request)
{
    $request->validate([
        'method' => 'required|in:cod,paypal,card,paystack',
    ]);

    if (Cart::instance('cart')->count() == 0) {
        return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
    }

    $user_id = Auth::id();

    // Handle address
    $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();
    if (!$address) {
        $request->validate([
            'name'     => 'required|max:100',
            'phone'    => 'required|numeric',
            'zip'      => 'required',
            'state'    => 'required',
            'city'     => 'required',
            'address'  => 'required',
            'locality' => 'required',
            'landmark' => 'required',
        ]);

        $address = Address::create([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'zip'       => $request->zip,
            'state'     => $request->state,
            'city'      => $request->city,
            'address'   => $request->address,
            'locality'  => $request->locality,
            'landmark'  => $request->landmark,
            'country'   => 'Nigeria',
            'user_id'   => $user_id,
            'isdefault' => true,
        ]);
    }

    // Compute totals
    $this->setAmountforCheckout();

    DB::beginTransaction();
    try {
        // Save Order
        $order = Order::create([
            'user_id'   => $user_id,
            'sub_total' => Session::get('checkout')['sub_total'],
            'discount'  => Session::get('checkout')['discount'],
            'tax'       => Session::get('checkout')['tax'],
            'total'     => Session::get('checkout')['total'],
            'name'      => $address->name,
            'phone'     => $address->phone,
            'locality'  => $address->locality,
            'address'   => $address->address,
            'city'      => $address->city,
            'state'     => $address->state,
            'country'   => $address->country,
            'landmark'  => $address->landmark,
            'zip'       => $address->zip,
        ]);

        // Save Order Items
        foreach (Cart::instance('cart')->content() as $item) {
            OrderItem::create([
                'product_id' => $item->id,
                'order_id'   => $order->id,
                'price'      => $item->price,
                'quantity'   => $item->qty,
            ]);
        }

        // Create Transaction
        $transaction = Transaction::create([
            'user_id'          => $user_id,
            'order_id'         => $order->id,
            'gateway'          => $request->input('method'),
            'method'           => $request->input('method'),
            'reference'        => null,
            'status'           => 'pending',
            'gateway_response' => null,
        ]);

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Order failed: ' . $e->getMessage());
    }

    // ================================
    // HANDLE PAYMENT METHODS
    // ================================
    if ($request->input('method') === 'paystack') {
    try {
        $paymentData = [
            'email' => Auth::user()->email,
            'amount' => $order->total * 100,
            'metadata' => [
                'name' => $address->name,
                'phone' => $address->phone,
                'order_id' => $order->id,
            ],
            'callback_url' => route('payment.callback'),
        ];

        $response = $this->paystack->initializeTransaction($paymentData);

        // Update transaction reference
        $transaction->update(['reference' => $response['data']['reference']]);

        return redirect($response['data']['authorization_url']);

    } catch (\GuzzleHttp\Exception\ClientException $e) {
        return back()->with('error', 'Paystack error: ' . $e->getMessage());
    } catch (\Exception $e) {
        return back()->with('error', 'Unexpected error: ' . $e->getMessage());
    }
}

    // COD or other offline payments
    Cart::instance('cart')->destroy();
    Session::forget(['checkout', 'coupon', 'discounts']);
    Session::put('order_id', $order->id);

    return redirect()->route('cart.order-confirmation');
}

    public function paystackCallback(Request $request)
{
    $reference = $request->query('reference');

    if (!$reference) {
        return redirect()->route('cart.index')->with('error', 'Payment reference missing.');
    }

    // Find the transaction by reference
    $transaction = Transaction::where('reference', $reference)->first();

    if (!$transaction) {
        return redirect()->route('cart.index')->with('error', 'Transaction not found.');
    }

    try {
        $response = $this->paystack->verifyTransaction($reference);

        // Save raw response
        $transaction->gateway_response = json_encode($response);

        if ($response['status'] && $response['data']['status'] === 'success') {
            $transaction->status = 'approved';
            $transaction->save();

            // Clear cart
            Cart::instance('cart')->destroy();
            Session::forget(['checkout', 'coupon', 'discounts']);
            Session::put('order_id', $transaction->order_id);

            return redirect()->route('cart.order-confirmation')
                             ->with('success', 'Payment successful!');
        } else {
            $transaction->status = 'declined';
            $transaction->save();

            return redirect()->route('cart.index')
                             ->with('error', 'Payment failed or cancelled.');
        }

    } catch (\Exception $e) {
        return redirect()->route('cart.index')
                         ->with('error', 'Payment verification failed: ' . $e->getMessage());
    }
}

    public function setAmountforCheckout()
    {
        if (Cart::instance('cart')->count() == 0) {
            Session::forget('checkout');
            return;
        }

        $cartSubtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
        $taxRate = config('cart.tax');
        $tax = ($cartSubtotal * $taxRate) / 100;
        $total = $cartSubtotal + $tax;

        if (Session::has('coupon') && Session::has('discounts')) {
            Session::put('checkout', [
                'discount'  => Session::get('discounts')['discount'],
                'sub_total' => Session::get('discounts')['subtotal'],
                'tax'       => Session::get('discounts')['tax'],
                'total'     => Session::get('discounts')['total'],
            ]);
        } else {
            Session::put('checkout', [
                'discount'  => 0,
                'sub_total' => number_format($cartSubtotal, 2, '.', ''),
                'tax'       => number_format($tax, 2, '.', ''),
                'total'     => number_format($total, 2, '.', ''),
            ]);
        }
    }

    public function order_confirmation()
    {
        if (Session::has('order_id')) {
            // Eager-load related order items and transaction so the view can safely access them
            $order = Order::with(['orderItem.product', 'transaction'])->find(Session::get('order_id'));
            return view('order-confirmation', compact('order'));
        }

        return redirect()->route('cart.index');
    }
}
