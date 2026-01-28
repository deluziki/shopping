<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Package } from 'lucide-vue-next';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Product {
    id: number;
    name: string;
    image: string | null;
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
    user: User;
    items: OrderItem[];
    created_at: string;
}

const props = defineProps<{
    order: Order;
}>();

const selectedStatus = ref(props.order.status);
const isUpdating = ref(false);

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

const updateStatus = () => {
    if (selectedStatus.value === props.order.status) return;

    isUpdating.value = true;
    router.patch(
        `/admin/orders/${props.order.id}/status`,
        { status: selectedStatus.value },
        {
            preserveScroll: true,
            onFinish: () => {
                isUpdating.value = false;
            },
        },
    );
};
</script>

<template>
    <Head :title="`Order ${order.order_number} - Admin`" />
    <AdminLayout>
        <div class="space-y-6">
            <div>
                <Link href="/admin/orders" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground mb-4">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Orders
                </Link>
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Order {{ order.order_number }}</h1>
                        <p class="text-muted-foreground">{{ formatDate(order.created_at) }}</p>
                    </div>
                    <Badge :variant="getStatusVariant(order.status)" class="h-8 px-4 text-sm capitalize">
                        {{ order.status }}
                    </Badge>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Items -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Order Items</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Product</TableHead>
                                        <TableHead>Details</TableHead>
                                        <TableHead class="text-right">Price</TableHead>
                                        <TableHead class="text-right">Qty</TableHead>
                                        <TableHead class="text-right">Total</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in order.items" :key="item.id">
                                        <TableCell>
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-md bg-muted">
                                                    <Package class="h-6 w-6 text-muted-foreground" />
                                                </div>
                                                <span class="font-medium">{{ item.product_name }}</span>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <span v-if="item.size || item.color" class="text-sm text-muted-foreground">
                                                <span v-if="item.size">Size: {{ item.size }}</span>
                                                <span v-if="item.size && item.color"> | </span>
                                                <span v-if="item.color">Color: {{ item.color }}</span>
                                            </span>
                                            <span v-else class="text-sm text-muted-foreground">-</span>
                                        </TableCell>
                                        <TableCell class="text-right">{{ formatPrice(item.price) }}</TableCell>
                                        <TableCell class="text-right">{{ item.quantity }}</TableCell>
                                        <TableCell class="text-right font-medium">
                                            {{ formatPrice(item.price * item.quantity) }}
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>

                    <!-- Customer & Shipping Info -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Customer</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p class="font-medium">{{ order.user.name }}</p>
                                <p class="text-sm text-muted-foreground">{{ order.user.email }}</p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Shipping Address</CardTitle>
                            </CardHeader>
                            <CardContent class="text-sm">
                                <p class="font-medium">{{ order.shipping_name }}</p>
                                <p class="text-muted-foreground">{{ order.shipping_address }}</p>
                                <p class="text-muted-foreground">
                                    {{ order.shipping_city }}, {{ order.shipping_state }} {{ order.shipping_zip }}
                                </p>
                                <p class="text-muted-foreground">{{ order.shipping_country }}</p>
                                <p v-if="order.shipping_phone" class="mt-2 text-muted-foreground">
                                    Phone: {{ order.shipping_phone }}
                                </p>
                            </CardContent>
                        </Card>
                    </div>

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

                <!-- Order Summary & Actions -->
                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Order Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
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

                    <Card>
                        <CardHeader>
                            <CardTitle>Update Status</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <Select v-model="selectedStatus">
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="pending">Pending</SelectItem>
                                    <SelectItem value="processing">Processing</SelectItem>
                                    <SelectItem value="shipped">Shipped</SelectItem>
                                    <SelectItem value="delivered">Delivered</SelectItem>
                                    <SelectItem value="cancelled">Cancelled</SelectItem>
                                </SelectContent>
                            </Select>
                            <Button
                                class="w-full"
                                :disabled="selectedStatus === order.status || isUpdating"
                                @click="updateStatus"
                            >
                                {{ isUpdating ? 'Updating...' : 'Update Status' }}
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
