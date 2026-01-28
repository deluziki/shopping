<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, X } from 'lucide-vue-next';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';

interface Category {
    id: number;
    name: string;
}

interface Product {
    id: number;
    name: string;
    category_id: number;
    description: string;
    price: number;
    image: string | null;
    sizes: string[] | null;
    colors: string[] | null;
    material: string | null;
    stock: number;
    featured: boolean;
    active: boolean;
}

const props = defineProps<{
    product: Product;
    categories: Category[];
}>();

const form = useForm({
    name: props.product.name,
    category_id: String(props.product.category_id),
    description: props.product.description,
    price: String(props.product.price),
    image: props.product.image || '',
    sizes: props.product.sizes || [],
    colors: props.product.colors || [],
    material: props.product.material || '',
    stock: String(props.product.stock),
    featured: props.product.featured,
    active: props.product.active,
});

const newSize = ref('');
const newColor = ref('');

const addSize = () => {
    if (newSize.value && !form.sizes.includes(newSize.value)) {
        form.sizes.push(newSize.value);
        newSize.value = '';
    }
};

const removeSize = (size: string) => {
    form.sizes = form.sizes.filter((s) => s !== size);
};

const addColor = () => {
    if (newColor.value && !form.colors.includes(newColor.value)) {
        form.colors.push(newColor.value);
        newColor.value = '';
    }
};

const removeColor = (color: string) => {
    form.colors = form.colors.filter((c) => c !== color);
};

const submit = () => {
    form.put(`/admin/products/${props.product.id}`);
};
</script>

<template>
    <Head :title="`Edit ${product.name} - Admin`" />
    <AdminLayout>
        <div class="mx-auto max-w-3xl space-y-6">
            <div>
                <Link href="/admin/products" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground mb-4">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Products
                </Link>
                <h1 class="text-3xl font-bold">Edit Product</h1>
                <p class="text-muted-foreground">Update product information.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Basic Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <Label for="name">Product Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                class="mt-1"
                                required
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-destructive">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <Label for="category">Category</Label>
                            <Select v-model="form.category_id" required>
                                <SelectTrigger class="mt-1">
                                    <SelectValue placeholder="Select a category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="String(category.id)"
                                    >
                                        {{ category.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.category_id" class="mt-1 text-sm text-destructive">
                                {{ form.errors.category_id }}
                            </p>
                        </div>

                        <div>
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1"
                                rows="4"
                                required
                            />
                            <p v-if="form.errors.description" class="mt-1 text-sm text-destructive">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label for="price">Price ($)</Label>
                                <Input
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1"
                                    required
                                />
                                <p v-if="form.errors.price" class="mt-1 text-sm text-destructive">
                                    {{ form.errors.price }}
                                </p>
                            </div>
                            <div>
                                <Label for="stock">Stock</Label>
                                <Input
                                    id="stock"
                                    v-model="form.stock"
                                    type="number"
                                    min="0"
                                    class="mt-1"
                                    required
                                />
                                <p v-if="form.errors.stock" class="mt-1 text-sm text-destructive">
                                    {{ form.errors.stock }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <Label for="image">Image URL</Label>
                            <Input
                                id="image"
                                v-model="form.image"
                                type="url"
                                class="mt-1"
                                placeholder="https://example.com/image.jpg"
                            />
                        </div>

                        <div>
                            <Label for="material">Material</Label>
                            <Input
                                id="material"
                                v-model="form.material"
                                class="mt-1"
                                placeholder="e.g., 100% Wool"
                            />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Variants</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <Label>Sizes</Label>
                            <div class="mt-2 flex gap-2">
                                <Input
                                    v-model="newSize"
                                    placeholder="e.g., S, M, L, XL"
                                    @keydown.enter.prevent="addSize"
                                />
                                <Button type="button" variant="outline" @click="addSize">
                                    <Plus class="h-4 w-4" />
                                </Button>
                            </div>
                            <div v-if="form.sizes.length > 0" class="mt-2 flex flex-wrap gap-2">
                                <Badge v-for="size in form.sizes" :key="size" variant="secondary">
                                    {{ size }}
                                    <button type="button" class="ml-1" @click="removeSize(size)">
                                        <X class="h-3 w-3" />
                                    </button>
                                </Badge>
                            </div>
                        </div>

                        <div>
                            <Label>Colors</Label>
                            <div class="mt-2 flex gap-2">
                                <Input
                                    v-model="newColor"
                                    placeholder="e.g., Navy, Black, Gray"
                                    @keydown.enter.prevent="addColor"
                                />
                                <Button type="button" variant="outline" @click="addColor">
                                    <Plus class="h-4 w-4" />
                                </Button>
                            </div>
                            <div v-if="form.colors.length > 0" class="mt-2 flex flex-wrap gap-2">
                                <Badge v-for="color in form.colors" :key="color" variant="secondary">
                                    {{ color }}
                                    <button type="button" class="ml-1" @click="removeColor(color)">
                                        <X class="h-3 w-3" />
                                    </button>
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Status</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center space-x-2">
                            <Checkbox id="active" v-model:checked="form.active" />
                            <Label for="active" class="cursor-pointer">Active (visible in store)</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox id="featured" v-model:checked="form.featured" />
                            <Label for="featured" class="cursor-pointer">Featured (show on homepage)</Label>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-4">
                    <Link href="/admin/products">
                        <Button type="button" variant="outline">Cancel</Button>
                    </Link>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
