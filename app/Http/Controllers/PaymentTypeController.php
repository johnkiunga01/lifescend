<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentType;
use App\Services\OrderFulfillmentService;
use App\Mail\TicketPurchaseMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentTypeController extends Controller
{
    public function index(Request $request)
    {
        $types = PaymentType::latest()->get();

        $query = Order::with(['user', 'event', 'items.ticketType', 'tickets', 'invoice', 'coupon'])
            ->latest('orders.created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('event', fn($e) => $e->where('name', 'like', "%{$search}%"))
                  ->orWhere('stripe_payment_intent_id', 'like', "%{$search}%")
                  ->orWhereHas('invoice', fn($i) => $i->where('invoice_number', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Order::count(),
            'completed' => Order::where('status', 'completed')->count(),
            'reserved' => Order::where('status', 'reserved')->count(),
            'pending' => Order::where('status', 'pending')->count(),
            'failed' => Order::where('status', 'failed')->count(),
            'revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'today' => Order::where('status', 'completed')->whereDate('created_at', today())->sum('total_amount'),
        ];

        return view('manage.payments.index', compact('types', 'orders', 'stats'));
    }

    public function markOrderReserved(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_method' => 'required|string|max:255|exists:payment_types,name',
        ]);

        $order->update([
            'status' => 'reserved',
            'payment_method' => $data['payment_method'],
            'reserved_at' => $order->reserved_at ?? now(),
        ]);

        return back()->with('success', "Order #{$order->id} marked as reserved ({$data['payment_method']}).");
    }

    public function markOrderPaid(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_method' => 'required|string|max:255|exists:payment_types,name',
        ]);

        if ($order->status === 'completed' && $order->tickets()->exists()) {
            return back()->with('success', "Order #{$order->id} is already paid.");
        }

        $order->update([
            'payment_method' => $data['payment_method'],
            'reserved_at' => $order->reserved_at ?? now(),
        ]);

        try {
            app(OrderFulfillmentService::class)->fulfill($order);
        } catch (\Throwable $e) {
            return back()->withErrors(['order' => 'Could not issue tickets: ' . $e->getMessage()]);
        }

        try {
            if ($order->user?->email) {
                Mail::to($order->user->email)->send(new TicketPurchaseMail($order->fresh()));
            }
        } catch (\Throwable $e) {}

        return back()->with('success', "Order #{$order->id} marked as paid ({$data['payment_method']}). Tickets issued.");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:payment_types,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        PaymentType::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('manage.payments.index')->with('success', 'Payment type created.');
    }

    public function update(Request $request, $id)
    {
        $type = PaymentType::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:payment_types,name,' . $type->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $type->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('manage.payments.index')->with('success', 'Payment type updated.');
    }

    public function destroy($id)
    {
        $type = PaymentType::findOrFail($id);
        $type->delete();

        return redirect()->route('manage.payments.index')->with('success', 'Payment type deleted.');
    }
}
