@extends('layouts.app')

@section('title', 'Sản phẩm')

@section('content')
<div class="container mx-auto px-4 py-8">
<!-- Page Header -->
<div class="mb-4">
    <h1 class="h3 fw-bold text-dark mb-2">Sản phẩm tươi ngon</h1>
    <p class="text-muted">Khám phá những sản phẩm rau củ quả tươi ngon nhất</p>
</div>

<!-- Filter and Sort Section -->
<div class="bg-white rounded shadow-sm p-4 mb-4 border">
    <div class="row gy-3 align-items-center justify-content-between">
        <!-- Category and Price Filter -->
        <div class="col-lg-8">
            <div class="row g-3">
                <!-- Category Filter -->
                <div class="col-sm-6 col-md-4">
                    <label class="form-label fw-medium text-dark">Danh mục:</label>
                    <select class="form-select form-select-sm" id="categoryFilter" onchange="handleFilterChange()">
                        <option value="">Tất cả</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>


                <!-- Price Filter -->
                <div class="col-sm-6 col-md-4">
                    <label class="form-label fw-medium text-dark">Giá:</label>
                    <select class="form-select form-select-sm" id="priceFilter">
                        <option value="">Tất cả</option>
                        <option value="0-50000">Dưới 50,000đ</option>
                        <option value="50000-100000">50,000đ - 100,000đ</option>
                        <option value="100000-200000">100,000đ - 200,000đ</option>
                        <option value="200000+">Trên 200,000đ</option>
                    </select>
                </div>

                <!-- Search Box -->
                <div class="col-sm-12 col-md-4">
                    <label class="form-label fw-medium text-dark">Tìm kiếm:</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="searchInput"
                               placeholder="Tên sản phẩm...">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sort Options -->
        <div class="col-lg-4">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label fw-medium text-dark mb-0">Sắp xếp:</label>
                </div>
                <div class="col">
                    <select class="form-select form-select-sm" id="sortSelect">
                        <option value="latest">Mới nhất</option>
                        <option value="price_asc">Giá thấp đến cao</option>
                        <option value="price_desc">Giá cao đến thấp</option>
                        <option value="name_asc">Tên A-Z</option>
                        <option value="popular">Phổ biến</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Filters Button -->
    <div class="row mt-3" id="clearFiltersRow" style="display: none;">
        <div class="col-12">
            <button class="btn btn-outline-secondary btn-sm" id="clearFiltersBtn">
                <i class="fas fa-times me-1"></i>
                Xóa bộ lọc
            </button>
            <span class="text-muted small ms-2" id="productCount">
                Đang hiển thị 0 sản phẩm
            </span>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loadingSpinner" class="text-center py-4" style="display: none;">
    <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">Đang tải...</span>
    </div>
</div>

<!-- Products Grid -->
<div class="row g-4 mb-5" id="productsGrid">
    <!-- Products will be loaded here via JavaScript -->
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center" id="paginationContainer">
    <!-- Pagination will be loaded here via JavaScript -->
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thông báo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cartModalBody">
                <!-- Dynamic content -->
            </div>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,.125);
}

.product-card:hover {
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    transform: translateY(-2px);
}

.product-image-container {
    overflow: hidden;
    height: 200px;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-overlay {
    background: rgba(0,0,0,0);
    transition: all 0.3s ease;
    opacity: 0;
}

.product-card:hover .product-overlay {
    background: rgba(0,0,0,0.2);
    opacity: 1;
}

.quick-view-btn {
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.product-card:hover .quick-view-btn {
    transform: translateY(0);
}

.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
    max-height: 2.8em;
}

.gap-2 {
    gap: 0.5rem;
}

.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 576px) {
    .product-image-container {
        height: 150px;
    }
}
</style>

<script>

const allProducts = @json($allProducts);

// Thiết lập trạng thái ban đầu cho việc lọc và phân trang
let currentFilters = {
    category: '',
    priceRange: '',
    search: '',
    sort: 'latest'
};

let currentPage = 1;
const itemsPerPage = 4;
let filteredProducts = [...allProducts];//sao chép toàn bộ sản phẩm trong allProduct để phục vụ việc lọc sản phẩm

// Khởi tạo trang
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();//gắn các sự kiện vào phần tử
    loadProducts();//Hiển thị danh sách sp 
});

//Cài đặt event listeners
function setupEventListeners() {
    document.getElementById('categoryFilter').addEventListener('change',handleFilterChange);
    document.getElementById('priceFilter').addEventListener('change', handleFilterChange);
    document.getElementById('searchInput').addEventListener('input', debounce(handleSearchChange, 500));
    document.getElementById('searchBtn').addEventListener('click', handleSearchChange);
    document.getElementById('sortSelect').addEventListener('change', handleFilterChange);
    document.getElementById('clearFiltersBtn').addEventListener('click', clearAllFilters);

    // Ấn nút Enter để tìm kiếm
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            handleSearchChange();
        }
    });
}

