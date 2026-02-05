@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Quản lý đơn hàng</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Đơn hàng</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng đơn hàng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOrders ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đơn đã hoàn thành
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedOrders ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Đơn chờ xử lý
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingOrders ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Doanh thu tháng này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }}đ
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn hàng</h6>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download"></i> Xuất dữ liệu
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="">
                        <i class="fas fa-file-excel"></i> Excel
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.orders.export.pdf') }}">
                        <i class="fas fa-file-pdf"></i> PDF (toàn bộ)
                    </a></li>
                </ul>
            </div>
        </div>
    <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Mã đơn hàng</th>
                                <th width="15%">Khách hàng</th>
                                <th width="10%">Ngày đặt</th>
                                <th width="10%">Tổng tiền</th>
                                <th width="12%">Trạng thái</th>
                                <th width="15%">Phương thức TT</th>
                                <th width="13%">Trạng thái TT</th>
                                <th width="10%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $index => $order)
                            <tr>
                                <td>{{ $orders->firstItem() + $index }}</td>
                                <td>
                                    <strong class="text-primary">#{{ $order->id }}</strong>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $order->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $order->customer_email }}</small>
                                    </div>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong class="text-success">
                                        {{ number_format($order->total_price, 0, ',', '.') }}đ
                                    </strong>
                                </td>
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
                                        @case('completed')
                                            <span class="badge bg-success">Đã giao</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">Đã hủy</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">Không xác định</span>
                                    @endswitch
                                </td>
                                <td>
                                    @switch($order->payment_method)
                                        @case('COD')
                                            <span class="badge bg-secondary">COD</span>
                                            @break
                                        @case('bank_transfer')
                                            <span class="badge bg-info">Chuyển khoản</span>
                                            @break
                                        @case('momo')
                                            <span class="badge bg-danger">MoMo</span>
                                            @break
                                        @case('vnpay')
                                            <span class="badge bg-primary">VNPay</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">Khác</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($order->payment_status == 'paid')
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    @elseif($order->payment_status == 'pending')
                                        <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                    @else
                                        <span class="badge bg-danger">Chưa thanh toán</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group" aria-label="Actions">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if($order->payment_status != 'paid' && $order->status != 'cancelled')
                                            <form action="{{ route('admin.orders.confirmPayment', $order->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Xác nhận đã nhận thanh toán cho đơn hàng #{{ $order->id }}?');" title="Xác nhận thanh toán">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4"></div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không tìm thấy đơn hàng nào</h5>
                    <p class="text-muted">Thử thay đổi bộ lọc tìm kiếm của bạn</p>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Hiển thị {{ $orders->firstItem() }}-{{ $orders->lastItem() }}
                    trong tổng số {{ $orders->total() }} đơn hàng
                </div>
                {{ $orders->appends(request()->query())->links('pagination::bootstrap-4') }}
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

@endsection

@push('scripts')
<script>
let currentOrderId = null;
let currentStatus = null;

// function updateOrderStatus(orderId, status) {
//     currentOrderId = orderId;
//     currentStatus = status;

//     const statusText = {
//         'confirmed': 'Đã xác nhận',
//         'shipping': 'Đang giao hàng',
//         'delivered': 'Đã giao hàng',
//         'cancelled': 'Đã hủy'
//     };

//     document.getElementById('statusUpdateDetails').innerHTML =
//         `<strong>Trạng thái mới:</strong> <span class="badge bg-primary">${statusText[status]}</span>`;

//     const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
//     modal.show();
// }

// document.getElementById('confirmStatusUpdate').addEventListener('click', function() {
//     if (currentOrderId && currentStatus) {
//         // Tạo form để submit
//         const form = document.createElement('form');
//         form.method = 'POST';
//         form.action = `/admin/orders/${currentOrderId}/update-status`;

//         // CSRF token
//         const csrfToken = document.createElement('input');
//         csrfToken.type = 'hidden';
//         csrfToken.name = '_token';
//         csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
//         form.appendChild(csrfToken);

//         // Method
//         const method = document.createElement('input');
//         method.type = 'hidden';
//         method.name = '_method';
//         method.value = 'PATCH';
//         form.appendChild(method);

//         // Status
//         const status = document.createElement('input');
//         status.type = 'hidden';
//         status.name = 'status';
//         status.value = currentStatus;
//         form.appendChild(status);

//         document.body.appendChild(form);
//         form.submit();
//     }
// });

// Auto-hide alerts after 5 seconds
// document.addEventListener('DOMContentLoaded', function() {
//     const alerts = document.querySelectorAll('.alert');
//     alerts.forEach(function(alert) {
//         setTimeout(function() {
//             alert.style.transition = 'opacity 0.5s';
//             alert.style.opacity = '0';
//             setTimeout(function() {
//                 alert.remove();
//             }, 500);
//         }, 5000);
//     });
// });
</script>
@endpush

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}

.badge {
    font-size: 0.75em;
}

.btn-group .dropdown-toggle::after {
    margin-left: 0.255em;
}

.card {
    border: none;
    border-radius: 0.35rem;
}

.card-header {
    border-bottom: 1px solid #e3e6f0;
    background-color: #f8f9fc;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fc;
}

.dropdown-item:hover {
    background-color: #f8f9fc;
}

.text-xs {
    font-size: 0.7rem;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}
</style>
@endpush
