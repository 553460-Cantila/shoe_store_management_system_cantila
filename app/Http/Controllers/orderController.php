<?php

namespace App\Http\Controllers;

use App\Models\ShoeProduct;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['shoeProduct', 'user', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $shoeProducts = ShoeProduct::where('stock', '>', 0)->orderBy('name')->get();

        return view('order.order', compact('orders', 'shoeProducts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'shoe_product_id' => 'required|exists:shoe_products,id',  
            'quantity' => 'required|integer|min:1',
        ]);

        $shoeProduct = ShoeProduct::findOrFail($validated['shoe_product_id']);

        if ($shoeProduct->stock < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock. Available: ' . $shoeProduct->stock . ' pairs')
                ->withInput();
        }

        $unitPrice = $shoeProduct->price;
        $totalPrice = $unitPrice * $validated['quantity'];

        $order = Order::create([
            'customer_name' => $validated['customer_name'],
            'shoe_product_id' => $validated['shoe_product_id'],
            'user_id' => Auth::id(),
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'paid_amount' => 0,
            'order_status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $shoeProduct->reduceStock($validated['quantity']);

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'shoe_product_id' => 'required|exists:shoe_products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $oldQuantity = $order->quantity;
        $newQuantity = $validated['quantity'];

        if ($order->shoe_product_id != $validated['shoe_product_id']) {
            $oldShoeProduct = $order->shoeProduct;
            $oldShoeProduct->stock += $oldQuantity;
            $oldShoeProduct->save();

            $newShoeProduct = ShoeProduct::findOrFail($validated['shoe_product_id']);
            if ($newShoeProduct->stock < $newQuantity) {
                return back()->with('error', 'Insufficient stock for new product. Available: ' . $newShoeProduct->stock . ' pairs');
            }
            $newShoeProduct->stock -= $newQuantity;
            $newShoeProduct->save();

            $order->update([
                'customer_name' => $validated['customer_name'],
                'shoe_product_id' => $validated['shoe_product_id'],
                'quantity' => $newQuantity,
                'unit_price' => $newShoeProduct->price,
                'total_price' => $newShoeProduct->price * $newQuantity,
            ]);
        } else {
            $shoeProduct = $order->shoeProduct;
            $diff = $newQuantity - $oldQuantity;
            if ($diff > 0 && $shoeProduct->stock < $diff) {
                return back()->with('error', 'Not enough stock. Available: ' . $shoeProduct->stock . ' pairs');
            }
            $shoeProduct->stock -= $diff;
            $shoeProduct->save();

            $order->update([
                'customer_name' => $validated['customer_name'],
                'quantity' => $newQuantity,
                'total_price' => $order->unit_price * $newQuantity,
            ]);
        }

        $order->updatePaymentStatus();

        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        if ($order->order_status !== 'completed') {
            $order->shoeProduct->increaseStock($order->quantity);
        }
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}