// Xử lý thay đổi bộ lọc
function handleFilterChange() {
    currentFilters.category = document.getElementById('categoryFilter').value;//Lấy giá trị đc chọn và cập nhật vào biến
    currentFilters.priceRange = document.getElementById('priceFilter').value;
    currentFilters.sort = document.getElementById('sortSelect').value;
    currentPage = 1;// Mỗi lần lọc phân trang trở về trang đầu tiên 
    loadProducts();
}

// Xử lý thay đổi phần tìm kiếm
function handleSearchChange() {
    currentFilters.search = document.getElementById('searchInput').value.trim();
    currentPage = 1;
    loadProducts();
}

// Xóa tất cả bộ lọc
function clearAllFilters() {
    document.getElementById('categoryFilter').value = '';//gán về giá trị ban đầu khi khời tạo
    document.getElementById('priceFilter').value = '';
    document.getElementById('searchInput').value = '';
    document.getElementById('sortSelect').value = 'latest';

    currentFilters = {
        category: '',
        priceRange: '',
        search: '',
        sort: 'latest'
    };
    currentPage = 1;
    loadProducts();
}

// Hàm chính để tải và hiển thị sản phẩm 
function loadProducts() {
    showLoading(true);

    // giả lập độ trễ
    setTimeout(() => {
        // Lọc sản phẩm
        filteredProducts = filterProducts([...allProducts]);

        // Sắp xếp sản phẩm
        filteredProducts = sortProducts(filteredProducts);

        renderProducts();
        renderPagination();
        updateUI();

        showLoading(false);
    }, 300);
}

// Lọc sản phẩm theo các điều kiện
function filterProducts(products) {
    let filtered = products;

    // Lọc danh mục
    // chỉ giữ lại những sp có slug trùng với lựa chọn người dùng)
    if (currentFilters.category) {
        filtered = filtered.filter(product =>
            product.category.slug === currentFilters.category
        );
    }

    // Lọc theo giá
    if (currentFilters.priceRange) {
        filtered = filtered.filter(product => {
            const price = product.price;
            switch (currentFilters.priceRange) {
                case '0-50000':
                    return price < 50000;
                case '50000-100000':
                    return price >= 50000 && price <= 100000;
                case '100000-200000':
                    return price >= 100000 && price <= 200000;
                case '200000+':
                    return price > 200000;
                default:
                    return true;
            }
        });
    }

    // Lọc theo thanh tìm kiếm
    if (currentFilters.search) {
        const searchTerm = currentFilters.search.toLowerCase();
        filtered = filtered.filter(product =>
            product.name.toLowerCase().includes(searchTerm) ||
            product.description.toLowerCase().includes(searchTerm)
        );
    }

    return filtered;
}

// Sắp xếp sản phẩm
function sortProducts(products) {
    const sorted = [...products];

    switch (currentFilters.sort) {
        case 'price_asc':
            return sorted.sort((a, b) => a.price - b.price);
        case 'price_desc':
            return sorted.sort((a, b) => b.price - a.price);
        case 'name_asc':
            return sorted.sort((a, b) => a.name.localeCompare(b.name));
        case 'popular':
            return sorted.sort((a, b) => b.views - a.views);
        case 'latest':
        default:
            return sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }
}

// Hiển thị danh sách sản phẩm sau khi lọc 
function renderProducts() {
    const grid = document.getElementById('productsGrid');// Lấy phần tử HTML chứa sp 
    //Phân trang hiển thị
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const productsToShow = filteredProducts.slice(startIndex, endIndex);//Lấy danh sách sp cần hiển thị
    //Nếu ko có sp nào
    if (productsToShow.length === 0) {
        grid.innerHTML = renderEmptyState();
        return;
    }
    //Nếu có thì hiển thị ra
    const html = productsToShow.map(product => renderProductCard(product)).join('');
    grid.innerHTML = html;
    grid.classList.add('fade-in');
}

