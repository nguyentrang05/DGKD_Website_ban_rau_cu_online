@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Chi tiết đơn hàng #{{ $order->id }}</h2>
                    <p class="text-muted mb-0">Đặt hàng ngày {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>

            <div class="row">
                <!-- Thông tin đơn hàng -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-box"></i> Sản phẩm đã đặt
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderItems as $detail)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($detail->product->image_url)
                                                            <img src="{{ asset('storage/' . $detail->product->image_url) }}"
                                                                 alt="{{ $detail->product->name }}"
                                                                 class="rounded"
                                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                                 style="width: 60px; height: 60px;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ $detail->product->name }}</h6>
                                                        <small class="text-muted">
                                                            {{ $detail->product->category->name ?? 'Chưa phân loại' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-light text-dark">{{ $detail->quantity }}</span>
                                            </td>
                                            <td class="text-end align-middle">
                                                {{ number_format($detail->price, 0, ',', '.') }}đ
                                            </td>
                                            <td class="text-end align-middle">
                                                <strong>{{ number_format($detail->price * $detail->quantity, 0, ',', '.') }}đ</strong>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin giao hàng -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-truck"></i> Thông tin giao hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">Người nhận</h6>
                                    <p class="mb-3">
                                        <strong>{{ $order->shipping_name }}</strong><br>
                                        <i class="fas fa-phone text-muted me-1"></i> {{ $order->shipping_phone }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">Địa chỉ giao hàng</h6>
                                    <p class="mb-3">
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                        {{ $order->shipping_address }}
                                    </p>
                                </div>
                            </div>

                            @if($order->notes)
                            <div class="mt-3">
                                <h6 class="text-muted mb-2">Ghi chú</h6>
                                <p class="mb-0 p-3 bg-light rounded">
                                    <i class="fas fa-sticky-note text-muted me-1"></i>
                                    {{ $order->notes }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
.status-timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -23px;
    top: 8px;
    bottom: -12px;
    width: 2px;
    background: #e9ecef;
}

.timeline-item:last-child:before {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #e9ecef;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-item.completed .timeline-marker {
    background: #28a745;
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-item.completed:before {
    background: #28a745;
}

.timeline-content {
    padding-left: 0;
}

.text-sm {
    font-size: 0.875rem;
}
</style>
@endsection
