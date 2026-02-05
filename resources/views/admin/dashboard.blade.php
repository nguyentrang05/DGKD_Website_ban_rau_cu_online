@extends('layouts.admin')

@section('title', 'Dashboard - Quản Trị')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#quickStatsModal">
                <i class="fas fa-chart-line me-1"></i>Thống Kê Chi Tiết
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-calendar me-1"></i>Hôm Nay
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Hôm Nay</a></li>
                    <li><a class="dropdown-item" href="#">7 Ngày Qua</a></li>
                    <li><a class="dropdown-item" href="#">30 Ngày Qua</a></li>
                    <li><a class="dropdown-item" href="#">Tháng Này</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Summary Cards Row -->
    <div class="row mb-4">
        <!-- Total Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Doanh Thu Hôm Nay
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">2,450,000₫</div>
                            <div class="small text-success">
                                <i class="fas fa-arrow-up"></i> 12.5% so với hôm qua
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                        <!-- Export Revenue Modal -->
                        <div class="modal fade" id="exportRevenueModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Xuất báo cáo doanh thu (PDF)</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="GET" action="{{ route('admin.reports.revenue.export.pdf') }}">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="month" class="form-label">Tháng</label>
                                                <select id="month" name="month" class="form-select">
                                                    @for($m=1;$m<=12;$m++)
                                                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ $m }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="year" class="form-label">Năm</label>
                                                <select id="year" name="year" class="form-select">
                                                    @for($y = now()->year - 2; $y <= now()->year; $y++)
                                                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-primary">Xuất PDF</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Đơn Hàng Hôm Nay
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">42</div>
                            <div class="small text-success">
                                <i class="fas fa-arrow-up"></i> 8 đơn hàng mới
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Tổng Sản Phẩm
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">156</div>
                            <div class="small text-warning">
                                <i class="fas fa-exclamation-triangle"></i> 12 sản phẩm sắp hết
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-leaf fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Khách Hàng
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">1,245</div>
                            <div class="small text-success">
                                <i class="fas fa-user-plus"></i> 15 khách mới tuần này
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-area me-2"></i>Biểu Đồ Doanh Thu
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow">
                            <a class="dropdown-item" href="#">Xuất PDF</a>
                            <a class="dropdown-item" href="#">Xuất Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart" height="320"></canvas>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col">
                            <div class="text-xs text-uppercase text-muted mb-1">Tuần Này</div>
                            <div class="h6 mb-0">15,250,000₫</div>
                        </div>
                        <div class="col">
                            <div class="text-xs text-uppercase text-muted mb-1">Tháng Này</div>
                            <div class="h6 mb-0">65,890,000₫</div>
                        </div>
                        <div class="col">
                            <div class="text-xs text-uppercase text-muted mb-1">Năm Này</div>
                            <div class="h6 mb-0">456,230,000₫</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Sales Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Danh Mục Bán Chạy
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="me-2"><i class="fas fa-circle text-primary"></i> Rau Xanh</span>
                        <span class="me-2"><i class="fas fa-circle text-success"></i> Củ Quả</span>
                        <span class="me-2"><i class="fas fa-circle text-info"></i> Trái Cây</span>
                        <span class="me-2"><i class="fas fa-circle text-warning"></i> Khác</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders and Quick Actions Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-list me-2"></i>Đơn Hàng Gần Đây
                    </h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm">
                        Xem Tất Cả <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Khách Hàng</th>
                                    <th>Sản Phẩm</th>
                                    <th>Tổng Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-secondary">#ORD-001</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <img class="avatar-img rounded-circle" src="https://via.placeholder.com/32" alt="">
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Nguyễn Văn A</div>
                                                <div class="text-muted small">nguyenvana@email.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rau cải xanh, Củ cà rốt</td>
                                    <td><span class="fw-bold text-success">125,000₫</span></td>
                                    <td><span class="badge bg-warning">Chờ Xác Nhận</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success" data-bs-toggle="tooltip" title="Xác Nhận">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">#ORD-002</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <img class="avatar-img rounded-circle" src="https://via.placeholder.com/32" alt="">
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Trần Thị B</div>
                                                <div class="text-muted small">tranthib@email.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Táo xanh, Cam sành</td>
                                    <td><span class="fw-bold text-success">89,000₫</span></td>
                                    <td><span class="badge bg-info">Đang Giao</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-info" data-bs-toggle="tooltip" title="Theo Dõi">
                                                <i class="fas fa-truck"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">#ORD-003</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <img class="avatar-img rounded-circle" src="https://via.placeholder.com/32" alt="">
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Lê Minh C</div>
                                                <div class="text-muted small">leminhc@email.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Khoai tây, Hành tây</td>
                                    <td><span class="fw-bold text-success">156,000₫</span></td>
                                    <td><span class="badge bg-success">Hoàn Thành</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="In Hóa Đơn">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Alerts -->
        <div class="col-lg-4 mb-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Thao Tác Nhanh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Thêm Sản Phẩm Mới
                        </a>
                        <!-- Thêm Danh Mục đã loại bỏ theo yêu cầu -->
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-warning">
                            <i class="fas fa-list-alt me-2"></i>Quản Lý Đơn Hàng
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                            <i class="fas fa-users me-2"></i>Quản Lý Khách Hàng
                        </a>
                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportRevenueModal">
                            <i class="fas fa-file-pdf me-2"></i>Xuất báo cáo doanh thu (PDF)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Alerts & Notifications -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Cảnh Báo
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-box me-1"></i>Sản phẩm sắp hết:</strong>
                        <div class="small mt-1">
                            • Rau cải xanh (còn 5 kg)<br>
                            • Cà chua (còn 8 kg)<br>
                            • Khoai tây (còn 12 kg)
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-clock me-1"></i>Đơn hàng chờ xác nhận:</strong>
                        <div class="small mt-1">Bạn có 8 đơn hàng mới cần xác nhận</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-chart-line me-1"></i>Doanh thu tăng:</strong>
                        <div class="small mt-1">Doanh thu tuần này tăng 15% so với tuần trước</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Modal -->
