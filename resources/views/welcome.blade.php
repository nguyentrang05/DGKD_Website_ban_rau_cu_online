@extends('layouts.app')

@section('title', 'Chào mừng đến với cửa hàng rau củ quả sạch')

@section('content')
<div class="hero-section position-relative mb-5">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('images/banners/banner1.png') }}" class="d-block w-100" alt="Rau củ quả tươi ngon">
                <div class="carousel-caption">
                    <h2>Rau củ quả tươi ngon mỗi ngày</h2>
                    <p>Cung cấp các loại rau củ quả sạch, an toàn và chất lượng cao</p>
                    <a href="{{ url('/products') }}" class="btn btn-success btn-lg">Mua ngay</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/banners/banner2.jpg') }}" class="d-block w-100" alt="Sản phẩm hữu cơ">
                <div class="carousel-caption">
                    <h2>Sản phẩm hữu cơ 100%</h2>
                    <p>Không thuốc trừ sâu, không hóa chất độc hại</p>
                    <a href="{{ url('/products?type=organic') }}" class="btn btn-success btn-lg">Khám phá</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/banners/banner3.jpg') }}" class="d-block w-100" alt="Giao hàng nhanh">
                <div class="carousel-caption">
                    <h2>Giao hàng nhanh chóng</h2>
                    <p>Miễn phí giao hàng cho đơn từ 200.000đ</p>
                    <a href="{{ url('/products') }}" class="btn btn-success btn-lg">Mua sắm ngay</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<div class="categories-section mb-5">
    <h2 class="text-center mb-4">Danh mục sản phẩm</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card category-card">
                <img src="{{ asset('images/rau-sach.jpg') }}" class="card-img-top" alt="Rau xanh">
                <div class="card-body text-center">
                    <h5 class="card-title">Rau xanh</h5>
                    <p class="card-text">Các loại rau xanh tươi ngon, giàu dinh dưỡng</p>
                    <a href="{{ url('/products?category=rau') }}" class="btn btn-outline-success">Xem sản phẩm</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card category-card">
                <img src="{{ asset('images/cu-cac-loai.jpg') }}" class="card-img-top" alt="Củ">
                <div class="card-body text-center">
                    <h5 class="card-title">Củ các loại</h5>
                    <p class="card-text">Các loại củ tươi ngon, đảm bảo chất lượng</p>
                    <a href="{{ url('/products?category=cu') }}" class="btn btn-outline-success">Xem sản phẩm</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card category-card">
                <img src="{{ asset('images/qua-cac-loai.jpg') }}" class="card-img-top" alt="Quả">
                <div class="card-body text-center">
                    <h5 class="card-title">Trái cây</h5>
                    <p class="card-text">Các loại trái cây tươi ngon, nhiều vitamin</p>
                    <a href="{{ url('/products?category=qua') }}" class="btn btn-outline-success">Xem sản phẩm</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="features-section mb-5">
    <h2 class="text-center mb-4">Tại sao chọn chúng tôi?</h2>
    <div class="row">
        <div class="col-md-3 mb-4 text-center">
            <div class="feature-box p-3">
                <i class="fas fa-leaf fa-3x text-success mb-3"></i>
                <h5>Sản phẩm hữu cơ</h5>
                <p>Rau củ quả được trồng theo phương pháp hữu cơ, không sử dụng hóa chất độc hại</p>
            </div>
        </div>
        <div class="col-md-3 mb-4 text-center">
            <div class="feature-box p-3">
                <i class="fas fa-truck fa-3x text-success mb-3"></i>
                <h5>Giao hàng nhanh chóng</h5>
                <p>Giao hàng trong ngày đối với khu vực nội thành, đảm bảo độ tươi ngon</p>
            </div>
        </div>
        <div class="col-md-3 mb-4 text-center">
            <div class="feature-box p-3">
                <i class="fas fa-medal fa-3x text-success mb-3"></i>
                <h5>Chất lượng đảm bảo</h5>
                <p>Cam kết hoàn tiền 100% nếu sản phẩm không đạt chất lượng như mô tả</p>
            </div>
        </div>
        <div class="col-md-3 mb-4 text-center">
            <div class="feature-box p-3">
                <i class="fas fa-headset fa-3x text-success mb-3"></i>
                <h5>Hỗ trợ 24/7</h5>
                <p>Đội ngũ chăm sóc khách hàng luôn sẵn sàng hỗ trợ bạn mọi lúc</p>
            </div>
        </div>
    </div>
</div>

<div class="testimonials-section mb-5">
    <h2 class="text-center mb-4">Khách hàng nói gì về chúng tôi</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <img src="{{ asset('images/avatars/avt1.png') }}" class="rounded-circle me-3" width="50" height="50" alt="Avatar">
                        <div>
                            <h5 class="mb-0">Nguyễn Văn A</h5>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="card-text">"Rau củ quả luôn tươi ngon, giao hàng nhanh và đúng hẹn. Tôi rất hài lòng với chất lượng sản phẩm và dịch vụ."</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <img src="{{ asset('images/avatars/avt2.webp') }}" class="rounded-circle me-3" width="50" height="50" alt="Avatar">
                        <div>
                            <h5 class="mb-0">Trần Thị B</h5>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p class="card-text">"Đã mua hàng nhiều lần và chưa bao giờ thất vọng. Sản phẩm luôn tươi ngon, đóng gói cẩn thận và giao hàng đúng hẹn."</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <img src="{{ asset('images/avatars/avt3.webp') }}" class="rounded-circle me-3" width="50" height="50" alt="Avatar">
                        <div>
                            <h5 class="mb-0">Lê Văn C</h5>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="card-text">"Tôi rất yên tâm khi mua rau củ quả ở đây. Sản phẩm luôn tươi ngon, an toàn và giá cả hợp lý. Sẽ tiếp tục ủng hộ shop."</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="newsletter-section bg-light p-4 rounded mb-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3>Đăng ký nhận thông tin</h3>
            <p>Nhận thông tin về sản phẩm mới, khuyến mãi và mẹo sử dụng rau củ quả.</p>
        </div>
        <div class="col-md-6">
            <form class="d-flex">
                <input type="email" class="form-control me-2" placeholder="Email của bạn">
                <button type="submit" class="btn btn-success">Đăng ký</button>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- @section('scripts') --}}
{{-- <script>
    // Script để xử lý thêm vào giỏ hàng
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            // Thêm sản phẩm vào giỏ hàng (sẽ xử lý bằng AJAX)
            alert('Đã thêm sản phẩm vào giỏ hàng!');
        });
    });
</script> --}}
{{-- @endsection --}}
