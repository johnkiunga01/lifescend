<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\CheckIn;
use App\Models\Attendee;
use App\Models\Ticket;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $cards = [
            [
                'title' => 'Sales Overview',
                'description' => 'Revenue, orders, and payment trends',
                'icon' => 'sales',
                'route' => route('manage.reports.sales'),
                'color' => 'emerald',
                'stat' => '$' . number_format(Order::where('status', 'completed')->sum('total_amount'), 2),
                'statLabel' => 'Total Revenue',
            ],
            [
                'title' => 'Event Performance',
                'description' => 'Tickets sold, revenue, and attendance by event',
                'icon' => 'events',
                'route' => route('manage.reports.events'),
                'color' => 'blue',
                'stat' => Event::count(),
                'statLabel' => 'Total Events',
            ],
            [
                'title' => 'Attendance Report',
                'description' => 'Check-in rates, no-shows, and daily attendance',
                'icon' => 'attendance',
                'route' => route('manage.reports.attendance'),
                'color' => 'amber',
                'stat' => CheckIn::count(),
                'statLabel' => 'Total Check-Ins',
            ],
            [
                'title' => 'Customer Insights',
                'description' => 'Top customers, purchase patterns, and demographics',
                'icon' => 'customers',
                'route' => route('manage.reports.customers'),
                'color' => 'purple',
                'stat' => User::has('orders')->count(),
                'statLabel' => 'Paying Customers',
            ],
            [
                'title' => 'Coupon Analytics',
                'description' => 'Coupon usage, discount impact, and redemption rates',
                'icon' => 'coupons',
                'route' => route('manage.reports.coupons'),
                'color' => 'rose',
                'stat' => CouponUsage::count(),
                'statLabel' => 'Coupon Redemptions',
            ],
            [
                'title' => 'Revenue Breakdown',
                'description' => 'Revenue by payment method, event type, and ticket tier',
                'icon' => 'revenue',
                'route' => route('manage.reports.revenue'),
                'color' => 'teal',
                'stat' => Order::where('status', 'completed')->count(),
                'statLabel' => 'Paid Orders',
            ],
        ];

        $quickStats = [
            'todayRevenue' => Order::where('status', 'completed')->whereDate('created_at', today())->sum('total_amount'),
            'todayOrders' => Order::whereDate('created_at', today())->count(),
            'todayCheckIns' => CheckIn::whereDate('checked_in_at', today())->count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
        ];

        return view('manage.reports.index', compact('cards', 'quickStats'));
    }

    public function sales(Request $request)
    {
        $period = $request->get('period', '30d');
        $startDate = match ($period) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '12m' => now()->subYear(),
            default => now()->subDays(30),
        };

        $orders = Order::with(['user', 'event', 'items.ticketType'])
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->latest()
            ->paginate(25);

        $dailyRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totals = [
            'revenue' => $orders->sum('total_amount') ?: Order::where('status', 'completed')->where('created_at', '>=', $startDate)->sum('total_amount'),
            'count' => Order::where('status', 'completed')->where('created_at', '>=', $startDate)->count(),
            'avg' => Order::where('status', 'completed')->where('created_at', '>=', $startDate)->avg('total_amount') ?? 0,
        ];

        return view('manage.reports.sales', compact('orders', 'dailyRevenue', 'totals', 'period'));
    }

    public function events()
    {
        $events = Event::withCount(['orders as completed_orders' => fn($q) => $q->where('status', 'completed')])
            ->withCount(['checkIns', 'tickets', 'attendees'])
            ->withSum(['orders as revenue' => fn($q) => $q->where('status', 'completed')], 'total_amount')
            ->orderByDesc('revenue')
            ->get();

        return view('manage.reports.events', compact('events'));
    }

    public function attendance(Request $request)
    {
        $eventId = $request->get('event_id');
        $events = Event::orderBy('start_date', 'desc')->get();

        $query = CheckIn::with(['user', 'ticket', 'event', 'checkedInBy'])->latest('checked_in_at');

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $checkIns = $query->paginate(30);

        $stats = [
            'total' => CheckIn::count(),
            'today' => CheckIn::whereDate('checked_in_at', today())->count(),
            'avg_per_event' => Event::has('checkIns')->withCount('checkIns')->get()->avg('check_ins_count') ?? 0,
        ];

        return view('manage.reports.attendance', compact('checkIns', 'events', 'eventId', 'stats'));
    }

    public function customers()
    {
        $topCustomers = User::whereHas('orders', fn($q) => $q->where('status', 'completed'))
            ->withCount('orders')
            ->withSum(['orders as total_spent' => fn($q) => $q->where('status', 'completed')], 'total_amount')
            ->withCount('tickets')
            ->orderByDesc('total_spent')
            ->take(50)
            ->get();

        return view('manage.reports.customers', compact('topCustomers'));
    }

    public function coupons()
    {
        $coupons = Coupon::withCount('usages')
            ->withSum('usages', 'discount_amount')
            ->latest()
            ->get();

        $totalDiscount = CouponUsage::sum('discount_amount');
        $totalRedemptions = CouponUsage::count();
        $activeCoupons = Coupon::where('is_active', true)->count();

        return view('manage.reports.coupons', compact('coupons', 'totalDiscount', 'totalRedemptions', 'activeCoupons'));
    }

    public function revenue(Request $request)
    {
        $period = $request->get('period', '30d');
        $startDate = match ($period) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '12m' => now()->subYear(),
            default => now()->subDays(30),
        };

        $byMethod = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw("CASE WHEN stripe_payment_intent_id LIKE 'pi_simulated_%' THEN 'Direct' ELSE 'Stripe' END as method, SUM(total_amount) as revenue, COUNT(*) as count")
            ->groupBy('method')
            ->get();

        $byEventType = DB::table('orders')
            ->join('events', 'orders.event_id', '=', 'events.id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $startDate)
            ->selectRaw('events.event_type, SUM(orders.total_amount) as revenue, COUNT(*) as count')
            ->groupBy('events.event_type')
            ->orderByDesc('revenue')
            ->get();

        $byTicketType = DB::table('order_items')
            ->join('ticket_types', 'order_items.ticket_type_id', '=', 'ticket_types.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $startDate)
            ->selectRaw('ticket_types.name, SUM(order_items.total_price) as revenue, SUM(order_items.quantity) as sold')
            ->groupBy('ticket_types.name')
            ->orderByDesc('revenue')
            ->get();

        return view('manage.reports.revenue', compact('byMethod', 'byEventType', 'byTicketType', 'period'));
    }
}
