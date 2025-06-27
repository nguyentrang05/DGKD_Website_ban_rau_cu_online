@extends('layouts.admin')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Chỉnh sửa sản phẩm</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa: {{ $product->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $product->name) }}" required
                                       placeholder="Nhập tên sản phẩm...">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror"
                                        id="category_id" name="category_id" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}"
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="unit" class="form-label">Đơn vị tính <span class="text-danger">*</span></label>
                                <select class="form-select @error('unit') is-invalid @enderror"
                                        id="unit" name="unit" required>
                                    <option value="">Chọn đơn vị</option>
                                    <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                    <option value="gram" {{ old('unit', $product->unit) == 'gram' ? 'selected' : '' }}>Gram (g)</option>
                                    <option value="bó" {{ old('unit', $product->unit) == 'bó' ? 'selected' : '' }}>Bó</option>
                                    <option value="gói" {{ old('unit', $product->unit) == 'gói' ? 'selected' : '' }}>Gói</option>
                                    <option value="hộp" {{ old('unit', $product->unit) == 'hộp' ? 'selected' : '' }}>Hộp</option>
                                    <option value="chai" {{ old('unit', $product->unit) == 'chai' ? 'selected' : '' }}>Chai</option>
                                    <option value="lon" {{ old('unit', $product->unit) == 'lon' ? 'selected' : '' }}>Lon</option>
                                    <option value="cái" {{ old('unit', $product->unit) == 'cái' ? 'selected' : '' }}>Cái</option>
                                    <option value="chiếc" {{ old('unit', $product->unit) == 'chiếc' ? 'selected' : '' }}>Chiếc</option>
                                    <option value="lít" {{ old('unit', $product->unit) == 'lít' ? 'selected' : '' }}>Lít</option>
                                    <option value="nải" {{ old('unit', $product->unit) == 'nải' ? 'selected' : '' }}>Nải</option>
                                </select>
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Mô tả sản phẩm <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="6" required
                                          placeholder="Mô tả chi tiết về sản phẩm...">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>Giá & Kho hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Giá bán <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                           id="price" name="price" value="{{ old('price', $product->price) }}"
                                           min="0" step="0.01" required placeholder="0.00">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="stock_quantity" class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                       min="0" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Product Images -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-images me-2"></i>Hình ảnh sản phẩm
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Current Image -->
                        @if($product->image_url)
                            <div class="mb-3">
                                <label class="form-label">Hình ảnh hiện tại:</label>
                                <div class="current-image-container">
                                    <img src="{{ Storage::url($product->image_url) }}"
                                         alt="{{ $product->name }}"
                                         class="img-preview current-image"
                                         id="current-image">
                                    <div class="image-overlay">
                                        <button type="button" class="btn btn-sm btn-danger" id="remove-current-image">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="image_url" class="form-label">
                                {{ $product->image_url ? 'Thay đổi hình ảnh' : 'Thêm hình ảnh' }}
                            </label>
                            <input type="file" class="form-control @error('image_url') is-invalid @enderror"
                                   id="image_url" name="image_url" accept="image/*">
                            @error('image_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Khuyến nghị: 800x800px, định dạng JPG/PNG, tối đa 2MB</small>
                        </div>

                        <!-- Hidden input for image removal -->
                        <input type="hidden" name="remove_image" id="remove-image-input" value="0">
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-search me-2"></i>URL SLUG
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="slug" class="form-label">URL Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                   id="slug" name="slug" value="{{ old('slug', $product->slug) }}" required
                                   placeholder="url-san-pham">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">URL hiện tại:
                                <a href="#" class="text-primary">{{ url('/products/' . $product->slug) }}</a>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info me-2"></i>Thông tin sản phẩm
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <small class="text-muted">Ngày tạo:</small>
                            <div class="fw-bold">{{ $product->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="info-item mt-2">
                            <small class="text-muted">Cập nhật lần cuối:</small>
                            <div class="fw-bold">{{ $product->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="info-item mt-2">
                            <small class="text-muted">ID sản phẩm:</small>
                            <div class="fw-bold">#{{ $product->id }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Cập nhật sản phẩm
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                            </a>
                            <div class="dropdown-divider"></div>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-2"></i>Xóa sản phẩm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@push('styles')
<style>
    .form-label {
        font-weight: 600;
        color: #374151;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .card {
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
    }

    .card-header {
        border-bottom: 1px solid #e3e6f0;
        background-color: #f8f9fa !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .invalid-feedback {
        display: block;
    }

    .img-preview {
        max-width: 100%;
        max-height: 200px;
        object-fit: cover;
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
    }

    .current-image-container {
        position: relative;
        display: inline-block;
    }

    .current-image {
        display: block;
        transition: opacity 0.3s ease;
    }

    .image-overlay {
        position: absolute;
        top: 5px;
        right: 5px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .current-image-container:hover .current-image {
        opacity: 0.8;
    }

    .current-image-container:hover .image-overlay {
        opacity: 1;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
        color: #6c757d;
    }

    .breadcrumb a {
        color: #0d6efd;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        color: #0b5ed7;
        text-decoration: underline;
    }

    .info-item {
        padding: 0.25rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .dropdown-divider {
        height: 0;
        margin: 0.5rem 0;
        overflow: hidden;
        border-top: 1px solid #e9ecef;
    }

    .alert {
        border-radius: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto generate slug from product name (but don't override manually edited)
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    let slugManuallyEdited = false;

    nameInput.addEventListener('input', function() {
        if (!slugManuallyEdited) {
            const slug = this.value
                .toLowerCase()
                .replace(/[áàảãạăắằẳẵặâấầẩẫậ]/g, 'a')
                .replace(/[éèẻẽẹêếềểễệ]/g, 'e')
                .replace(/[íìỉĩị]/g, 'i')
                .replace(/[óòỏõọôốồổỗộơớờởỡợ]/g, 'o')
                .replace(/[úùủũụưứừửữự]/g, 'u')
                .replace(/[ýỳỷỹỵ]/g, 'y')
                .replace(/đ/g, 'd')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugInput.value = slug;
        }
    });

    slugInput.addEventListener('input', function() {
        slugManuallyEdited = true;
    });

    // Image preview functionality for new image
    const imageInput = document.getElementById('image_url');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const currentImage = document.getElementById('current-image');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';

                // Dim current image when new image is selected
                if (currentImage) {
                    currentImage.style.opacity = '0.5';
                }
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
            if (currentImage) {
                currentImage.style.opacity = '1';
            }
        }
    });

    // Remove current image functionality
    const removeCurrentBtn = document.getElementById('remove-current-image');
    const removeImageInput = document.getElementById('remove-image-input');

    if (removeCurrentBtn) {
        removeCurrentBtn.addEventListener('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa hình ảnh hiện tại?')) {
                const currentImageContainer = document.querySelector('.current-image-container');
                currentImageContainer.style.display = 'none';
                removeImageInput.value = '1';
            }
        });
    }

    // Form validation
    const form = document.getElementById('productForm');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Price formatting
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('blur', function() {
        if (this.value) {
            const value = parseFloat(this.value);
            if (!isNaN(value)) {
                this.value = value.toFixed(2);
            }
        }
    });
});

// Update and continue editing
function updateAndContinue() {
    const form = document.getElementById('productForm');
    const continueInput = document.createElement('input');
    continueInput.type = 'hidden';
    continueInput.name = 'continue_editing';
    continueInput.value = '1';
    form.appendChild(continueInput);
    form.submit();
}

// Confirm delete
function confirmDelete() {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?\n\nHành động này không thể hoàn tác!')) {
        document.getElementById('deleteForm').submit();
    }
}

// Show success/error messages
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Auto-save functionality (optional)
let autoSaveTimer;
let hasUnsavedChanges = false;

function enableAutoSave() {
    const formInputs = document.querySelectorAll('#productForm input, #productForm textarea, #productForm select');

    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            hasUnsavedChanges = true;
            clearTimeout(autoSaveTimer);

            // Show indicator that there are unsaved changes
            document.title = '* Chỉnh sửa sản phẩm - Có thay đổi chưa lưu';

            autoSaveTimer = setTimeout(() => {
                // Auto-save logic here (could save as draft)
                console.log('Auto-saving...');
            }, 30000); // Auto-save after 30 seconds of inactivity
        });
    });

    // Reset unsaved changes flag on form submit
    document.getElementById('productForm').addEventListener('submit', function() {
        hasUnsavedChanges = false;
        document.title = 'Chỉnh sửa sản phẩm';
    });

    // Warn before leaving page with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi trang?';
            return e.returnValue;
        }
    });
}

// Uncomment to enable auto-save and unsaved changes warning
// enableAutoSave();
</script>
@endpush
