@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-shopping-cart text-success"></i>
                Giỏ hàng của bạn
                @if(isset($cart) && $cart && $cart->cartItems->count() > 0)
                    <span class="badge bg-success ms-2">{{ $cart->cartItems->count() }} sản phẩm</span>
                @endif
            </h2>
        </div>
    </div>

    @if(isset($cart) && $cart && $cart->cartItems->count() > 0)
        <div class="row">
            <!-- Danh sách sản phẩm trong giỏ hàng -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-list-ul"></i>
                            Danh sách sản phẩm
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($cart->cartItems as $cartItem)
                            <div class="cart-item border-bottom p-3" data-item-id="{{ $cartItem->id }}">
                                <div class="row align-items-center">
                                    <!-- Hình ảnh sản phẩm -->
                                    <div class="col-md-2 col-sm-3">
                                        <img src="{{ asset('storage/' . ($cartItem->product->image_url ?? 'default.jpg')) }}"
                                             alt="{{ $cartItem->product->name }}"
                                             class="img-fluid rounded cart-product-image"
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>

                                    <!-- Thông tin sản phẩm -->
                                    <div class="col-md-4 col-sm-9">
                                        <h6 class="mb-1">
                                            <a href="{{ route('products.show', $cartItem->product->id) }}"
                                               class="text-decoration-none text-dark">
                                                {{ $cartItem->product->name }}
                                            </a>
                                        </h6>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-tag"></i>
                                            {{ $cartItem->product->category->name ?? 'Chưa phân loại' }}
                                        </p>
                                        <p class="text-success fw-bold mb-0">
                                            {{ number_format($cartItem->product->price, 0, ',', '.') }}đ / {{ $cartItem->product->unit ?? 'kg' }}
                                        </p>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <input type="number"
                                                   class="form-control form-control-sm mx-2 text-center "
                                                   value="{{ $cartItem->quantity }}"
                                                   min="1"
                                                   max="99"
                                                   style="width: 60px;"
                                                   readonly>
                                        </div>
                                    </div>

                                    <!-- Thành tiền và xóa -->
                                    <div class="col-md-3 col-sm-6">
                                        <div class="text-end">
                                            <p class="fw-bold text-success mb-2 item-total">
                                                {{ number_format($cartItem->product->price * $cartItem->quantity, 0, ',', '.') }}đ
                                            </p>
                                            <button class="btn btn-outline-danger btn-sm"
                                                    onclick="removeFromCart({{ $cartItem->id }})"
                                                    title="Xóa khỏi giỏ hàng">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Nút tiếp tục mua hàng -->
                <div class="mt-3">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-success">
                        <i class="fas fa-arrow-left"></i>
                        Tiếp tục mua hàng
                    </a>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt"></i>
                            Tóm tắt đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính:</span>
                            <span class="fw-bold subtotal">
                                {{ number_format($cart->cartItems->sum(function($cartItem) {
                                    return $cartItem->product->price * $cartItem->quantity;
                                }), 0, ',', '.') }}đ
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển:</span>
                            <span class="text-success">Miễn phí</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Tổng cộng:</span>
                            <span class="fw-bold fs-5 text-success total-amount">
                                {{ number_format($cart->cartItems->sum(function($cartItem) {
                                    return $cartItem->product->price * $cartItem->quantity;
                                }), 0, ',', '.') }}đ
                            </span>
                        </div>

                        <!-- Mã giảm giá -->
                        <div class="mb-3">
                            <label class="form-label small">Mã giảm giá:</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm"
                                       placeholder="Nhập mã giảm giá" id="couponCode">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="">
                                    Áp dụng
                                </button>
                            </div>
                        </div>

                        <!-- Nút thanh toán -->
                        <div class="d-grid">
                            <form action="{{ route('orders.checkout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg d-grid">
                                    <i class="fas fa-credit-card"></i>
                                    Tiến hành thanh toán
                                </button>
                            </form>
                        </div>

                        <!-- Thông tin bảo mật -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt text-success"></i>
                                Thanh toán an toàn & bảo mật
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Sản phẩm gợi ý -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb text-warning"></i>
                            Có thể bạn quan tâm
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        @if(isset($suggestedProducts) && $suggestedProducts->count() > 0)
                            @foreach($suggestedProducts as $product)
                                <div class="d-flex align-items-center mb-2 p-2 border rounded">
                                    <img src="{{ asset('storage/' . ($product->image_url ?? 'default.jpg')) }}"
                                         alt="{{ $product->name }}"
                                         class="me-2 rounded"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <p class="mb-0 small">{{ $product->name }}</p>
                                        <p class="mb-0 small text-success fw-bold">{{ number_format($product->price, 0, ',', '.') }}đ</p>
                                    </div>
                                    <button class="btn btn-outline-success btn-sm" onclick="">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Giỏ hàng trống -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-shopping-cart text-muted" style="font-size: 120px;"></i>
                    </div>
                    <h4 class="text-muted mb-3">Giỏ hàng của bạn đang trống</h4>
                    <p class="text-muted mb-4">
                        Hãy khám phá các sản phẩm tươi ngon của chúng tôi và thêm vào giỏ hàng nhé!
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-shopping-bag"></i>
                        Mua sắm ngay
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- CSS -->
<style>
.cart-product-image {
    transition: transform 0.3s ease;
}

