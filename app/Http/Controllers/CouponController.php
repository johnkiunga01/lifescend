<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::with('event', 'createdBy')
            ->withCount('usages')
            ->latest()
            ->get();

        return view('manage.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $events = Event::orderBy('name')->get(['id', 'name', 'start_date']);
        return view('manage.coupons.create', compact('events'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string|max:500',
            'event_id' => 'nullable|exists:events,id',
            'type' => 'required|in:amount,percentage',
            'value' => 'required|numeric|min:0.01|max:' . ($request->type === 'percentage' ? '100' : '999999'),
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['created_by'] = Auth::id();
        $data['is_active'] = $request->boolean('is_active', true);

        Coupon::create($data);

        return redirect()->route('manage.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        $events = Event::orderBy('name')->get(['id', 'name', 'start_date']);
        return view('manage.coupons.edit', compact('coupon', 'events'));
    }

    public function update(Request $request, int $id)
    {
        $coupon = Coupon::findOrFail($id);

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string|max:500',
            'event_id' => 'nullable|exists:events,id',
            'type' => 'required|in:amount,percentage',
            'value' => 'required|numeric|min:0.01|max:' . ($request->type === 'percentage' ? '100' : '999999'),
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);

        $coupon->update($data);

        return redirect()->route('manage.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(int $id)
    {
        $coupon = Coupon::withCount('usages')->findOrFail($id);

        if ($coupon->usages_count > 0) {
            return back()->withErrors(['error' => 'Cannot delete coupon with existing usage records.']);
        }

        $coupon->delete();

        return redirect()->route('manage.coupons.index')->with('success', 'Coupon deleted.');
    }

    public function validateCoupon(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50',
            'event_id' => 'required|exists:events,id',
            'tickets' => 'required|array|min:1',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'tickets.*.quantity' => 'required|integer|min:1|max:10',
        ]);

        $coupon = Coupon::where('code', strtoupper($data['code']))->first();

        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Coupon code not found.'], 404);
        }

        $error = $coupon->validationError($data['event_id'], Auth::id());

        if ($error) {
            return response()->json(['valid' => false, 'message' => $error], 422);
        }

        $orderAmount = 0;
        foreach ($data['tickets'] as $entry) {
            $tt = \App\Models\TicketType::findOrFail($entry['ticket_type_id']);
            $orderAmount += $tt->price * (int) $entry['quantity'];
        }

        $discount = $coupon->calculateDiscount($orderAmount);

        if ($discount <= 0) {
            return response()->json(['valid' => false, 'message' => 'Minimum order amount not met.'], 422);
        }

        return response()->json([
            'valid' => true,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
            ],
            'discount' => $discount,
            'total_before' => $orderAmount,
            'total_after' => max(0, $orderAmount - $discount),
        ]);
    }
}
