<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Package, Plus, Search, Trash2 } from 'lucide-vue-next';
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
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Category {
    id: number;
    name: string;
}

interface Product {
    id: number;
    name: string;
    slug: string;
    price: number;
    stock: number;
    active: boolean;
    featured: boolean;
    category: Category;
}

interface PaginatedProducts {
    data: Product[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    current_page: number;
    last_page: number;
}

interface Filters {
    search?: string;
    category?: string;
    stock?: string;
}

const props = defineProps<{
    products: PaginatedProducts;
    categories: Category[];
    filters: Filters;
}>();

const search = ref(props.filters.search || '');
const categoryFilter = ref(props.filters.category || 'all');
const stockFilter = ref(props.filters.stock || 'all');
const deleteDialogOpen = ref(false);
const productToDelete = ref<Product | null>(null);

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};

const applyFilters = useDebounceFn(() => {
    router.get(
        '/admin/products',
        {
            search: search.value || undefined,
            category: categoryFilter.value !== 'all' ? categoryFilter.value : undefined,
            stock: stockFilter.value !== 'all' ? stockFilter.value : undefined,
        },
        { preserveState: true, replace: true },
    );
}, 300);

watch([search, categoryFilter, stockFilter], () => {
    applyFilters();
});

const goToPage = (url: string | null) => {
    if (url) {
        router.get(url);
    }
};

const confirmDelete = (product: Product) => {
    productToDelete.value = product;
    deleteDialogOpen.value = true;
};

const deleteProduct = () => {
    if (productToDelete.value) {
        router.delete(`/admin/products/${productToDelete.value.id}`, {
            onFinish: () => {
                deleteDialogOpen.value = false;
                productToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Products - Admin" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Products</h1>
                    <p class="text-muted-foreground">Manage your product inventory.</p>
                </div>
                <Link href="/admin/products/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Add Product
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <CardTitle>All Products</CardTitle>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    v-model="search"
                                    placeholder="Search products..."
                                    class="pl-9 w-full sm:w-64"
                                />
                            </div>
                            <Select v-model="categoryFilter">
                                <SelectTrigger class="w-full sm:w-40">
                                    <SelectValue placeholder="Category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Categories</SelectItem>
                                    <SelectItem
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="String(category.id)"
                                    >
                                        {{ category.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <Select v-model="stockFilter">
                                <SelectTrigger class="w-full sm:w-40">
                                    <SelectValue placeholder="Stock" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Stock</SelectItem>
                                    <SelectItem value="low">Low Stock</SelectItem>
                                    <SelectItem value="out">Out of Stock</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Product</TableHead>
                                <TableHead>Category</TableHead>
                                <TableHead>Price</TableHead>
                                <TableHead>Stock</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="product in products.data" :key="product.id">
                                <TableCell>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-muted">
                                            <Package class="h-5 w-5 text-muted-foreground" />
                                        </div>
                                        <div>
                                            <p class="font-medium">{{ product.name }}</p>
                                            <p v-if="product.featured" class="text-xs text-primary">Featured</p>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>{{ product.category.name }}</TableCell>
                                <TableCell>{{ formatPrice(product.price) }}</TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="product.stock === 0 ? 'destructive' : product.stock <= 10 ? 'secondary' : 'outline'"
                                    >
                                        {{ product.stock }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="product.active ? 'default' : 'secondary'">
                                        {{ product.active ? 'Active' : 'Inactive' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="`/admin/products/${product.id}/edit`">
                                            <Button variant="ghost" size="icon">
                                                <Edit class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="text-destructive hover:text-destructive"
                                            @click="confirmDelete(product)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="products.data.length === 0">
                                <TableCell colspan="6" class="text-center text-muted-foreground">
                                    No products found
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Pagination -->
                    <div v-if="products.last_page > 1" class="mt-4 flex justify-center gap-2">
                        <Button
                            v-for="link in products.links"
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

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Product</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete "{{ productToDelete?.name }}"? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteDialogOpen = false">Cancel</Button>
                    <Button variant="destructive" @click="deleteProduct">Delete</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AdminLayout>
</template>
