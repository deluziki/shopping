<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';
import { computed } from 'vue';
import StoreLayout from '@/layouts/store/StoreLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Category {
    id: number;
    name: string;
    slug: string;
    description: string;
    image: string;
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

defineProps<{
    featuredProducts: Product[];
    categories: Category[];
}>();

const page = usePage();
const storeName = computed(() => (page.props.name as string) || 'Store');

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};
</script>

<template>
    <Head title="Home" />
    <StoreLayout>
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-b from-muted/50 to-background">
            <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">
                        {{ storeName }}
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-muted-foreground">
                        Discover our collection of premium suits and shirts, crafted with precision and designed for the modern gentleman.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-4">
                        <Link href="/products">
                            <Button size="lg">
                                Shop Collection
                                <ArrowRight class="ml-2 h-4 w-4" />
                            </Button>
                        </Link>
                        <Link href="/products/category/suits">
                            <Button variant="outline" size="lg">View Suits</Button>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold tracking-tight">Shop by Category</h2>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="`/products/category/${category.slug}`"
                    class="group relative overflow-hidden rounded-lg border bg-card transition-all hover:shadow-lg"
                >
                    <div class="aspect-[4/3] bg-muted">
                        <img
                            v-if="category.image"
                            :src="category.image"
                            :alt="category.name"
                            class="h-full w-full object-cover transition-transform group-hover:scale-105"
                        />
                        <div v-else class="flex h-full items-center justify-center text-4xl font-bold text-muted-foreground/30">
                            {{ category.name[0] }}
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold">{{ category.name }}</h3>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ category.products_count }} products
                        </p>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold tracking-tight">Featured Products</h2>
                <Link href="/products" class="text-sm font-medium text-primary hover:underline">
                    View all <ArrowRight class="ml-1 inline h-4 w-4" />
                </Link>
            </div>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <Card
                    v-for="product in featuredProducts"
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
                            <p class="mt-1 text-lg font-bold">{{ formatPrice(product.price) }}</p>
                            <p v-if="product.stock <= 5" class="mt-1 text-xs text-destructive">
                                Only {{ product.stock }} left
                            </p>
                        </CardContent>
                    </Link>
                </Card>
            </div>
        </section>

        <!-- Value Props -->
        <section class="border-t bg-muted/30">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid gap-8 sm:grid-cols-3">
                    <div class="text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                            <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="mt-4 font-semibold">Premium Quality</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Crafted from the finest materials for lasting elegance.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                            <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="mt-4 font-semibold">Free Shipping</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Free shipping on orders over $500.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                            <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <h3 class="mt-4 font-semibold">Easy Returns</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            30-day hassle-free return policy.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </StoreLayout>
</template>
