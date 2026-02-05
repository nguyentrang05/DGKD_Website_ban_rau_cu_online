<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    #index
    public function index()
    {
        // Doanh thu tháng này (theo created_at và status = 'completed')
        $monthlyRevenue = Order::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');

        // Các dữ liệu khác
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        $orders = Order::latest()->paginate(10); // dùng phân trang nếu cần

        return view('admin.orders.index', compact(
            'monthlyRevenue',
            'totalOrders',
            'completedOrders',
            'orders'
        ));
    }
    #show
    public function show($id)
    {
        // Load the order with related items and user and pass the model to the view
        $order = Order::with(['orderItems.product', 'user'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Add a note to the order (from admin UI).
     */
    public function addNote(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $order = Order::findOrFail($id);

        // Overwrite or append note as desired. Here we append with a timestamp for admin trace.
        $timestamp = now()->format('d/m/Y H:i');
        $newNote = "[{$timestamp}] " . $request->input('note');

        if ($order->notes) {
            $order->notes = $order->notes . "\n" . $newNote;
        } else {
            $order->notes = $newNote;
        }

        $order->save();

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Ghi chú đã được thêm.');
    }

    /**
     * Export orders list to PDF.
     */
    public function exportPdf(Request $request)
    {
        // Fetch orders (optionally apply filters from request)
        $orders = Order::latest()->get();

        // Check for PDF generator (barryvdh/laravel-dompdf)
        if (!class_exists('Barryvdh\\DomPDF\\Facade\\Pdf') && !class_exists('PDF')) {
            return redirect()->back()->with('error', "PDF library not found. Please run `composer require barryvdh/laravel-dompdf` and register the service provider.");
        }

        try {
            if (class_exists('PDF')) {
                $pdf = \PDF::loadView('admin.orders.pdf_list', compact('orders'));
            } else {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.pdf_list', compact('orders'));
            }

            return $pdf->download('orders-' . now()->format('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Export orders PDF failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể xuất PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export monthly revenue report to PDF.
     */
    public function exportRevenuePdf(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $orders = Order::where('status', 'completed')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $total = $orders->sum('total_price');

        if (!class_exists('PDF') && !class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            return redirect()->back()->with('error', "PDF library not found. Please run `composer require barryvdh/laravel-dompdf`.");
        }

        try {
            if (class_exists('PDF')) {
                $pdf = \PDF::loadView('admin.reports.revenue_pdf', compact('orders', 'total', 'month', 'year'));
            } else {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.revenue_pdf', compact('orders', 'total', 'month', 'year'));
            }

            return $pdf->download('revenue-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.pdf');
        } catch (\Exception $e) {
            Log::error('Export revenue PDF failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể xuất báo cáo PDF: ' . $e->getMessage());
        }
    }
}
