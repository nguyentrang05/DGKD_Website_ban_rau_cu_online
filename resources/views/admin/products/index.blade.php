@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Quản lý sản phẩm</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Sản phẩm</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Thêm sản phẩm mới
                </a>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    {{-- <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       placeholder="Tên sản phẩm..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="category" class="form-label">Danh mục</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}"
                                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Tất cả</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hiển thị</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Ẩn</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Tìm kiếm
                                    </button>
                                    <a href="" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo me-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fs-6 fw-bold">Tổng sản phẩm</div>
                            <div class="fs-3 fw-bold">{{ $totalProducts ?? 0 }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fs-6 fw-bold">Đang hiển thị</div>
                            <div class="fs-3 fw-bold">{{ $activeProducts ?? 0 }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-eye fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fs-6 fw-bold">Sắp hết hàng</div>
                            <div class="fs-3 fw-bold">{{ $lowStockProducts ?? 0 }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fs-6 fw-bold">Hết hàng</div>
                            <div class="fs-3 fw-bold">{{ $outOfStockProducts ?? 0 }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách sản phẩm</h5>
                        {{-- <div class="d-flex gap-2">
                            <button class="btn btn-outline-success btn-sm" >
                                <i class="fas fa-file-excel me-1"></i>Xuất Excel
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown">
                                    Hành động
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-trash me-2"></i>Xóa đã chọn
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" >
                                        <i class="fas fa-toggle-on me-2"></i>Đổi trạng thái
                                    </a></li>
                                </ul>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(isset($products) && $products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th width="80">Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th width="120">Giá</th>
                                        <th width="100">Số lượng</th>
                                        <th width="120">Ngày tạo</th>
                                        <th width="120">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input product-checkbox"
                                                       value="{{ $product->id }}">
                                            </td>
                                            <td>
                                                <img src="{{ $product->image_url ? asset('storage/' . $product->image_url) : asset('images/no-image.png') }}"
                                                     alt="{{ $product->name }}"
                                                     class="img-thumbnail rounded"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $product->name }}</strong>
                                                    @if($product->description)
                                                        <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $product->category->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ number_format($product->price) }}đ</strong>
                                                @if($product->sale_price)
                                                    <br><small class="text-decoration-line-through text-muted">
                                                        {{ number_format($product->sale_price) }}đ
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->stock_quantity > 10)
                                                    <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                                                @elseif($product->stock_quantity > 0)
                                                    <span class="badge bg-warning">{{ $product->stock_quantity }}</span>
                                                @else
                                                    <span class="badge bg-danger">Hết hàng</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $product->created_at->format('d/m/Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="">
                                                                <i class="fas fa-eye me-2"></i>Xem chi tiết
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.products.edit', $product->id) }}">
                                                                <i class="fas fa-edit me-2"></i>Chỉnh sửa
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                               onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')">
                                                                <i class="fas fa-trash me-2"></i>Xóa
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($products->hasPages())
                            <div class="card-footer">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                                    <div class="pagination-info">
                                        Hiển thị {{ $products->firstItem() }} - {{ $products->lastItem() }}
                                        trong tổng số {{ $products->total() }} sản phẩm
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không có sản phẩm nào</h5>
                            <p class="text-muted">Hãy thêm sản phẩm đầu tiên của bạn</p>
                            <a href="" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm sản phẩm mới
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa sản phẩm <strong id="productName"></strong>?</p>
                <p class="text-danger">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .img-thumbnail {
        border: 1px solid #dee2e6;
    }

    .table td {
        vertical-align: middle;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .status-toggle:checked {
        background-color: #198754;
        border-color: #198754;
    }

    .card {
        border: 1px solid #e3e6f0;
    }

    .card-header {
        border-bottom: 1px solid #e3e6f0;
    }

    /* Fix pagination styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        color: #6c757d;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        margin: 0 2px;
        border-radius: 0.375rem;
    }

    .pagination .page-link:hover {
        color: #0d6efd;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
        opacity: 0.5;
    }

    /* Fix pagination container */
    .card-footer {
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const selectAllCheckbox = document.getElementById('selectAll');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Status toggle functionality
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const productId = this.dataset.id;
            const isActive = this.checked;

            // AJAX call to update status
            fetch(`/admin/products/${productId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', 'Cập nhật trạng thái thành công!');
                } else {
                    // Revert toggle state
                    this.checked = !isActive;
                    showAlert('error', 'Có lỗi xảy ra!');
                }
            })
            .catch(error => {
                // Revert toggle state
                this.checked = !isActive;
                showAlert('error', 'Có lỗi xảy ra!');
            });
        });
    });
});

// Delete product function
function deleteProduct(id, name) {
    document.getElementById('productName').textContent = name;
    document.getElementById('deleteForm').action = `/admin/products/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Show alert function
function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insert at top of container
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);

    // Auto hide after 3 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}
</script>
@endpush
