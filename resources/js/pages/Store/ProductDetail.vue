<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Minus, Plus, ShoppingBag } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import StoreLayout from '@/layouts/store/StoreLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface Category {
    id: number;
    name: string;
    slug: string;
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
    sizes: string[] | null;
    colors: string[] | null;
    material: string | null;
}

const props = defineProps<{
    product: Product;
    relatedProducts: Product[];
}>();

const page = usePage();
const user = computed(() => page.props.auth?.user);

const quantity = ref(1);
const selectedSize = ref<string>('');
const selectedColor = ref<string>('');
const isAdding = ref(false);

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};

const incrementQuantity = () => {
    if (quantity.value < props.product.stock) {
        quantity.value++;
    }
};

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

const addToCart = () => {
    if (!user.value) {
        router.get('/login');
        return;
    }

    isAdding.value = true;
    router.post(
        '/cart/add',
        {
            product_id: props.product.id,
            quantity: quantity.value,
            size: selectedSize.value || null,
            color: selectedColor.value || null,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                isAdding.value = false;
            },
        },
    );
};
</script>

<template>
    <Head :title="product.name" />
    <StoreLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <Link href="/products" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Products
                </Link>
            </nav>

            <!-- Product Details -->
            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Product Image -->
                <div class="aspect-[3/4] overflow-hidden rounded-lg bg-muted">
                    <img
                        v-if="product.image"
                        :src="product.image"
                        :alt="product.name"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="flex h-full items-center justify-center text-9xl font-bold text-muted-foreground/20">
                        {{ product.name[0] }}
                    </div>
                </div>

                <!-- Product Info -->
                <div class="flex flex-col">
                    <Badge variant="secondary" class="w-fit">{{ product.category.name }}</Badge>
                    <h1 class="mt-4 text-3xl font-bold">{{ product.name }}</h1>
                    <p class="mt-4 text-3xl font-bold">{{ formatPrice(product.price) }}</p>

                    <div class="mt-6 space-y-4 border-t pt-6">
                        <p class="text-muted-foreground">{{ product.description }}</p>

                        <div v-if="product.material" class="flex items-center gap-2">
                            <span class="text-sm font-medium">Material:</span>
                            <span class="text-sm text-muted-foreground">{{ product.material }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium">Availability:</span>
                            <span :class="product.stock > 0 ? 'text-green-600' : 'text-destructive'" class="text-sm">
                                {{ product.stock > 0 ? `${product.stock} in stock` : 'Out of stock' }}
                            </span>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="mt-6 space-y-4 border-t pt-6">
                        <!-- Size -->
                        <div v-if="product.sizes && product.sizes.length > 0">
                            <Label>Size</Label>
                            <Select v-model="selectedSize">
                                <SelectTrigger class="mt-2 w-full">
                                    <SelectValue placeholder="Select size" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="size in product.sizes" :key="size" :value="size">
                                        {{ size }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Color -->
                        <div v-if="product.colors && product.colors.length > 0">
                            <Label>Color</Label>
                            <Select v-model="selectedColor">
                                <SelectTrigger class="mt-2 w-full">
                                    <SelectValue placeholder="Select color" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="color in product.colors" :key="color" :value="color">
                                        {{ color }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <Label>Quantity</Label>
                            <div class="mt-2 flex items-center gap-3">
                                <Button variant="outline" size="icon" @click="decrementQuantity" :disabled="quantity <= 1">
                                    <Minus class="h-4 w-4" />
                                </Button>
                                <span class="w-12 text-center text-lg font-medium">{{ quantity }}</span>
                                <Button variant="outline" size="icon" @click="incrementQuantity" :disabled="quantity >= product.stock">
                                    <Plus class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Add to Cart -->
                    <div class="mt-8">
                        <Button
                            size="lg"
                            class="w-full"
                            :disabled="product.stock === 0 || isAdding"
                            @click="addToCart"
                        >
                            <ShoppingBag class="mr-2 h-5 w-5" />
                            {{ isAdding ? 'Adding...' : 'Add to Cart' }}
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <section v-if="relatedProducts.length > 0" class="mt-16">
                <h2 class="text-2xl font-bold">Related Products</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <Card
                        v-for="relatedProduct in relatedProducts"
                        :key="relatedProduct.id"
                        class="group overflow-hidden transition-all hover:shadow-lg"
                    >
                        <Link :href="`/product/${relatedProduct.slug}`">
                            <div class="aspect-[3/4] overflow-hidden bg-muted">
                                <img
                                    v-if="relatedProduct.image"
                                    :src="relatedProduct.image"
                                    :alt="relatedProduct.name"
                                    class="h-full w-full object-cover transition-transform group-hover:scale-105"
                                />
                                <div v-else class="flex h-full items-center justify-center text-6xl font-bold text-muted-foreground/20">
                                    {{ relatedProduct.name[0] }}
                                </div>
                            </div>
                            <CardContent class="p-4">
                                <h3 class="font-semibold line-clamp-1">{{ relatedProduct.name }}</h3>
                                <p class="mt-1 text-lg font-bold">{{ formatPrice(relatedProduct.price) }}</p>
                            </CardContent>
                        </Link>
                    </Card>
                </div>
            </section>
        </div>
    </StoreLayout>
</template>
