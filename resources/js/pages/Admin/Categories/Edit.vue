<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

interface Category {
    id: number;
    name: string;
    description: string | null;
    image: string | null;
}

const props = defineProps<{
    category: Category;
}>();

const form = useForm({
    name: props.category.name,
    description: props.category.description || '',
    image: props.category.image || '',
});

const submit = () => {
    form.put(`/admin/categories/${props.category.id}`);
};
</script>

<template>
    <Head :title="`Edit ${category.name} - Admin`" />
    <AdminLayout>
        <div class="mx-auto max-w-2xl space-y-6">
            <div>
                <Link href="/admin/categories" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground mb-4">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Categories
                </Link>
                <h1 class="text-3xl font-bold">Edit Category</h1>
                <p class="text-muted-foreground">Update category information.</p>
            </div>

            <form @submit.prevent="submit">
                <Card>
                    <CardHeader>
                        <CardTitle>Category Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <Label for="name">Name</Label>
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
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1"
                                rows="3"
                            />
                            <p v-if="form.errors.description" class="mt-1 text-sm text-destructive">
                                {{ form.errors.description }}
                            </p>
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

                        <div class="flex justify-end gap-4 pt-4">
                            <Link href="/admin/categories">
                                <Button type="button" variant="outline">Cancel</Button>
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </form>
        </div>
    </AdminLayout>
</template>
