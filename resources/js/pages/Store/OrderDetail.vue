<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import StoreLayout from '@/layouts/store/StoreLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Product {
    id: number;
    name: string;
    slug: string;
    image: string;
}

interface OrderItem {
    id: number;
    product: Product;
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
    shipping_phone: string | null;
    notes: string | null;
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
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'pending':
            return 'secondary';
        case 'processing':
            return 'default';
        case 'shipped':
            return 'default';
        case 'delivered':
            return 'default';
        case 'cancelled':
            return 'destructive';
        default:
            return 'secondary';
    }
};
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />
    <StoreLayout>
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <nav class="mb-8">
                <Link href="/orders" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Orders
                </Link>
            </nav>

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Order Details</h1>
                    <p class="mt-1 text-muted-foreground">{{ formatDate(order.created_at) }}</p>
                </div>
                <Badge :variant="getStatusVariant(order.status)" class="h-8 px-4 text-sm capitalize">
                    {{ order.status }}
                </Badge>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Items -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Items</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div
                                    v-for="item in order.items"
                                    :key="item.id"
                                    class="flex gap-4 border-b pb-4 last:border-0 last:pb-0"
                                >
                                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-md bg-muted">
                                        <img
                                            v-if="item.product?.image"
                                            :src="item.product.image"
                                            :alt="item.product_name"
                                            class="h-full w-full object-cover"
                                        />
                                        <div v-else class="flex h-full items-center justify-center text-xl font-bold text-muted-foreground/30">
                                            {{ item.product_name[0] }}
                                        </div>
                                    </div>
                                    <div class="flex flex-1 flex-col">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="font-semibold">{{ item.product_name }}</p>
                                                <p class="text-sm text-muted-foreground">
                                                    <span v-if="item.size">Size: {{ item.size }}</span>
                                                    <span v-if="item.size && item.color"> | </span>
                                                    <span v-if="item.color">Color: {{ item.color }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-auto flex items-center justify-between pt-2">
                                            <span class="text-sm text-muted-foreground">
                                                {{ formatPrice(item.price) }} x {{ item.quantity }}
                                            </span>
                                            <span class="font-semibold">{{ formatPrice(item.price * item.quantity) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Shipping Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Shipping Address</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="text-sm">
                                <p class="font-medium">{{ order.shipping_name }}</p>
                                <p class="text-muted-foreground">{{ order.shipping_address }}</p>
                                <p class="text-muted-foreground">
                                    {{ order.shipping_city }}, {{ order.shipping_state }} {{ order.shipping_zip }}
                                </p>
                                <p class="text-muted-foreground">{{ order.shipping_country }}</p>
                                <p v-if="order.shipping_phone" class="mt-2 text-muted-foreground">
                                    Phone: {{ order.shipping_phone }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Notes -->
                    <Card v-if="order.notes">
                        <CardHeader>
                            <CardTitle>Order Notes</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">{{ order.notes }}</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <Card class="sticky top-24">
                        <CardHeader>
                            <CardTitle>Order Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="rounded-lg bg-muted/50 p-3">
                                <p class="text-xs text-muted-foreground">Order Number</p>
                                <p class="font-mono font-semibold">{{ order.order_number }}</p>
                            </div>

                            <div class="space-y-2">
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
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between text-lg font-semibold">
                                    <span>Total</span>
                                    <span>{{ formatPrice(order.total) }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </StoreLayout>
</template>
