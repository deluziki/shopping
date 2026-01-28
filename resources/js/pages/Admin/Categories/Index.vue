<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Folder, Plus, Trash2 } from 'lucide-vue-next';
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
    slug: string;
    description: string | null;
    products_count: number;
}

const props = defineProps<{
    categories: Category[];
}>();

const deleteDialogOpen = ref(false);
const categoryToDelete = ref<Category | null>(null);

const confirmDelete = (category: Category) => {
    categoryToDelete.value = category;
    deleteDialogOpen.value = true;
};

const deleteCategory = () => {
    if (categoryToDelete.value) {
        router.delete(`/admin/categories/${categoryToDelete.value.id}`, {
            onFinish: () => {
                deleteDialogOpen.value = false;
                categoryToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Categories - Admin" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Categories</h1>
                    <p class="text-muted-foreground">Manage product categories.</p>
                </div>
                <Link href="/admin/categories/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Add Category
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>All Categories</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>Slug</TableHead>
                                <TableHead>Description</TableHead>
                                <TableHead>Products</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="category in categories" :key="category.id">
                                <TableCell>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-muted">
                                            <Folder class="h-5 w-5 text-muted-foreground" />
                                        </div>
                                        <span class="font-medium">{{ category.name }}</span>
                                    </div>
                                </TableCell>
                                <TableCell class="font-mono text-sm">{{ category.slug }}</TableCell>
                                <TableCell class="max-w-xs truncate">
                                    {{ category.description || '-' }}
                                </TableCell>
                                <TableCell>
                                    <Badge variant="secondary">{{ category.products_count }}</Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="`/admin/categories/${category.id}/edit`">
                                            <Button variant="ghost" size="icon">
                                                <Edit class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="text-destructive hover:text-destructive"
                                            :disabled="category.products_count > 0"
                                            @click="confirmDelete(category)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="categories.length === 0">
                                <TableCell colspan="5" class="text-center text-muted-foreground">
                                    No categories found
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Category</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete "{{ categoryToDelete?.name }}"? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteDialogOpen = false">Cancel</Button>
                    <Button variant="destructive" @click="deleteCategory">Delete</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AdminLayout>
</template>