.cart-product-image:hover {
    transform: scale(1.05);
}

.cart-item {
    transition: background-color 0.3s ease;
}

.cart-item:hover {
    background-color: #f8f9fa;
}

.quantity-btn {
    transition: all 0.3s ease;
}

.quantity-btn:hover {
    transform: scale(1.1);
}

.quantity-input {
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

@media (max-width: 768px) {
    .cart-item .row > div {
        margin-bottom: 10px;
    }

    .cart-item .text-end {
        text-align: center !important;
    }
}
</style>

<!-- JavaScript cho chức năng giỏ hàng -->
<script>


// Xóa sản phẩm khỏi giỏ hàng
function removeFromCart(cartItemId) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
        fetch(`/cart/remove/${cartItemId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-item-id="${cartItemId}"]`).remove();
                updateCartDisplay();
                showToast('Đã xóa sản phẩm khỏi giỏ hàng', 'success');

                // Reload trang nếu giỏ hàng trống
                if (data.cartEmpty) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                showToast('Có lỗi xảy ra khi xóa sản phẩm', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi xóa sản phẩm', 'error');
        });
    }
}

// Cập nhật hiển thị giỏ hàng
function updateCartDisplay() {
    // Tính toán lại tổng tiền
    let subtotal = 0;

    document.querySelectorAll('.cart-item').forEach(item => {
        const quantityInput = item.querySelector('.quantity-input');
        const priceTextElem = item.querySelector('.text-success.fw-bold');
        const itemTotalElem = item.querySelector('.item-total');

        if (!quantityInput || !priceTextElem || !itemTotalElem) return;

        const quantity = parseInt(quantityInput.value);
        const priceText = priceTextElem.textContent;
        const price = parseInt(priceText.replace(/[^\d]/g, ''));
        const itemTotal = price * quantity;

        itemTotalElem.textContent = itemTotal.toLocaleString('vi-VN') + 'đ';
        subtotal += itemTotal;
    });

    const subtotalElem = document.querySelector('.subtotal');
    const totalElem = document.querySelector('.total-amount');

    if (subtotalElem) {
        subtotalElem.textContent = subtotal.toLocaleString('vi-VN') + 'đ';
    }
    if (totalElem) {
        totalElem.textContent = subtotal.toLocaleString('vi-VN') + 'đ';
    }
}


// Hiển thị thông báo
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(toast);

    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
}

// // Khởi tạo khi trang load
// document.addEventListener('DOMContentLoaded', function() {
//     // Xử lý thay đổi số lượng bằng input
//     document.querySelectorAll('.quantity-input').forEach(input => {
//         input.addEventListener('blur', function() {
//             const cartItemId = this.closest('.cart-item').dataset.itemId;
//             const value = parseInt(this.value);
//             if (value < 1) {
//                 this.value = 1;
//             }
//         });
//     });
// });
</script>
@endsection
