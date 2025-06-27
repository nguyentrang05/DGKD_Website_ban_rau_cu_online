<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrdersController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $orders = Order::with('orderItems.product') 
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('orders.index', compact('orders'));
    }


    public function show($orderId)
    {
        $order = Order::with('orderItems.product') // lấy cả sản phẩm trong đơn hàng
                    ->where('user_id', auth()->id()) // đảm bảo user chỉ xem đơn của mình
                    ->findOrFail($orderId);

        return view('orders.show', compact('order'));
    }

    public function checkout(Request $request)
    {
        $user = auth()->user();

        $cart = Cart::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->with('cartItems.product')
                    ->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Giỏ hàng trống hoặc không tồn tại.');
        }

        DB::beginTransaction();

        try {
            // Tính tổng giá đơn hàng
            $totalPrice = $cart->cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // Tạo đơn hàng
            $order = Order::create([
                'user_id'        => $user->id,
                'total_price'    => $totalPrice,
                'status'         => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $request->input('payment_method', 'COD'),
                'address'        => $request->input('address', 'Không có địa chỉ'),
                'delivery_date'  => $request->input('delivery_date', null),
            ]);

            // Lưu từng item trong order_items
            foreach ($cart->cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->price,
                ]);
            }

            // Xóa cart items
            $cart->cartItems()->delete();

            DB::commit();

            return redirect()->route('cart')->with('success', 'Đơn hàng đã được tạo và giỏ hàng đã được làm trống.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xử lý đơn hàng: ' . $e->getMessage());
        }
    }


}
