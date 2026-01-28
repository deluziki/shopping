<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import StoreLayout from '@/layouts/store/StoreLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

interface Product {
    id: number;
    name: string;
    price: number;
}

interface CartItem {
    id: number;
    product: Product;
    quantity: number;
    size: string | null;
    color: string | null;
}

defineProps<{
    cartItems: CartItem[];
    subtotal: number;
    tax: number;
    shipping: number;
    total: number;
}>();

const form = useForm({
    shipping_name: '',
    shipping_address: '',
    shipping_city: '',
    shipping_state: '',
    shipping_zip: '',
    shipping_country: 'USA',
    shipping_phone: '',
    notes: '',
});

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};

const submit = () => {
    form.post('/checkout');
};
</script>

<template>
    <Head title="Checkout" />
    <StoreLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <nav class="mb-8">
                <Link href="/cart" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Cart
                </Link>
            </nav>

            <h1 class="text-3xl font-bold">Checkout</h1>

            <form @submit.prevent="submit" class="mt-8 grid gap-8 lg:grid-cols-3">
                <!-- Shipping Information -->
                <div class="lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Shipping Information</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div>
                                <Label for="shipping_name">Full Name</Label>
                                <Input
                                    id="shipping_name"
                                    v-model="form.shipping_name"
                                    class="mt-1"
                                    required
                                />
                                <p v-if="form.errors.shipping_name" class="mt-1 text-sm text-destructive">
                                    {{ form.errors.shipping_name }}
                                </p>
                            </div>

                            <div>
                                <Label for="shipping_address">Address</Label>
                                <Input
                                    id="shipping_address"
                                    v-model="form.shipping_address"
                                    class="mt-1"
                                    required
                                />
                                <p v-if="form.errors.shipping_address" class="mt-1 text-sm text-destructive">
                                    {{ form.errors.shipping_address }}
                                </p>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <Label for="shipping_city">City</Label>
                                    <Input
                                        id="shipping_city"
                                        v-model="form.shipping_city"
                                        class="mt-1"
                                        required
                                    />
                                    <p v-if="form.errors.shipping_city" class="mt-1 text-sm text-destructive">
                                        {{ form.errors.shipping_city }}
                                    </p>
                                </div>
                                <div>
                                    <Label for="shipping_state">State</Label>
                                    <Input
                                        id="shipping_state"
                                        v-model="form.shipping_state"
                                        class="mt-1"
                                        required
                                    />
                                    <p v-if="form.errors.shipping_state" class="mt-1 text-sm text-destructive">
                                        {{ form.errors.shipping_state }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <Label for="shipping_zip">ZIP Code</Label>
                                    <Input
                                        id="shipping_zip"
                                        v-model="form.shipping_zip"
                                        class="mt-1"
                                        required
                                    />
                                    <p v-if="form.errors.shipping_zip" class="mt-1 text-sm text-destructive">
                                        {{ form.errors.shipping_zip }}
                                    </p>
                                </div>
                                <div>
                                    <Label for="shipping_country">Country</Label>
                                    <Input
                                        id="shipping_country"
                                        v-model="form.shipping_country"
                                        class="mt-1"
                                        required
                                    />
                                    <p v-if="form.errors.shipping_country" class="mt-1 text-sm text-destructive">
                                        {{ form.errors.shipping_country }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <Label for="shipping_phone">Phone (Optional)</Label>
                                <Input
                                    id="shipping_phone"
                                    v-model="form.shipping_phone"
                                    type="tel"
                                    class="mt-1"
                                />
                            </div>

                            <div>
                                <Label for="notes">Order Notes (Optional)</Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    class="mt-1"
                                    rows="3"
                                    placeholder="Special instructions for delivery..."
                                />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <Card class="sticky top-24">
                        <CardHeader>
                            <CardTitle>Order Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Items -->
                            <div class="space-y-3">
                                <div v-for="item in cartItems" :key="item.id" class="flex justify-between text-sm">
                                    <div>
                                        <span>{{ item.product.name }}</span>
                                        <span class="text-muted-foreground"> x {{ item.quantity }}</span>
                                    </div>
                                    <span>{{ formatPrice(item.product.price * item.quantity) }}</span>
                                </div>
                            </div>

                            <div class="border-t pt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Subtotal</span>
                                    <span>{{ formatPrice(subtotal) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Shipping</span>
                                    <span>{{ shipping === 0 ? 'Free' : formatPrice(shipping) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Tax</span>
                                    <span>{{ formatPrice(tax) }}</span>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between text-lg font-semibold">
                                    <span>Total</span>
                                    <span>{{ formatPrice(total) }}</span>
                                </div>
                            </div>

                            <Button type="submit" class="w-full" size="lg" :disabled="form.processing">
                                {{ form.processing ? 'Processing...' : 'Place Order' }}
                            </Button>

                            <p class="text-center text-xs text-muted-foreground">
                                By placing your order, you agree to our terms and conditions.
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </form>
        </div>
    </StoreLayout>
</template>
