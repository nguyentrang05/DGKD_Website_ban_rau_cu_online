@extends('pdf.layout')

@section('title', 'Báo cáo doanh thu (PDF)')
@section('meta', 'Tháng ' . $month . ' / ' . $year)

@section('content')
    <h3>Báo cáo doanh thu - Tháng {{ $month }}/{{ $year }}</h3>
    <p>Tổng doanh thu: <strong>{{ number_format($total, 0, ',', '.') }}đ</strong></p>

    <table>
        <thead>
            <tr>
                <th style="width:6%">#</th>
                <th style="width:18%">Mã đơn</th>
                <th>Ngày</th>
                <th style="width:18%" class="text-right">Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $i => $order)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
