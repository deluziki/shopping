<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
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

interface Order {
    id: number;
    order_number: string;
    status: string;
    total: number;
    user: User;
    created_at: string;
}

interface PaginatedOrders {
    data: Order[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    current_page: number;
    last_page: number;
}

interface Filters {
    status?: string;
    search?: string;
}

const props = defineProps<{
    orders: PaginatedOrders;
    filters: Filters;
}>();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');

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
        year: 'numeric',
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

const applyFilters = useDebounceFn(() => {
    router.get(
        '/admin/orders',
        {
            search: search.value || undefined,
            status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        },
        { preserveState: true, replace: true },
    );
}, 300);

watch([search, statusFilter], () => {
    applyFilters();
});

const goToPage = (url: string | null) => {
    if (url) {
        router.get(url);
    }
};
</script>

<template>
    <Head title="Orders - Admin" />
    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-bold">Orders</h1>
                <p class="text-muted-foreground">View and manage customer orders.</p>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <CardTitle>All Orders</CardTitle>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    v-model="search"
                                    placeholder="Search orders..."
                                    class="pl-9 w-full sm:w-64"
                                />
                            </div>
                            <Select v-model="statusFilter">
                                <SelectTrigger class="w-full sm:w-40">
                                    <SelectValue placeholder="Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Status</SelectItem>
                                    <SelectItem value="pending">Pending</SelectItem>
                                    <SelectItem value="processing">Processing</SelectItem>
                                    <SelectItem value="shipped">Shipped</SelectItem>
                                    <SelectItem value="delivered">Delivered</SelectItem>
                                    <SelectItem value="cancelled">Cancelled</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Order</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Date</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Total</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="order in orders.data" :key="order.id">
                                <TableCell class="font-mono">{{ order.order_number }}</TableCell>
                                <TableCell>
                                    <div>
                                        <p class="font-medium">{{ order.user.name }}</p>
                                        <p class="text-sm text-muted-foreground">{{ order.user.email }}</p>
                                    </div>
                                </TableCell>
                                <TableCell>{{ formatDate(order.created_at) }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusVariant(order.status)" class="capitalize">
                                        {{ order.status }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right font-medium">{{ formatPrice(order.total) }}</TableCell>
                                <TableCell class="text-right">
                                    <Link :href="`/admin/orders/${order.id}`">
                                        <Button variant="ghost" size="icon">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="orders.data.length === 0">
                                <TableCell colspan="6" class="text-center text-muted-foreground">
                                    No orders found
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Pagination -->
                    <div v-if="orders.last_page > 1" class="mt-4 flex justify-center gap-2">
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
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
