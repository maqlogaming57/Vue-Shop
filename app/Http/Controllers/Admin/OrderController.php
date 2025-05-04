<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['items.product'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Order/Index', [
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function invoice($id)
    {
        $order = Order::with('items', 'items.product', 'items.product.category', 'items.product.brand', 'items.product.product_images')
            ->where('id',$id)
            ->first();
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
       return Inertia::render('Admin/Order/Invoice', [
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

    /**
     * Update AWB for the specified order.
     */
    public function updateAwb(Request $request, Order $order)
    {
        $validated = $request->validate([
            'awb' => 'required|string|max:100'
        ]);

        try {
            $order->update([
                'awb' => $validated['awb']
            ]);

            return redirect()->back()->with('success', 'AWB number has been updated');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update AWB number');
        }
    }
}
