<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Tambahkan fillable fields untuk size dan color
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'size',
        'color',
        'note', // Menambahkan field note
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function product_image(){
        return $this->hasMany(ProductImage::class,'product_id', 'product_id');
    }

}
