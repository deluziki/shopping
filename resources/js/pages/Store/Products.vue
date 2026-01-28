<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Filter } from 'lucide-vue-next';
import StoreLayout from '@/layouts/store/StoreLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Category {
    id: number;
    name: string;
    slug: string;
    products_count: number;
}

interface Product {
    id: number;
    name: string;
    slug: string;
    description: string;
    price: number;
    image: string;
    category: Category;
    stock: number;
}

interface PaginatedProducts {
    data: Product[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    current_page: number;
    last_page: number;
}

const props = defineProps<{
    products: PaginatedProducts;
    categories: Category[];
    currentCategory: Category | null;
}>();

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};

const goToPage = (url: string | null) => {
    if (url) {
        router.get(url);
    }
};
</script>

<template>
    <Head :title="currentCategory ? currentCategory.name : 'All Products'" />
    <StoreLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ currentCategory ? currentCategory.name : 'All Products' }}
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        {{ products.data.length }} products found
                    </p>
                </div>
            </div>

            <div class="mt-8 grid gap-8 lg:grid-cols-4">
                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <div class="sticky top-24">
                        <h2 class="flex items-center gap-2 text-lg font-semibold">
                            <Filter class="h-5 w-5" />
                            Categories
                        </h2>
                        <nav class="mt-4 space-y-1">
                            <Link
                                href="/products"
                                :class="[
                                    'block rounded-md px-3 py-2 text-sm transition-colors',
                                    !currentCategory
                                        ? 'bg-primary text-primary-foreground'
                                        : 'text-muted-foreground hover:bg-accent hover:text-foreground',
                                ]"
                            >
                                All Products
                            </Link>
                            <Link
                                v-for="category in categories"
                                :key="category.id"
                                :href="`/products/category/${category.slug}`"
                                :class="[
                                    'block rounded-md px-3 py-2 text-sm transition-colors',
                                    currentCategory?.id === category.id
                                        ? 'bg-primary text-primary-foreground'
                                        : 'text-muted-foreground hover:bg-accent hover:text-foreground',
                                ]"
                            >
                                {{ category.name }}
                                <span class="ml-2 text-xs opacity-70">({{ category.products_count }})</span>
                            </Link>
                        </nav>
                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="lg:col-span-3">
                    <div v-if="products.data.length === 0" class="py-12 text-center">
                        <p class="text-lg text-muted-foreground">No products found.</p>
                        <Link href="/products">
                            <Button variant="outline" class="mt-4">View All Products</Button>
                        </Link>
                    </div>

                    <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <Card
                            v-for="product in products.data"
                            :key="product.id"
                            class="group overflow-hidden transition-all hover:shadow-lg"
                        >
                            <Link :href="`/product/${product.slug}`">
                                <div class="aspect-[3/4] overflow-hidden bg-muted">
                                    <img
                                        v-if="product.image"
                                        :src="product.image"
                                        :alt="product.name"
                                        class="h-full w-full object-cover transition-transform group-hover:scale-105"
                                    />
                                    <div v-else class="flex h-full items-center justify-center text-6xl font-bold text-muted-foreground/20">
                                        {{ product.name[0] }}
                                    </div>
                                </div>
                                <CardContent class="p-4">
                                    <Badge variant="secondary" class="mb-2">{{ product.category.name }}</Badge>
                                    <h3 class="font-semibold line-clamp-1">{{ product.name }}</h3>
                                    <p class="mt-1 text-sm text-muted-foreground line-clamp-2">
                                        {{ product.description }}
                                    </p>
                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="text-lg font-bold">{{ formatPrice(product.price) }}</span>
                                        <span v-if="product.stock <= 5" class="text-xs text-destructive">
                                            {{ product.stock }} left
                                        </span>
                                    </div>
                                </CardContent>
                            </Link>
                        </Card>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.last_page > 1" class="mt-8 flex justify-center gap-2">
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
                </div>
            </div>
        </div>
    </StoreLayout>
</template>
