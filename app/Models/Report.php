<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Report extends Model
{
    /**
     * Ambil data penjualan yang sudah dibayar.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getSalesReport()
    {
        return Order::with(['items.product']) // Relasi ke items dan product
            ->where('status', 'paid') // Hanya pesanan yang sudah dibayar
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
