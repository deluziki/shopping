<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    DollarSign,
    Package,
    ShoppingCart,
    Users,
} from 'lucide-vue-next';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Order {
    id: number;
    order_number: string;
    status: string;
    total: number;
    user: User;
    created_at: string;
}

interface Category {
    id: number;
    name: string;
}

interface Product {
    id: number;
    name: string;
    stock: number;
    price: number;
    category: Category;
}

interface Stats {
    totalProducts: number;
    totalOrders: number;
    totalCustomers: number;
    totalRevenue: number;
    pendingOrders: number;
    lowStockProducts: number;
    outOfStockProducts: number;
}

const props = defineProps<{
    stats: Stats;
    recentOrders: Order[];
    lowStockProducts: Product[];
    ordersByStatus: Record<string, number>;
}>();

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
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
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-bold">Dashboard</h1>
                <p class="text-muted-foreground">Overview of your store performance.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Total Revenue</CardTitle>
                        <DollarSign class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatPrice(stats.totalRevenue) }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Total Orders</CardTitle>
                        <ShoppingCart class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalOrders }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.pendingOrders }} pending
                        </p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Products</CardTitle>
                        <Package class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalProducts }}</div>
                        <p v-if="stats.outOfStockProducts > 0" class="text-xs text-destructive">
                            {{ stats.outOfStockProducts }} out of stock
                        </p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Customers</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalCustomers }}</div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Recent Orders -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle>Recent Orders</CardTitle>
                        <Link href="/admin/orders">
                            <Button variant="outline" size="sm">View All</Button>
                        </Link>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Order</TableHead>
                                    <TableHead>Customer</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="text-right">Total</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="order in recentOrders" :key="order.id">
                                    <TableCell>
                                        <Link
                                            :href="`/admin/orders/${order.id}`"
                                            class="font-mono text-sm hover:underline"
                                        >
                                            {{ order.order_number }}
                                        </Link>
                                    </TableCell>
                                    <TableCell>{{ order.user.name }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="getStatusVariant(order.status)" class="capitalize">
                                            {{ order.status }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">{{ formatPrice(order.total) }}</TableCell>
                                </TableRow>
                                <TableRow v-if="recentOrders.length === 0">
                                    <TableCell colspan="4" class="text-center text-muted-foreground">
                                        No orders yet
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Low Stock Products -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle class="flex items-center gap-2">
                            <AlertTriangle v-if="lowStockProducts.length > 0" class="h-5 w-5 text-amber-500" />
                            Low Stock Products
                        </CardTitle>
                        <Link href="/admin/products?stock=low">
                            <Button variant="outline" size="sm">View All</Button>
                        </Link>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Product</TableHead>
                                    <TableHead>Category</TableHead>
                                    <TableHead class="text-right">Stock</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="product in lowStockProducts" :key="product.id">
                                    <TableCell>
                                        <Link
                                            :href="`/admin/products/${product.id}/edit`"
                                            class="font-medium hover:underline"
                                        >
                                            {{ product.name }}
                                        </Link>
                                    </TableCell>
                                    <TableCell>{{ product.category.name }}</TableCell>
                                    <TableCell class="text-right">
                                        <Badge :variant="product.stock === 0 ? 'destructive' : 'secondary'">
                                            {{ product.stock }}
                                        </Badge>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="lowStockProducts.length === 0">
                                    <TableCell colspan="3" class="text-center text-muted-foreground">
                                        All products are well stocked
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template>