<div class="modal fade" id="quickStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chart-bar me-2"></i>Thống Kê Chi Tiết
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title text-primary">Sản Phẩm Bán Chạy</h5>
                                <p class="card-text">
                                    <strong>Rau cải xanh</strong><br>
                                    <span class="text-muted">245 kg đã bán tuần này</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title text-success">Khách Hàng VIP</h5>
                                <p class="card-text">
                                    <strong>Nguyễn Văn A</strong><br>
                                    <span class="text-muted">Đã mua 15 đơn hàng</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Doanh Thu Hôm Nay Theo Giờ</h6>
                        <canvas id="hourlyChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary">Xuất Báo Cáo</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
            datasets: [{
                label: 'Doanh Thu (VNĐ)',
                data: [1200000, 1900000, 3000000, 2500000, 2200000, 3300000, 2450000],
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Category Pie Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Rau Xanh', 'Củ Quả', 'Trái Cây', 'Khác'],
            datasets: [{
                data: [35, 25, 30, 10],
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(54, 185, 204, 0.8)',
                    'rgba(246, 194, 62, 0.8)'
                ],
                borderColor: [
                    'rgba(78, 115, 223, 1)',
                    'rgba(28, 200, 138, 1)',
                    'rgba(54, 185, 204, 1)',
                    'rgba(246, 194, 62, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Hourly Chart for Modal
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: ['6h', '8h', '10h', '12h', '14h', '16h', '18h', '20h'],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: [120000, 190000, 300000, 450000, 320000, 280000, 400000, 350000],
                backgroundColor: 'rgba(28, 200, 138, 0.8)',
                borderColor: 'rgba(28, 200, 138, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(value);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.avatar {
    width: 32px;
    height: 32px;
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    border: 1px solid #e3e6f0;
}

.border-start {
    border-left: 0.25rem solid !important;
}

.chart-area {
    position: relative;
    height: 320px;
}

.chart-pie {
    position: relative;
    height: 15rem;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df, #224abe);
}

.text-xs {
    font-size: 0.7rem;
}

@media (max-width: 768px) {
    .d-sm-flex {
        flex-direction: column !important;
    }

    .d-sm-flex > * {
        margin-bottom: 0.5rem;
    }

    .chart-area {
        height: 250px;
    }
}
</style>
@endpush
