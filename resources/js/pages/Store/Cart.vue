<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, Minus, Plus, ShoppingBag, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import StoreLayout from '@/layouts/store/StoreLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';

interface Product {
    id: number;
    name: string;
    slug: string;
    price: number;
    image: string;
    stock: number;
}

interface CartItem {
    id: number;
    product: Product;
    quantity: number;
    size: string | null;
    color: string | null;
}

const props = defineProps<{
    cartItems: CartItem[];
    subtotal: number;
}>();

const updatingItem = ref<number | null>(null);

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};

const updateQuantity = (item: CartItem, quantity: number) => {
    if (quantity < 1 || quantity > item.product.stock) return;

    updatingItem.value = item.id;
    router.patch(
        `/cart/${item.id}`,
        { quantity },
        {
            preserveScroll: true,
            onFinish: () => {
                updatingItem.value = null;
            },
        },
    );
};

const removeItem = (item: CartItem) => {
    updatingItem.value = item.id;
    router.delete(`/cart/${item.id}`, {
        preserveScroll: true,
        onFinish: () => {
            updatingItem.value = null;
        },
    });
};
</script>

<template>
    <Head title="Shopping Cart" />
    <StoreLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold">Shopping Cart</h1>

            <div v-if="cartItems.length === 0" class="mt-12 text-center">
                <ShoppingBag class="mx-auto h-16 w-16 text-muted-foreground/50" />
                <h2 class="mt-4 text-xl font-semibold">Your cart is empty</h2>
                <p class="mt-2 text-muted-foreground">Start shopping to add items to your cart.</p>
                <Link href="/products">
                    <Button class="mt-6">
                        Browse Products
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Button>
                </Link>
            </div>

            <div v-else class="mt-8 grid gap-8 lg:grid-cols-3">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="space-y-4">
                        <Card v-for="item in cartItems" :key="item.id" class="overflow-hidden">
                            <div class="flex gap-4 p-4">
                                <!-- Product Image -->
                                <Link :href="`/product/${item.product.slug}`" class="shrink-0">
                                    <div class="h-24 w-24 overflow-hidden rounded-md bg-muted">
                                        <img
                                            v-if="item.product.image"
                                            :src="item.product.image"
                                            :alt="item.product.name"
                                            class="h-full w-full object-cover"
                                        />
                                        <div v-else class="flex h-full items-center justify-center text-2xl font-bold text-muted-foreground/30">
                                            {{ item.product.name[0] }}
                                        </div>
                                    </div>
                                </Link>

                                <!-- Product Info -->
                                <div class="flex flex-1 flex-col">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <Link :href="`/product/${item.product.slug}`" class="font-semibold hover:underline">
                                                {{ item.product.name }}
                                            </Link>
                                            <div class="mt-1 text-sm text-muted-foreground">
                                                <span v-if="item.size">Size: {{ item.size }}</span>
                                                <span v-if="item.size && item.color"> | </span>
                                                <span v-if="item.color">Color: {{ item.color }}</span>
                                            </div>
                                        </div>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-muted-foreground hover:text-destructive"
                                            @click="removeItem(item)"
                                            :disabled="updatingItem === item.id"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>

                                    <div class="mt-auto flex items-center justify-between pt-2">
                                        <div class="flex items-center gap-2">
                                            <Button
                                                variant="outline"
                                                size="icon"
                                                class="h-8 w-8"
                                                @click="updateQuantity(item, item.quantity - 1)"
                                                :disabled="item.quantity <= 1 || updatingItem === item.id"
                                            >
                                                <Minus class="h-3 w-3" />
                                            </Button>
                                            <span class="w-8 text-center">{{ item.quantity }}</span>
                                            <Button
                                                variant="outline"
                                                size="icon"
                                                class="h-8 w-8"
                                                @click="updateQuantity(item, item.quantity + 1)"
                                                :disabled="item.quantity >= item.product.stock || updatingItem === item.id"
                                            >
                                                <Plus class="h-3 w-3" />
                                            </Button>
                                        </div>
                                        <span class="font-semibold">
                                            {{ formatPrice(item.product.price * item.quantity) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <Card class="sticky top-24">
                        <CardHeader>
                            <CardTitle>Order Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-muted-foreground">Subtotal</span>
                                <span>{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-muted-foreground">Shipping</span>
                                <span>{{ subtotal >= 500 ? 'Free' : formatPrice(25) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-muted-foreground">Tax (8%)</span>
                                <span>{{ formatPrice(subtotal * 0.08) }}</span>
                            </div>
                            <div class="border-t pt-4">
                                <div class="flex justify-between font-semibold">
                                    <span>Total</span>
                                    <span>{{ formatPrice(subtotal + (subtotal >= 500 ? 0 : 25) + subtotal * 0.08) }}</span>
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter>
                            <Link href="/checkout" class="w-full">
                                <Button class="w-full" size="lg">
                                    Proceed to Checkout
                                    <ArrowRight class="ml-2 h-4 w-4" />
                                </Button>
                            </Link>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>
    </StoreLayout>
</template>
