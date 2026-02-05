@extends('pdf.layout')

@section('title', 'Danh sách đơn hàng (PDF)')
@section('meta', 'Ngày xuất: ' . now()->format('d/m/Y H:i'))

@section('content')
    <h3>Danh sách đơn hàng</h3>
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:12%">Mã đơn</th>
                <th>Khách hàng</th>
                <th style="width:18%">Ngày đặt</th>
                <th style="width:12%" class="text-right">Tổng tiền</th>
                <th style="width:12%">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $i => $order)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->customer_name ?? ($order->user->name ?? '') }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-right">{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                <td>{{ ucfirst($order->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="summary">
        <strong>Tổng đơn: </strong> {{ $orders->count() }}
    </div>
@endsection
