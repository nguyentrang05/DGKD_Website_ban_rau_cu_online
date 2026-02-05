@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Chi tiết đơn hàng #{{ $order->order_code }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> In đơn hàng
            </button>
        </div>
    </div>

    <!-- Order Status Timeline -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Trạng thái đơn hàng</h6>
        </div>
        <div class="card-body">
            <div class="timeline">
                <div class="timeline-item {{ $order->status == 'pending' ? 'active' : ($order->status != 'cancelled' ? 'completed' : '') }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Chờ xử lý</h6>
                        <p class="timeline-text">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="timeline-item {{ $order->status == 'confirmed' ? 'active' : (in_array($order->status, ['shipping', 'delivered']) ? 'completed' : '') }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Đã xác nhận</h6>
                        <p class="timeline-text">{{ $order->confirmed_at ? $order->confirmed_at->format('d/m/Y H:i') : '--' }}</p>
                    </div>
                </div>
                <div class="timeline-item {{ $order->status == 'shipping' ? 'active' : ($order->status == 'delivered' ? 'completed' : '') }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Đang giao hàng</h6>
                        <p class="timeline-text">{{ $order->shipped_at ? $order->shipped_at->format('d/m/Y H:i') : '--' }}</p>
                    </div>
                </div>
                <div class="timeline-item {{ $order->status == 'delivered' ? 'active completed' : '' }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Đã giao hàng</h6>
                        <p class="timeline-text">{{ $order->delivered_at ? $order->delivered_at->format('d/m/Y H:i') : '--' }}</p>
                    </div>
                </div>
                @if($order->status == 'cancelled')
                <div class="timeline-item cancelled active">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Đã hủy</h6>
                        <p class="timeline-text">{{ $order->cancelled_at ? $order->cancelled_at->format('d/m/Y H:i') : '--' }}</p>
                    </div>
                </div>
                @endif
            </div>

            @if($order->status != 'cancelled' && $order->status != 'delivered')
            <div class="mt-4">
                <h6 class="mb-3">Cập nhật trạng thái:</h6>
                <div class="btn-group" role="group">
                    @if($order->status == 'pending')
                        <button type="button" class="btn btn-info btn-sm"
                                onclick="updateOrderStatus({{ $order->id }}, 'confirmed')">
                            <i class="fas fa-check"></i> Xác nhận đơn hàng
                        </button>
                    @endif
                    @if($order->status == 'confirmed')
                        <button type="button" class="btn btn-primary btn-sm"
                                onclick="updateOrderStatus({{ $order->id }}, 'shipping')">
                            <i class="fas fa-truck"></i> Bắt đầu giao hàng
                        </button>
                    @endif
                    @if($order->status == 'shipping')
                        <button type="button" class="btn btn-success btn-sm"
                                onclick="updateOrderStatus({{ $order->id }}, 'delivered')">
                            <i class="fas fa-check-circle"></i> Đã giao hàng
                        </button>
                    @endif
                    <button type="button" class="btn btn-danger btn-sm"
                            onclick="updateOrderStatus({{ $order->id }}, 'cancelled')">
                        <i class="fas fa-times"></i> Hủy đơn hàng
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <!-- Order Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Mã đơn hàng:</strong></td>
                                    <td>#{{ $order->order_code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày đặt:</strong></td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge bg-info">Đã xác nhận</span>
                                                @break
                                            @case('shipping')
                                                <span class="badge bg-primary">Đang giao</span>
                                                @break
                                            @case('delivered')
                                                <span class="badge bg-success">Đã giao</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Đã hủy</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Phương thức thanh toán:</strong></td>
                                    <td>
                                        @switch($order->payment_method)
                                            @case('cod')
                                                <span class="badge bg-secondary">Thanh toán khi nhận hàng</span>
                                                @break
                                            @case('bank_transfer')
                                                <span class="badge bg-info">Chuyển khoản ngân hàng</span>
                                                @break
                                            @case('momo')
                                                <span class="badge bg-danger">Ví MoMo</span>
                                                @break
                                            @case('vnpay')
                                                <span class="badge bg-primary">VNPay</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái thanh toán:</strong></td>
                                    <td>
                                        @if($order->payment_status == 'paid')
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        @elseif($order->payment_status == 'pending')
                                            <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                        @else
                                            <span class="badge bg-danger">Chưa thanh toán</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Ghi chú:</strong></td>
                                    <td>{{ $order->notes ?: 'Không có ghi chú' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Chi tiết sản phẩm</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th width="15%">Đơn giá</th>
                                    <th width="10%">Số lượng</th>
                                    <th width="15%">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderDetails as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('storage/products/' . $item->product->image) }}"
                                                     alt="{{ $item->product_name }}"
                                                     class="rounded me-3"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                @if($item->product)
                                                    <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td><strong>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle mb-2">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="mb-1">{{ $order->customer_name }}</h5>
                        <p class="text-muted small">{{ $order->customer_email }}</p>
                    </div>

                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><i class="fas fa-phone text-muted me-2"></i></td>
                            <td>{{ $order->customer_phone }}</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-map-marker-alt text-muted me-2"></i></td>
                            <td>{{ $order->shipping_address }}</td>
                        </tr>
                        @if($order->user)
                        <tr>
                            <td><i class="fas fa-calendar text-muted me-2"></i></td>
                            <td>Khách hàng từ {{ $order->user->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-shopping-cart text-muted me-2"></i></td>
                            <td>{{ $order->user->orders->count() }} đơn hàng</td>
                        </tr>
                        @endif
                    </table>

                    @if($order->user)
                    <div class="mt-3">
                        <a href="{{ route('admin.users.show', $order->user->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user"></i> Xem hồ sơ khách hàng
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tổng kết đơn hàng</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td>Tạm tính:</td>
                            <td class="text-end">{{ number_format($order->subtotal, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td>Phí vận chuyển:</td>
                            <td class="text-end">{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td>Giảm giá:</td>
                            <td class="text-end text-success">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Thuế:</td>
                            <td class="text-end">{{ number_format($order->tax_amount, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Tổng cộng:</strong></td>
                            <td class="text-end"><strong class="text-success fs-5">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao tác</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> In đơn hàng
                        </button>
                        <button type="button" class="btn btn-info" onclick="sendEmailToCustomer()">
                            <i class="fas fa-envelope"></i> Gửi email khách hàng
                        </button>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                            <i class="fas fa-sticky-note"></i> Thêm ghi chú
                        </button>
                        @if($order->status != 'cancelled')
                        <button type="button" class="btn btn-danger" onclick="cancelOrder()">
                            <i class="fas fa-ban"></i> Hủy đơn hàng
                        </button>
                        @endif

                        @if($order->payment_status != 'paid' && $order->status != 'cancelled')
                        <form id="confirmPaymentForm" action="{{ route('admin.orders.confirmPayment', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success mt-2" onclick="return confirm('Xác nhận đã nhận thanh toán cho đơn hàng này?');">
                                <i class="fas fa-money-bill-wave"></i> Xác nhận thanh toán
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng này?</p>
                <div id="statusUpdateDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm ghi chú</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.orders.add-note', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="note" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="note" name="note" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm ghi chú</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentOrderId = {{ $order->id }};
let currentStatus = null;

function updateOrderStatus(orderId, status) {
    currentStatus = status;

    const statusText = {
        'confirmed': 'Đã xác nhận',
        'shipping': 'Đang giao hàng',
        'delivered': 'Đã giao hàng',
        'cancelled': 'Đã hủy'
    };

    document.getElementById('statusUpdateDetails').innerHTML =
        `<strong>Trạng thái mới:</strong> <span class="badge bg-primary">${statusText[status]}</span>`;

    const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    modal.show();
}

document.getElementById('confirmStatusUpdate').addEventListener('click', function() {
    if (currentStatus) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/orders/${currentOrderId}/update-status`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'PATCH';
        form.appendChild(method);

        const status = document.createElement('input');
        status.type = 'hidden';
        status.name = 'status';
        status.value = currentStatus;
        form.appendChild(status);

        document.body.appendChild(form);
        form.submit();
    }
});

function sendEmailToCustomer() {
    if (confirm('Bạn có muốn gửi email thông báo cho khách hàng?')) {
        fetch(`/admin/orders/${currentOrderId}/send-email`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Email đã được gửi thành công!');
            } else {
                alert('Có lỗi xảy ra khi gửi email!');
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra khi gửi email!');
        });
    }
}

function cancelOrder() {
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        updateOrderStatus(currentOrderId, 'cancelled');
    }
}

// Print styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('print-mode');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('print-mode');
});
</script>
@endpush

@push('styles')
<style>
/* Timeline Styles */
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 70px;
}

.timeline-marker {
    position: absolute;
    left: 22px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #e3e6f0;
    border: 3px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-item.completed .timeline-marker {
    background: #1cc88a;
}

.timeline-item.active .timeline-marker {
    background: #4e73df;
    animation: pulse 2s infinite;
}

.timeline-item.cancelled .timeline-marker {
    background: #e74a3b;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 0;
    font-size: 12px;
    color: #6c757d;
}

.timeline-item.completed .timeline-title {
    color: #1cc88a;
}

.timeline-item.active .timeline-title {
    color: #4e73df;
}

.timeline-item.cancelled .timeline-title {
    color: #e74a3b;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(78, 115, 223, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(78, 115, 223, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(78, 115, 223, 0);
    }
}

/* Avatar Circle */
.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #f8f9fc;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 20px;
    color: #5a5c69;
}

/* Card Styles */
.card {
    border: none;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.table-borderless td {
    border: none;
    padding: 0.5rem 0;
}

.badge {
    font-size: 0.75em;
    padding: 0.375em 0.75em;
}

/* Print Styles */
@media print {
    .print-mode .btn,
    .print-mode .breadcrumb,
    .print-mode .card-header,
    .print-mode .timeline .btn-group {
        display: none !important;
    }

    .print-mode .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }

    .print-mode .timeline::before {
        background: #000 !important;
    }

    .print-mode .timeline-marker {
        background: #000 !important;
        border-color: #fff !important;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .timeline {
        padding-left: 20px;
    }

    .timeline::before {
        left: 20px;
    }

    .timeline-item {
        padding-left: 50px;
    }

    .timeline-marker {
        left: 12px;
    }
}
</style>
@endpush
