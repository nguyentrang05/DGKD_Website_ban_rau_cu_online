<!doctype html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title','Document')</title>
    <style>
        /* Basic PDF styles - keep inline for dompdf */
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; color:#222; }
        .header { display:flex; align-items:center; border-bottom:1px solid #ddd; padding-bottom:10px; margin-bottom:10px; }
        .logo { width:80px; margin-right:12px; }
        .company { font-weight:700; font-size:16px; }
        .meta { margin-left:auto; text-align:right; font-size:11px; color:#555; }
        table { width:100%; border-collapse:collapse; margin-top:8px; }
        th, td { border:1px solid #ddd; padding:6px 8px; text-align:left; font-size:11px; }
        th { background:#f5f5f5; font-weight:700; }
        .text-right { text-align:right; }
        .summary { margin-top:12px; }
        .footer { position: fixed; bottom: 0; left: 0; right:0; text-align:center; font-size:11px; color:#777; border-top:1px solid #eee; padding-top:6px; }
        .page-number { float:right; }
        @page { margin: 20mm }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            @if(file_exists(public_path('images/avatars/logo.png')))
                <img src="{{ public_path('images/avatars/logo.png') }}" alt="logo" style="width:80px;" />
            @endif
        </div>
        <div>
            <div class="company">Rau Củ Quả Sạch</div>
            <div class="small text-muted">Địa chỉ: Số X, Phường Y, Thành phố Z</div>
            <div class="small text-muted">Điện thoại: 0123 456 789</div>
        </div>
        <div class="meta">
            <div>@yield('meta')</div>
        </div>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        <span>Rau Củ Quả Sạch — © {{ date('Y') }}</span>
        <span class="page-number">Trang <span class="page"></span></span>
    </div>

    {{-- Dompdf page number script --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $pdf->page_text(520, 820, "Trang {PAGE_NUM} / {PAGE_COUNT}", $font, 10, array(0,0,0));
        }
    </script>
</body>
</html>
