<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            $shipping = explode('-', $request->items['shipping']);
            $courir = $shipping[0];
            $courir_type = $shipping[1];
            $courir_price = $shipping[2];
            $total = $request->total;
            $userAddress = UserAddress::where('user_id', $user->id)->where('isMain', 1)->first();

            if (!$userAddress) {
                return redirect()->back()->with('error', 'Main address not found');
            }

            $order_id = 'order-'.now()->format('Y').$user->id.now()->format('Hm-s').rand(1, 10);
            $cartItems = Cart::with('product')->where(['user_id' => $user->id])->whereNull('paid_at')->get();

            if ($cartItems->isEmpty()) {
                return redirect()->back()->with('error', 'Cart is empty');
            }

            $order = new Order;
            $order->order_id = $order_id;
            $order->user_id = $user->id;
            $order->status = 'Unpaid';
            $order->gross_amount = $total;
            $order->courir = $courir;
            $order->courir_type = $courir_type;
            $order->courir_price = $courir_price;
            $order->created_by = $user->id;
            $order->user_address_id = $userAddress->id;

            if($order->save()) {
                foreach ($cartItems as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $cartItem->product->price,
                        'size' => $cartItem->size,
                        'color' => $cartItem->color,
                        'note' => $cartItem->note,
                    ]);
                    
                    $cart = Cart::findOrFail($cartItem->id);
                    $cart->delete();
                }

                Cache::forget('carts_global_count');

                $paymentData = [
                    'order_id' => $order->id,
                    'amount' => $total,
                    'status' => 'pending',
                    'type' => 'online',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ];

                Payment::create($paymentData);

                return redirect()->route('dashboard')->with('success', 'Checkout successfully');
            }

            return redirect()->route('cart.show')->with('error', 'Checkout failed');

        } catch (\Exception $e) {
            \Log::error('Checkout Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_KEY'));
        $sessionId = $request->get('session_id');
        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            if (!$session) {
                throw new NotFoundHttpException;
            }
            $order = Order::where('session_id', $session->id)->first();
            if (!$order) {
                throw new NotFoundHttpException();
            }
            if ($order->status === 'unpaid') {
                $order->status = 'paid';
                $order->save();
            }

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
