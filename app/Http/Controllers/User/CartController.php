<?php

namespace App\Http\Controllers\User;

use App\Helper\CartHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Http\Resources\ProductResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class CartController extends Controller
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
    public function store(Request $request, Product $product)
    {
        $quantity = $request->input('quantity', 1);
        $size = $request->input('size');
        $color = $request->input('color');
        $note = $request->input('note'); // Tambahkan note

        $user = $request->user();

        if ($user) {
            $cartItem = Cart::where([
                'user_id' => $user->id, 
                'product_id' => $product->id,
                'size' => $size,
                'color' => $color
            ])->first();

            if ($cartItem) {
                $cartItem->quantity += 1;
                $cartItem->note = $note; // Update note jika ada
                $cartItem->save();
            } else {
                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'size' => $size,
                    'color' => $color,
                    'note' => $note // Simpan note
                ]);
            }
            Cache::forget('carts_global_count');
        } else {
            $cartItems = CartHelper::getCookieCartItems();
            $isProductExist = false;
            foreach ($cartItems as &$item) {
                if ($item['product_id'] == $product->id && 
                    $item['size'] == $size && 
                    $item['color'] == $color) {
                    $item['quantity'] += $quantity;
                    $item['note'] = $note; // Update note jika ada
                    $isProductExist = true;
                    break;
                }
            }
            if (!$isProductExist) {
                $cartItems[] = [
                    'user_id' => null,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'size' => $size,
                    'color' => $color,
                    'note' => $note, // Tambahkan note
                    'price' => $product->price,
                ];
            }
            CartHelper::setCookieCartItems($cartItems);
        }

        return redirect()->back()->with('success', 'Item added to cart successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Product $product)
    {
        $user = $request->user();
        $con =  Http::withHeaders([
            'key' => '067f3c3070f9ba9652054f7f1eb0e182',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get('https://api.rajaongkir.com/starter/province');

        $provinces = $con['rajaongkir']['results'];
        if($user){
            $carts = Cart::with('product', 'product_image')->where('user_id', $user->id)->get();
            $userAddress = UserAddress::where('user_id', $user->id)->where('isMain', 1)->first();
            $shippingCosts = [];
            if($userAddress){
                $response = Http::withHeaders([
                    'key' => '067f3c3070f9ba9652054f7f1eb0e182',
                ])->post('https://api.rajaongkir.com/starter/cost', [
                    'origin' => 501, //yogyakarta
                    'originType' => 'city',
                    'destination' => $userAddress->prov_id,
                    'destinationType' => 'city',
                    'weight' => '1',
                    'courier' => 'jne',
                ]);

                if (!$response->successful()) {
                    dd($response->body()); // Tampilkan respons jika gagal
                }
                
                $costs = $response['rajaongkir']['results'] ?? null;
                
                if (!$costs) {
                    return redirect()->back()->with('errors', 'Failed to fetch shipping costs.');
                }

                $costs = $response['rajaongkir']['results'];
                foreach ($costs as $cost) {
                    foreach ($cost['costs'] as $val) {
                        $price = $val['cost'][0]['value'];
                        $shippingCosts[] = [
                            'name' => $cost['code'],
                            'type' => $val['service'],
                            'price' => $price,
                        ];
                    }
                }
            }

            $count = Cart::where('user_id', $user->id)->count();
            $total = 0;

            if ($carts->count() != 0){
                foreach ($carts as $cart){
                    $item = Product::find($cart->product_id);
                    $sum = $item->price * $cart->quantity;
                    $total += $sum;
                }
                if($cart->count() > 0) {
                    return Inertia::render('User/CartList', [
                        'carts' => $carts->map(function ($cart) {
                            return [
                                'id' => $cart->id,
                                'product_id' => $cart->product_id,
                                'quantity' => $cart->quantity,
                                'size' => $cart->size,
                                'color' => $cart->color,
                                'note' => $cart->note, // Kirim note ke frontend
                                'product' => $cart->product,
                                'product_image' => $cart->product_image,
                            ];
                        }),
                        'count' => $count,
                        'total' => $total,
                        'provinces' => $provinces,
                        'userAddress' => $userAddress,
                        'shippings' => $shippingCosts
                    ]);
                }else {
                    return redirect()->back()->with('errors', 'You dont have product in cart');
                }
            }else{
                return Inertia::render('User/CartList', [
                    'userAddress' => $userAddress,
                    'provinces' => $provinces,
                ]);
            }
        }else{
            $cart = CartHelper::getCookieCartItems();
            if(count($cart) > 0){
                $cart = new CartResource(CartHelper::getProductsAndCartItems());
                return Inertia::render('User/CartList',[
                    'cart' => $cart,
                ]);
            }else{
                return redirect()->back()->with('errors', 'You dont have product in cart');
            }
        }

    }

    public function addAddress(Request $request)
    {

        $address = new UserAddress;
        $address->type = $request->type;
        $address->address1 = $request->address1;
        $address->no_hp = $request->no_hp;
        $address->isMain = $request->isMain;
        $address->postcode = $request->postcode;
        $address->country_code = $request->country_code;
        $address->city_id = $request->city_id;
        $address->prov_id = $request->prov_id;
        $address->user_id = $request->user()->id;

        $response = Http::withHeaders([
            'key' => '067f3c3070f9ba9652054f7f1eb0e182',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get('https://api.rajaongkir.com/starter/city?id='.$request->city_id.'&province='.$request->prov_id);

        $data = $response['rajaongkir']['results'];

        $address->province = $data['province'];
        $address->city = $data['city_name'];

        if ($address->save()){
            return redirect()->route('cart.show')->with('success', 'Address created successfully.');
        }else{
            return redirect()->back()->with('errors', 'Failed create address');
        }
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
    public function update(Request $request, Product $product)
    {
        $user = $request->user();
        
        if ($user) {
            $cart = Cart::where([
                'user_id' => $user->id,
                'product_id' => $product->id
            ])->first();

            if ($cart) {
                $cart->update([
                    'quantity' => $request->quantity,
                    'size' => $request->size,
                    'color' => $request->color,
                    'note' => $request->note // Update note
                ]);

                return redirect()->back()->with('success', 'Cart updated successfully');
            }
        } else {
            $cartItems = CartHelper::getCookieCartItems();
            foreach ($cartItems as &$item) {
                if ($item['product_id'] === $product->id) {
                    $item['quantity'] = $request->quantity;
                    $item['size'] = $request->size;
                    $item['color'] = $request->color;
                    $item['note'] = $request->note; // Update note
                    break;
                }
            }
            CartHelper::setCookieCartItems($cartItems);
        }

        return redirect()->back()->with('success', 'Cart updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        $user = $request->user();
        if($user){
            Cart::query()->where(['user_id' => $user->id, 'product_id' => $product->id])->first()?->delete();
            Cache::forget('carts_global_count');
            if(Cart::count() <= 0){
                return redirect()->route('home')->with('info', 'your cart is empty');
            }else{
                return redirect()->back()->with('success', 'item removed succesfully');
            }
        }else{
            $cartItems = Cart::getCookieCartItems();
            foreach ($cartItems as $i => &$item) {
                if ($item['product_id'] === $product->id) {
                    array_splice($cartItems, $i, 1);
                    break;
                }
            }
            CartHelper::setCookieCartItems($cartItems);
            if (count($cartItems) <= 0) {
                return redirect()->route('home')->with('info', 'your cart is empty');
            } else {
                return redirect()->back()->with('success', 'item removed successfully');
            }
        }
    }
}
