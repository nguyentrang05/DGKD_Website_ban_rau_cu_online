@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng #' . $order->id)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Thanh toán đơn hàng #{{ $order->id }}</h5>
                </div>
                <div class="card-body text-center">
                    <p class="mb-1">Số tiền cần thanh toán:</p>
                    <h3 class="text-success mb-3">{{ number_format($order->total_price, 0, ',', '.') }}đ</h3>


                    <div class="d-flex justify-content-center my-3">
                        <div id="qrcode"></div>
                    </div>

                    <p class="small text-muted">Quét QR để mở trang thanh toán (hoặc dùng app ngân hàng để chuyển khoản).</p>

                        <p class="text-muted small">Sau khi bạn chuyển khoản, admin sẽ kiểm tra và xác nhận thanh toán cho đơn này. Cảm ơn bạn.</p>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary ms-2">Quay lại chi tiết đơn</a>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const q = new QRCode(document.getElementById('qrcode'), {
                text: window.location.href,
                width: 220,
                height: 220
            });
        });
    </script>
@endsection

@endsection
