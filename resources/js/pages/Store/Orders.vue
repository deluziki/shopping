<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Package } from 'lucide-vue-next';
import StoreLayout from '@/layouts/store/StoreLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';

interface OrderItem {
    id: number;
    quantity: number;
}

interface Order {
    id: number;
    order_number: string;
    status: string;
    total: number;
    items: OrderItem[];
    created_at: string;
}

interface PaginatedOrders {
    data: Order[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    current_page: number;
    last_page: number;
}

const props = defineProps<{
    orders: PaginatedOrders;
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
        month: 'short',
        day: 'numeric',
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

const getTotalItems = (items: OrderItem[]) => {
    return items.reduce((sum, item) => sum + item.quantity, 0);
};

const goToPage = (url: string | null) => {
    if (url) {
        router.get(url);
    }
};
</script>

<template>
    <Head title="My Orders" />
    <StoreLayout>
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold">My Orders</h1>

            <div v-if="orders.data.length === 0" class="mt-12 text-center">
                <Package class="mx-auto h-16 w-16 text-muted-foreground/50" />
                <h2 class="mt-4 text-xl font-semibold">No orders yet</h2>
                <p class="mt-2 text-muted-foreground">Start shopping to see your orders here.</p>
                <Link href="/products">
                    <Button class="mt-6">Browse Products</Button>
                </Link>
            </div>

            <div v-else class="mt-8 space-y-4">
                <Card v-for="order in orders.data" :key="order.id">
                    <CardContent class="p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="space-y-1">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-semibold">{{ order.order_number }}</span>
                                    <Badge :variant="getStatusVariant(order.status)" class="capitalize">
                                        {{ order.status }}
                                    </Badge>
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    {{ formatDate(order.created_at) }} · {{ getTotalItems(order.items) }} items
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-lg font-semibold">{{ formatPrice(order.total) }}</span>
                                <Link :href="`/orders/${order.id}`">
                                    <Button variant="outline" size="sm">View Details</Button>
                                </Link>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Pagination -->
                <div v-if="orders.last_page > 1" class="mt-8 flex justify-center gap-2">
                    <Button
                        v-for="link in orders.links"
                        :key="link.label"
                        :variant="link.active ? 'default' : 'outline'"
                        :disabled="!link.url"
                        size="sm"
                        @click="goToPage(link.url)"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </StoreLayout>
</template>
