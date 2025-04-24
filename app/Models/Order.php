<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    // Pastikan kolom status dapat diisi
    protected $fillable = [
        'order_id',
        'status',
        'gross_amount',
        'user_id',
        // Kolom lain yang relevan
    ];

    // Atau gunakan $guarded jika Anda ingin mengecualikan kolom tertentu
    // protected $guarded = [];

    public function items(){
        return $this->hasMany(OrderItem::class);
    }

    public static function updateOrderStatus($transactionStatus, $orderId)
    {
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            $updated = self::where('order_id', $orderId)->update(['status' => 'paid']);
            if ($updated) {
                Log::info('Order ID ' . $orderId . ' status successfully updated to paid.');
            } else {
                Log::error('Failed to update status for Order ID ' . $orderId);
            }
        }
    }
}
