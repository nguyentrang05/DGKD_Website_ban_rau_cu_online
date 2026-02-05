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
            ->where('user_id', Auth::id()) // đảm bảo user chỉ xem đơn của mình
            ->findOrFail($orderId);

        return view('orders.show', compact('order'));
    }

    /**
     * Hiển thị trang thanh toán cho đơn hàng (được dùng khi quét QR link-to-order)
     */
    public function pay($orderId)
    {
        $order = Order::with('orderItems.product')
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        return view('orders.pay', compact('order'));
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();

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

            $paymentMethod = $request->input('payment_method', 'COD');

            // Với QRCode/COD: tạo đơn và giữ trạng thái chờ thanh toán
            // Lưu ý: cột payment_status trong DB là enum('unpaid','paid','refunded')
            $paymentStatus = 'unpaid';
            $status = 'pending';
            if ($paymentMethod === 'COD') {
                // COD (thu tiền khi giao hàng) -> chưa thanh toán
                $paymentStatus = 'unpaid';
                $status = 'pending';
            }

            // Tạo đơn hàng
            $order = Order::create([
                'user_id'        => $user->id,
                'total_price'    => $totalPrice,
                'status'         => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
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

            // Nếu request AJAX (ví dụ thanh toán QRCode), trả JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'amount' => $totalPrice,
                    'message' => 'Đơn hàng được tạo, chờ thanh toán.'
                ]);
            }

            return redirect()->route('cart')->with('success', 'Đơn hàng đã được tạo và giỏ hàng đã được làm trống.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xử lý đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Xác nhận thanh toán cho đơn (dùng sau khi người dùng quét QR và đã chuyển tiền)
     */
    public function confirmPayment(Request $request, $orderId)
    {
        $user = Auth::user();

        // Only admin can confirm payment
        if (!$user || !$user->isAdmin()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized');
        }

        $order = Order::findOrFail($orderId);
        $order->payment_status = 'paid';
        $order->status = 'completed';
        $order->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Thanh toán đã được xác nhận.']);
        }

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Thanh toán đã được xác nhận.');
    }

    /**
     * User cancels an order (called when user closes the QR modal and doesn't want to pay).
     * This will mark the order as cancelled and restore items back to user's active cart.
     */
    public function cancel(Request $request, $orderId)
    {
        $user = Auth::user();

        $order = Order::with('orderItems')->where('id', $orderId)->where('user_id', $user->id)->firstOrFail();

        if ($order->status === 'cancelled') {
            return response()->json(['success' => true, 'message' => 'Đơn hàng đã bị hủy trước đó.']);
        }

        DB::beginTransaction();
        try {
            // Mark order cancelled
            $order->status = 'cancelled';
            $order->save();

            // Restore items back to user's active cart
            $cart = Cart::firstOrCreate([
                'user_id' => $user->id,
                'is_active' => true
            ], [
                'user_id' => $user->id,
                'is_active' => true
            ]);

            foreach ($order->orderItems as $item) {
                // Re-add to cart (if product still exists)
                $cart->cartItems()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Đơn hàng đã được hủy và sản phẩm trả về giỏ hàng.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi khi huỷ đơn: ' . $e->getMessage()], 500);
        }
    }
}
