<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function index(): Response
    {
        $cartItems = auth()->user()->cartItems()
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);
        $tax = $subtotal * 0.08;
        $shipping = $subtotal >= 500 ? 0 : 25;
        $total = $subtotal + $tax + $shipping;

        return Inertia::render('Store/Checkout', [
            'cartItems' => $cartItems,
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'shipping' => $shipping,
            'total' => round($total, 2),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_zip' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:255',
            'shipping_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->withErrors([
                    'stock' => "Not enough stock for {$item->product->name}. Available: {$item->product->stock}",
                ]);
            }
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);
        $tax = $subtotal * 0.08;
        $shipping = $subtotal >= 500 ? 0 : 25;
        $total = $subtotal + $tax + $shipping;

        DB::transaction(function () use ($request, $cartItems, $subtotal, $tax, $shipping, $total) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_zip' => $request->shipping_zip,
                'shipping_country' => $request->shipping_country,
                'shipping_phone' => $request->shipping_phone,
                'notes' => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'size' => $item->size,
                    'color' => $item->color,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            auth()->user()->cartItems()->delete();

            session(['last_order_id' => $order->id]);
        });

        return redirect()->route('orders.confirmation');
    }

    public function confirmation(): Response
    {
        $orderId = session('last_order_id');

        if (! $orderId) {
            return redirect()->route('store.index');
        }

        $order = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        return Inertia::render('Store/OrderConfirmation', [
            'order' => $order,
        ]);
    }
}
