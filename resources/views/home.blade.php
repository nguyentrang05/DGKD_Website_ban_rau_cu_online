@extends('layouts.app')

@section('title', 'Trang chủ - Cửa hàng rau củ quả sạch')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-success">
            <h4>Xin chào, {{ Auth::user()->name }}!</h4>
            <p>Chào mừng bạn quay trở lại với cửa hàng rau củ quả sạch của chúng tôi.</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-shopping-bag me-2"></i> Đơn hàng của tôi</h5>
                <p class="card-text">Xem và quản lý các đơn hàng của bạn.</p>
                <a href="{{ route("orders.index") }}" class="btn btn-outline-primary">Xem đơn hàng</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user me-2"></i> Thông tin cá nhân</h5>
                <p class="card-text">Cập nhật thông tin cá nhân và địa chỉ giao hàng.</p>
                <a href="{{ url('/profile') }}" class="btn btn-outline-primary">Cập nhật thông tin</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-heart me-2"></i> Sản phẩm yêu thích</h5>
                <p class="card-text">Xem danh sách sản phẩm bạn đã đánh dấu yêu thích.</p>
                <a href="" class="btn btn-outline-primary">Xem yêu thích</a>
            </div>
        </div>
    </div>
</div>

{{-- uu dai dac biet --}}
<div class="special-offers mb-5">
    <h2 class="mb-4">Ưu đãi đặc biệt</h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="{{ asset('images/banners/dis15%.jpg') }}" class="img-fluid rounded-start h-100" alt="Ưu đãi">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">Giảm 15% cho đơn hàng đầu tiên</h5>
                            <p class="card-text">Nhập mã <strong>WELCOME15</strong> để được giảm 15% cho đơn hàng đầu tiên của bạn.</p>
                            <p class="card-text"><small class="text-muted">Có hiệu lực đến {{ date('d/m/Y', strtotime('+30 days')) }}</small></p>
                            <a href="{{ url('/products') }}" class="btn btn-success">Mua ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="{{ asset('images/banners/freeship.png') }}" class="img-fluid rounded-start h-100" alt="Ưu đãi">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">Miễn phí giao hàng</h5>
                            <p class="card-text">Miễn phí giao hàng cho đơn hàng từ 200.000đ trong khu vực nội thành.</p>
                            <p class="card-text"><small class="text-muted">Áp dụng mọi lúc</small></p>
                            <a href="{{ url('/products') }}" class="btn btn-success">Mua ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Script để xử lý thêm vào giỏ hàng
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            // Thêm sản phẩm vào giỏ hàng (sẽ xử lý bằng AJAX)
            alert('Đã thêm sản phẩm vào giỏ hàng!');
        });
    });
</script>
@endsection
