<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class paymentController extends Controller
{
    public function index(Request $request)
    {
        $allOrders = order::with('shoeProduct')->orderBy('created_at', 'desc')->get();

        $query = payment::with('order');
        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }
        if ($request->filled('customer_name')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->customer_name . '%');
            });
        }
        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);

        return view('payment.payment', compact('payments', 'allOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount_given' => 'required|numeric|min:0.01',
        ]);

        $order = order::findOrFail($request->order_id);
        $remaining = $order->remaining_balance;
        $amountGiven = $request->amount_given;

        $change = 0;
        $amountToApply = $amountGiven;
        if ($amountGiven > $remaining) {
            $change = $amountGiven - $remaining;
            $amountToApply = $remaining;
        }

        payment::create([
            'order_id' => $order->id,
            'amount_paid' => $amountToApply,
            'change_given' => $change,
        ]);

        $order->paid_amount += $amountToApply;
        $order->save();
        $order->updatePaymentStatus(); 

        $message = "Payment of ₱" . number_format($amountToApply,2) . " processed.";
        if ($change > 0) $message .= " Change: ₱" . number_format($change,2);
        if ($order->payment_status === 'paid') $message .= " Order is now fully paid.";

        return redirect()->route('payments.index')->with('success', $message);
    }

    public function edit(payment $payment)
    {
        $payment->load('order');
        return view('payment.editpayment', compact('payment'));
    }

    public function update(Request $request, payment $payment)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'change_given' => 'nullable|numeric|min:0',
        ]);

        $oldAmount = $payment->amount_paid;
        $newAmount = $validated['amount_paid'];
        $diff = $newAmount - $oldAmount;

        $order = $payment->order;
        $newPaidAmount = $order->paid_amount + $diff;

        if ($newPaidAmount < 0 || $newPaidAmount > $order->total_price) {
            return back()->withErrors(['amount_paid' => 'Invalid payment amount.']);
        }

        $payment->update([
            'amount_paid' => $newAmount,
            'change_given' => $validated['change_given'] ?? 0,
        ]);

        $order->paid_amount = $newPaidAmount;
        $order->save();
        $order->updatePaymentStatus(); 

        return redirect()->route('payments.index')->with('success', 'Payment updated.');
    }

    public function destroy(payment $payment)
    {
        $order = $payment->order;
        $order->paid_amount -= $payment->amount_paid;
        if ($order->paid_amount < 0) $order->paid_amount = 0;
        $order->save();
        $order->updatePaymentStatus(); 

        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }
}