<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Tính tổng doanh thu từ các đơn hàng đã giao
        $totalRevenue = Order::where('status', 'delivered')->sum('total');
        
        // Đếm tổng số đơn hàng
        $orderCount = Order::count();
        
        // Đếm số khách hàng đang hoạt động
        $userCount = User::where('role', 'user')
            ->where('active', 1)
            ->count();
        
        // Đếm số sản phẩm đang hoạt động
        $productCount = Product::where('active', 1)->count();
        
        // Lấy 5 đơn hàng gần nhất
        $recentOrders = Order::with(['user', 'items.product'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Tính top 5 sản phẩm bán chậy nhất
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'products.id',
                'products.name',
                'products.image',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
        
        // Dữ liệu doanh thu 12 tháng gần nhất cho biểu đồ
        $monthlyRevenue = Order::where('status', 'delivered')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Chuẩn bị dữ liệu cho Chart.js (12 tháng)
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            
            // Tìm doanh thu tương ứng
            $revenue = $monthlyRevenue->where('month', $month)
                ->where('year', $year)
                ->first();
            
            $chartLabels[] = 'Tháng ' . $month . '/' . $year;
            $chartData[] = $revenue ? $revenue->revenue : 0;
        }
        
        // Thống kê đơn hàng theo trạng thái
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Đếm đơn hàng theo từng trạng thái
        $pendingOrders = $ordersByStatus['pending'] ?? 0;
        $processingOrders = $ordersByStatus['processing'] ?? 0;
        $shippingOrders = $ordersByStatus['shipping'] ?? 0;
        $deliveredOrders = $ordersByStatus['delivered'] ?? 0;
        $cancelledOrders = $ordersByStatus['cancelled'] ?? 0;
        
        // Thống kê khách hàng theo loại (Admin vs Customer)
        $adminCount = User::where('role', 'admin')->where('active', 1)->count();
        
        // Dữ liệu cho customer chart (chỉ đếm số lượng)
        $customerChartData = [$userCount, $adminCount];
        
        // Tính xu hướng so với tháng trước (cho các chỉ số chính)
        $lastMonth = Carbon::now()->subMonth();
        
        // Doanh thu tháng trước
        $lastMonthRevenue = Order::where('status', 'delivered')
            ->whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->sum('total');
        
        // Doanh thu tháng này
        $thisMonthRevenue = Order::where('status', 'delivered')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total');
        
        // Tính phần trăm thay đổi
        $revenueTrend = 0;
        if ($lastMonthRevenue > 0) {
            $revenueTrend = round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
        } elseif ($thisMonthRevenue > 0) {
            $revenueTrend = 100;
        }
        
        // Đơn hàng tháng trước
        $lastMonthOrders = Order::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();
        
        // Đơn hàng tháng này
        $thisMonthOrders = Order::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        
        // Tính phần trăm thay đổi
        $orderTrend = 0;
        if ($lastMonthOrders > 0) {
            $orderTrend = round((($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1);
        } elseif ($thisMonthOrders > 0) {
            $orderTrend = 100;
        }
        
        return view('admin.index', compact(
            'totalRevenue',
            'orderCount',
            'userCount',
            'productCount',
            'recentOrders',
            'topProducts',
            'chartLabels',
            'chartData',
            'customerChartData',
            'pendingOrders',
            'processingOrders',
            'shippingOrders',
            'deliveredOrders',
            'cancelledOrders',
            'revenueTrend',
            'orderTrend'
        ));
    }
}
