<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, Package } from 'lucide-vue-next';
import StoreLayout from '@/layouts/store/StoreLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface OrderItem {
    id: number;
    product_name: string;
    price: number;
    quantity: number;
    size: string | null;
    color: string | null;
}

interface Order {
    id: number;
    order_number: string;
    status: string;
    subtotal: number;
    tax: number;
    shipping: number;
    total: number;
    shipping_name: string;
    shipping_address: string;
    shipping_city: string;
    shipping_state: string;
    shipping_zip: string;
    shipping_country: string;
    items: OrderItem[];
    created_at: string;
}

const props = defineProps<{
    order: Order;
}>();

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Order Confirmation" />
    <StoreLayout>
        <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <CheckCircle class="mx-auto h-16 w-16 text-green-500" />
                <h1 class="mt-4 text-3xl font-bold">Order Confirmed!</h1>
                <p class="mt-2 text-lg text-muted-foreground">
                    Thank you for your order. We'll send you an email with tracking information.
                </p>
            </div>

            <Card class="mt-8">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Order Details</CardTitle>
                        <span class="text-sm text-muted-foreground">{{ formatDate(order.created_at) }}</span>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="flex items-center justify-between rounded-lg bg-muted/50 p-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Order Number</p>
                            <p class="font-mono font-semibold">{{ order.order_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-muted-foreground">Status</p>
                            <p class="font-semibold capitalize">{{ order.status }}</p>
                        </div>
                    </div>

                    <!-- Items -->
                    <div>
                        <h3 class="font-semibold">Items</h3>
                        <div class="mt-3 space-y-3">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="flex items-center justify-between border-b pb-3 last:border-0"
                            >
                                <div>
                                    <p class="font-medium">{{ item.product_name }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        Qty: {{ item.quantity }}
                                        <span v-if="item.size"> | Size: {{ item.size }}</span>
                                        <span v-if="item.color"> | Color: {{ item.color }}</span>
                                    </p>
                                </div>
                                <span>{{ formatPrice(item.price * item.quantity) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="space-y-2 border-t pt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Subtotal</span>
                            <span>{{ formatPrice(order.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Shipping</span>
                            <span>{{ order.shipping === 0 ? 'Free' : formatPrice(order.shipping) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Tax</span>
                            <span>{{ formatPrice(order.tax) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 text-lg font-semibold">
                            <span>Total</span>
                            <span>{{ formatPrice(order.total) }}</span>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="border-t pt-4">
                        <h3 class="font-semibold">Shipping Address</h3>
                        <div class="mt-2 text-sm text-muted-foreground">
                            <p>{{ order.shipping_name }}</p>
                            <p>{{ order.shipping_address }}</p>
                            <p>{{ order.shipping_city }}, {{ order.shipping_state }} {{ order.shipping_zip }}</p>
                            <p>{{ order.shipping_country }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="mt-8 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                <Link href="/orders">
                    <Button variant="outline">
                        <Package class="mr-2 h-4 w-4" />
                        View All Orders
                    </Button>
                </Link>
                <Link href="/products">
                    <Button>Continue Shopping</Button>
                </Link>
            </div>
        </div>
    </StoreLayout>
</template>