// Hàm hiển thi từng box sản phẩm
function renderProductCard(product) {
    const isInStock = product.stock_quantity > 0;
    const stockStatus = isInStock
        ? `<span class="text-success small fw-medium">
             <i class="fas fa-check-circle me-1"></i>
             Còn hàng (${product.stock_quantity})
           </span>`
        : `<span class="text-danger small fw-medium">
             <i class="fas fa-times-circle me-1"></i>
             Hết hàng
           </span>`;

    return `
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 shadow-sm product-card">
                <div class="position-relative product-image-container">
                    <img src="${product.image_url || 'images/products/default.jpg'}"
                         alt="${product.name}"
                         class="card-img-top product-image">
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center product-overlay">
                            <a href="/products/${product.id}" class="btn btn-light btn-sm quick-view-btn text-decoration-none">
                                <i class="fas fa-eye me-1"></i>
                                Xem nhanh
                            </a>
                        </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <span class="badge bg-light text-dark text-uppercase small">
                            ${product.category.name}
                        </span>
                    </div>
                    <h5 class="card-title fw-semibold text-truncate-2 mb-2">
                        ${product.name}
                    </h5>
                    <p class="card-text text-muted small text-truncate-2 mb-3">
                        ${product.description}
                    </p>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <span class="text-success fw-bold fs-5 me-1">
                                ${formatPrice(product.price)}đ
                            </span>
                        </div>
                        <span class="text-muted small">
                            /${product.unit}
                        </span>
                    </div>
                    <div class="mb-3">
                        ${stockStatus}
                    </div>
                    <div class="mt-auto">
                        <div class="d-flex gap-2">
                            <a href="/products/${product.id}"
                            class="btn btn-success btn-sm flex-fill ${!isInStock ? 'disabled pointer-events-none opacity-50' : ''}">
                                <i class="fas fa-shopping-cart me-1"></i>
                                ${isInStock ? 'Thêm vào giỏ' : 'Hết hàng'}
                            </a>
                            <a href="/products/${product.id}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Hiển thị thông báo ko tìm thấy sản phẩm nếu bộ lọc ko phù hợp
function renderEmptyState() {
    const hasFilters = Object.values(currentFilters).some(value => value && value !== 'latest');

    return `
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-seedling display-1 text-muted opacity-50"></i>
                </div>
                <h3 class="h4 text-muted mb-3">Không tìm thấy sản phẩm</h3>
                <p class="text-muted mb-4">
                    ${hasFilters
                        ? 'Không có sản phẩm nào phù hợp với bộ lọc của bạn.'
                        : 'Hiện tại chưa có sản phẩm nào trong danh mục này.'}
                </p>
                ${hasFilters
                    ? '<button class="btn btn-success me-2" onclick="clearAllFilters()"><i class="fas fa-times me-2"></i>Xóa bộ lọc</button>'
                    : ''}
                <a href="/" class="btn btn-outline-success">
                    <i class="fas fa-home me-2"></i>
                    Về trang chủ
                </a>
            </div>
        </div>
    `;
}

// Hiển thị thanh phân trang
function renderPagination() {
    const container = document.getElementById('paginationContainer');
    const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);

    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<nav><ul class="pagination justify-content-center">';

    // nút trước
    html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <button class="page-link" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                Trước
            </button>
        </li>
    `;

    // số trang
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <button class="page-link" onclick="goToPage(${i})">${i}</button>
                </li>
            `;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // nút sau
    html += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <button class="page-link" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                Sau
            </button>
        </li>
    `;

    html += '</ul></nav>';
    container.innerHTML = html;
}

// Update UI elements

function updateUI() {
    //Kiểm tra xem người dùng có dùng bộ lọc nào ko
    const hasFilters = Object.values(currentFilters).some(value => value && value !== 'latest');
    const clearFiltersRow = document.getElementById('clearFiltersRow');
    const productCount = document.getElementById('productCount');
    //Nếu có thì hiện nút xóa bộ lọc
    if (hasFilters) {
        clearFiltersRow.style.display = 'block';
    } else {
        clearFiltersRow.style.display = 'none';
    }

    productCount.textContent = `Đang hiển thị ${filteredProducts.length} sản phẩm`;
}

// Utility functions
function showLoading(show) {
    const spinner = document.getElementById('loadingSpinner');
    const grid = document.getElementById('productsGrid');

    if (show) {
        spinner.style.display = 'block';
        grid.style.opacity = '0.5';
    } else {
        spinner.style.display = 'none';
        grid.style.opacity = '1';
    }
}
//Hàm chuyển trang
function goToPage(page) {
    const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderProducts();
        renderPagination();

        // cuộn trang xuống phần hiển thị sản phẩm 
        document.getElementById('productsGrid').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
// //Cập nhật số lượng trong giỏ hàng
// function updateCartCount() {
//     if (!window.cart) return;

//     const totalItems = Object.values(window.cart).reduce((sum, item) => sum + item.quantity, 0);
//     const cartBadge = document.querySelector('.cart-badge');
//     if (cartBadge) {
//         cartBadge.textContent = totalItems;
//         cartBadge.style.display = totalItems > 0 ? 'inline' : 'none';
//     }
// }

// Toast notification hiển thị thông báo cho người dùng
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideIn 0.3s ease;';

    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${icon} me-2"></i>
            ${message}
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add CSS for toast animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
@endsection
