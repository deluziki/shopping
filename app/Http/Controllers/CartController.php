<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(): Response
    {
        $cartItems = auth()->user()->cartItems()
            ->with('product.category')
            ->get();

        $subtotal = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);

        return Inertia::render('Store/Cart', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Not enough stock available.']);
        }

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->where('size', $request->size)
            ->where('color', $request->color)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($newQuantity > $product->stock) {
                return back()->withErrors(['quantity' => 'Not enough stock available.']);
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'size' => $request->size,
                'color' => $request->color,
            ]);
        }

        return back()->with('success', 'Item added to cart.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cartItem->product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Not enough stock available.']);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }
}
