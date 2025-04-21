<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderDetailResource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Midtrans\Config;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Auth::user()->id;
        $orders = Order::with('items', 'items.product')->where('user_id', $user_id)->latest()->paginate(10);

        return Inertia::render('User/Dashboard', [
            'orders' => $orders
        ]);
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
        $order = $request->order;

        $user = $request->user();
        $userAddress = UserAddress::where('user_id', $user->id)->where('isMain', 1)->first();
        // Set your Merchant Server Key
        Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = false;
        // Set sanitization on (default)
        Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Config::$is3ds = true;

        $params = array(
            'transaction_details' => array(
                'order_id' => $order['order_id'] . '-' . time(), // Tambahkan timestamp untuk keunikan
                'gross_amount' => intval($order['gross_amount']),
            ),
            'customer_details' => array(
                'first_name' => 'Dear',
                'last_name' => $user->name,
                'email' => $user->email,
                'phone' => $userAddress->no_hp,
            ),
            'expiry' => array(
                'start_time' => date("Y-m-d H:i:s T"),
                'unit' => 'minutes',
                'duration' => 60, // Transaksi expired dalam 60 menit
            ),
        );
//        dd($order);

        $total_product = 0;
        $total_price = $order['gross_amount'] + $order['courir_price'];
        foreach ($order['items'] as $item){
            $total_product += $item['quantity'];
        }
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return Inertia::render('User/Payment',[
            'order' => $order,
            'total_product' => $total_product,
            'total_price' => $total_price,
            'token' => $snapToken,
        ]);
    }

    public function response(Request $request)
    {
        // Ambil data notifikasi dari Midtrans
        $notification = $request->all();

        // Log notifikasi untuk debugging
        Log::info('Midtrans Notification:', $notification);

        // Validasi notifikasi dengan Midtrans
        $serverKey = config('services.midtrans.server_key'); // Ambil server key dari konfigurasi
        $signatureKey = hash('sha512', $notification['order_id'] . $notification['status_code'] . $notification['gross_amount'] . $serverKey);

        if ($signatureKey !== $notification['signature_key']) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Proses notifikasi berdasarkan status pembayaran
        $transactionStatus = $notification['transaction_status'];
        $orderId = $notification['order_id'];

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            // Pembayaran berhasil
            Order::where('order_id', $orderId)->update(['status' => 'paid']);
        } elseif ($transactionStatus == 'pending') {
            // Pembayaran tertunda
            Order::where('order_id', $orderId)->update(['status' => 'pending']);
        } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            // Pembayaran gagal
            Order::where('order_id', $orderId)->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'Notification processed successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function invoice($id)
    {
        $order = Order::with('items', 'items.product')->where('id',$id)->first();
        $user = UserAddress::with('user')->where('id', $order->user_address_id)->first();

        $data = [];
        foreach ($order->items as $item){
                foreach ($item->product as $prod){
                    $data[] = [
                        'order_id' => $order->order_id,
                        'title' => $prod->title,
                        'gross_amount' => intval($order->gross_amount),
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'status' => $order->status,
                        'paid_at' => $order->paid_at,
                        'courir' => $order->courir,
                        'courir_type' => $order->courir_type,
                        'courir_price' => $order->courir_price,
                    ];
                }
         }
        $sub_total = intval($order->gross_amount);
        $total_price = $order->gross_amount + $order->courir_price;

        return Inertia::render('User/Invoice',[
            'order' => $order,
            'sub_total' => $sub_total,
            'total_price' => $total_price,
            'data' => $data,
            'user' => $user
        ]);
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
