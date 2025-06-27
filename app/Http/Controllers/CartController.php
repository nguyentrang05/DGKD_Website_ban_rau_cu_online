<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['cartItems.product.category'])
                    ->where('user_id', auth()->id())
                    ->where('is_active', 1)
                    ->first();

        $suggestedProducts = Product::inRandomOrder()->limit(3)->get(); // Tùy chọn
        

        return view('cart.index', compact('cart', 'suggestedProducts'));
    }


    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $quantity = $request->input('quantity', 1);

        // Kiểm tra tồn kho đủ không
        if ($product->stock_quantity < $quantity) {
            return redirect()->back()->with('error', 'Sản phẩm không đủ số lượng tồn kho');
        }

        // Lấy cart hiện tại của user
        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id(),
        ]);

        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $item = CartItem::where('cart_id', $cart->id)
                        ->where('product_id', $id)
                        ->first();

        if ($item) {
            $newQuantity = $item->quantity + $quantity;

            // Kiểm tra lại tồn kho so với tổng số lượng mới
            if ($product->stock_quantity < ($newQuantity - $item->quantity)) {
                return redirect()->back()->with('error', 'Sản phẩm không đủ số lượng tồn kho');
            }

            $item->quantity = $newQuantity;
            $item->save();
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $id,
                'quantity'   => $quantity,
            ]);
        }

        // Trừ tồn kho
        $product->stock_quantity -= $quantity;
        $product->save();

        return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng');
    }



    public function remove($id)
    {
        $cartItem = \App\Models\CartItem::find($id);

        if (!$cartItem || $cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng']);
        }

        $cart = $cartItem->cart;

        // Lấy sản phẩm liên quan
        $product = \App\Models\Product::find($cartItem->product_id);
        if ($product) {
            $product->stock_quantity += $cartItem->quantity;
            $product->save();
        }

        // Xoá sản phẩm khỏi cart_items
        $cartItem->delete();

        // Kiểm tra nếu giỏ hàng đã trống
        $isCartEmpty = $cart->cartItems()->count() === 0;

        return response()->json([
            'success' => true,
            'cartEmpty' => $isCartEmpty
        ]);
    }


}
