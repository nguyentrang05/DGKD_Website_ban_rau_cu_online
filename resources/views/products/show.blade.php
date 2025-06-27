@extends('layouts.app')

@section('title', $product->name . ' - Chi tiết sản phẩm')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Sản phẩm</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-images">
                <!-- Main Image -->
                <div class="main-image mb-3">
                    <img src="{{ "/storage/$product->image_url" }}"
                         alt="{{ $product->name }}"
                         class="img-fluid rounded shadow-sm w-100"
                         style="height: 400px; object-fit: cover;"
                         id="mainProductImage">
                </div>

            </div>
        </div>

        <!-- Product Information -->
        <div class="col-lg-6">
            <div class="product-info">
                <!-- Product Title & Category -->
                <div class="mb-3">
                    <span class="badge bg-success mb-2">{{ $product->category->name }}</span>
                    <h1 class="h2 fw-bold text-dark mb-2">{{ $product->name }}</h1>

                    <!-- Rating (sample - can be implemented later) -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="text-warning me-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= 4)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-muted small">(15 đánh giá)</span>
                    </div>
                </div>

                <!-- Price -->
                <div class="price-section mb-4">
                    <span class="h3 text-success fw-bold">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                    <div class="text-muted small">Giá trên 1{{ $product->unit }}</div>
                </div>

                <!-- Product Details -->
                <div class="product-details mb-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex">
                                <strong class="text-muted me-2">Đơn vị:</strong>
                                <span>{{ $product->unit }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex">
                                <strong class="text-muted me-2">Tình trạng:</strong>
                                @if($product->stock_quantity > 0)
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Còn hàng ({{ $product->stock_quantity }})</span>
                                @else
                                    <span class="text-danger"><i class="fas fa-times-circle me-1"></i>Hết hàng</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex">
                                <strong class="text-muted me-2">Danh mục:</strong>
                                <span>{{ $product->category->name }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex">
                                <strong class="text-muted me-2">Mã sản phẩm:</strong>
                                <span class="text-uppercase">#{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quantity Selector & Add to Cart -->
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="quantity" class="form-label fw-semibold">Số lượng:</label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity()">-</button>
                                <input type="number" class="form-control text-center" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity({{ $product->stock_quantity }})">+</button>
                            </div>
                        </div>
                        <div class="col-md-8">
                            @if($product->stock_quantity > 0)
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-success btn-lg flex-fill">
                                        <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-lg" onclick="addToWishlist({{ $product->id }})">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            @else
                                <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                                    <i class="fas fa-times me-2"></i>Hết hàng
                                </button>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Features/Benefits -->
                <div class="features mb-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center text-success">
                                <i class="fas fa-leaf me-2"></i>
                                <small>100% tự nhiên</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center text-success">
                                <i class="fas fa-shipping-fast me-2"></i>
                                <small>Giao hàng nhanh</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center text-success">
                                <i class="fas fa-shield-alt me-2"></i>
                                <small>Đảm bảo chất lượng</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center text-success">
                                <i class="fas fa-undo me-2"></i>
                                <small>Đổi trả trong 24h</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Share -->
                <div class="share-section">
                    <span class="text-muted me-3">Chia sẻ:</span>
                    <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-info btn-sm me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-success btn-sm me-2"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="btn btn-outline-danger btn-sm"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description & Details Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                        Mô tả sản phẩm
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="nutrition-tab" data-bs-toggle="tab" data-bs-target="#nutrition" type="button" role="tab">
                        Thông tin dinh dưỡng
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                        Đánh giá (15)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="productTabsContent">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="p-4">
                        @if($product->description)
                            <div class="mb-4">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        @else
                            <p class="text-muted">Chưa có mô tả chi tiết cho sản phẩm này.</p>
                        @endif

                        <!-- Benefits -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5 class="text-success mb-3"><i class="fas fa-heart me-2"></i>Lợi ích sức khỏe</h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Giàu vitamin và khoáng chất</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Tăng cường hệ miễn dịch</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Hỗ trợ tiêu hóa tốt</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Chống oxy hóa tự nhiên</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><i class="fas fa-utensils me-2"></i>Cách sử dụng</h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Rửa sạch trước khi sử dụng</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Có thể ăn sống hoặc nấu chín</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Bảo quản trong tủ lạnh</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Sử dụng trong 3-5 ngày</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nutrition Tab -->
                <div class="tab-pane fade" id="nutrition" role="tabpanel">
                    <div class="p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Thành phần dinh dưỡng (100g)</h5>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td>Calories</td>
                                            <td class="fw-semibold">25 kcal</td>
                                        </tr>
                                        <tr>
                                            <td>Carbohydrate</td>
                                            <td class="fw-semibold">6g</td>
                                        </tr>
                                        <tr>
                                            <td>Protein</td>
                                            <td class="fw-semibold">1.2g</td>
                                        </tr>
                                        <tr>
                                            <td>Chất béo</td>
                                            <td class="fw-semibold">0.2g</td>
                                        </tr>
                                        <tr>
                                            <td>Chất xơ</td>
                                            <td class="fw-semibold">2.5g</td>
                                        </tr>
                                        <tr>
                                            <td>Vitamin C</td>
                                            <td class="fw-semibold">15mg</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Khoáng chất</h5>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td>Calcium</td>
                                            <td class="fw-semibold">20mg</td>
                                        </tr>
                                        <tr>
                                            <td>Iron</td>
                                            <td class="fw-semibold">0.5mg</td>
                                        </tr>
                                        <tr>
                                            <td>Magnesium</td>
                                            <td class="fw-semibold">12mg</td>
                                        </tr>
                                        <tr>
                                            <td>Potassium</td>
                                            <td class="fw-semibold">180mg</td>
                                        </tr>
                                        <tr>
                                            <td>Sodium</td>
                                            <td class="fw-semibold">5mg</td>
                                        </tr>
                                        <tr>
                                            <td>Zinc</td>
                                            <td class="fw-semibold">0.2mg</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="p-4">
                        <!-- Review Summary -->
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <div class="border rounded p-3">
                                    <div class="h2 text-warning mb-2">4.5</div>
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <div class="text-muted">15 đánh giá</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <!-- Rating Breakdown -->
                                @for($i = 5; $i >= 1; $i--)
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2">{{ $i }} sao</span>
                                    <div class="progress flex-fill me-2" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: {{ rand(10, 80) }}%"></div>
                                    </div>
                                    <span class="text-muted small">{{ rand(0, 20) }}</span>
                                </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Write Review -->
                        @auth
                        <div class="border-top pt-4 mb-4">
                            <h5 class="mb-3">Viết đánh giá của bạn</h5>
                            <form action="" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Đánh giá của bạn</label>
                                        <div class="rating-input">
                                            @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" required>
                                            <label for="star{{ $i }}" class="text-warning"><i class="fas fa-star"></i></label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="review" class="form-label">Nhận xét</label>
                                        <textarea class="form-control" id="review" name="review" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <a href="{{ route('login') }}" class="alert-link">Đăng nhập</a> để viết đánh giá về sản phẩm này.
                        </div>
                        @endauth

                        <!-- Reviews List -->
                        <div class="reviews-list">
                            <!-- Sample Review -->
                            <div class="border-bottom py-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="text-warning me-2">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <strong class="me-2">Nguyễn Văn A</strong>
                                    <span class="text-muted small">2 ngày trước</span>
                                </div>
                                <p class="mb-0">Sản phẩm rất tươi ngon, chất lượng tốt. Giao hàng nhanh, đóng gói cẩn thận. Sẽ mua lại lần sau.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input label {
    cursor: pointer;
    font-size: 1.2rem;
    margin: 0 2px;
    color: #ddd;
    transition: color 0.2s;
}

.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input[type="radio"]:checked ~ label {
    color: #ffc107;
}

.thumbnail-img:hover {
    opacity: 0.8;
    transform: scale(1.05);
    transition: all 0.3s ease;
}

.product-images .main-image img {
    transition: transform 0.3s ease;
}

.product-images .main-image img:hover {
    transform: scale(1.05);
}
</style>

<script>

//Tăng số lượng sản phẩm 
function increaseQuantity(max) {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    if (currentValue < max) {
        quantityInput.value = currentValue + 1;
    }
}
// Giảm số lượng sản phẩm
function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
    }
}
 </script>
@endsection
