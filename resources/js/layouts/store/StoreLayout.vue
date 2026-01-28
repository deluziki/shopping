<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, ShoppingBag, User, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const cartCount = computed(() => (page.props.cartCount as number) || 0);
const mobileMenuOpen = ref(false);

const navigation = [
    { name: 'Home', href: '/' },
    { name: 'Suits', href: '/products/category/suits' },
    { name: 'Shirts', href: '/products/category/shirts' },
    { name: 'All Products', href: '/products' },
];
</script>

<template>
    <div class="min-h-screen bg-background">
        <!-- Navigation -->
        <header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2">
                    <span class="text-xl font-bold tracking-tight">ELEGANCE</span>
                </Link>

                <!-- Desktop Navigation -->
                <div class="hidden items-center gap-8 md:flex">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                    >
                        {{ item.name }}
                    </Link>
                </div>

                <!-- Right side -->
                <div class="flex items-center gap-4">
                    <!-- Cart -->
                    <Link href="/cart" class="relative">
                        <Button variant="ghost" size="icon">
                            <ShoppingBag class="h-5 w-5" />
                            <span v-if="cartCount > 0" class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs text-primary-foreground">
                                {{ cartCount }}
                            </span>
                        </Button>
                    </Link>

                    <!-- User Menu -->
                    <template v-if="user">
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon">
                                    <User class="h-5 w-5" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-48">
                                <div class="px-2 py-1.5">
                                    <p class="text-sm font-medium">{{ user.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                                </div>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link href="/orders" class="w-full cursor-pointer">My Orders</Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link href="/settings/profile" class="w-full cursor-pointer">Settings</Link>
                                </DropdownMenuItem>
                                <template v-if="user.role === 'admin'">
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem as-child>
                                        <Link href="/admin" class="w-full cursor-pointer">Admin Dashboard</Link>
                                    </DropdownMenuItem>
                                </template>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link href="/logout" method="post" as="button" class="w-full cursor-pointer text-left">
                                        Logout
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </template>
                    <template v-else>
                        <Link href="/login">
                            <Button variant="ghost" size="sm">Login</Button>
                        </Link>
                        <Link href="/register" class="hidden sm:block">
                            <Button size="sm">Register</Button>
                        </Link>
                    </template>

                    <!-- Mobile menu button -->
                    <Button variant="ghost" size="icon" class="md:hidden" @click="mobileMenuOpen = !mobileMenuOpen">
                        <Menu v-if="!mobileMenuOpen" class="h-5 w-5" />
                        <X v-else class="h-5 w-5" />
                    </Button>
                </div>
            </nav>

            <!-- Mobile Navigation -->
            <div v-if="mobileMenuOpen" class="border-t md:hidden">
                <div class="space-y-1 px-4 py-3">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        class="block rounded-md px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-foreground"
                        @click="mobileMenuOpen = false"
                    >
                        {{ item.name }}
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-16 border-t bg-muted/30">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid gap-8 md:grid-cols-4">
                    <div>
                        <h3 class="text-lg font-semibold">ELEGANCE</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Premium suits and shirts for the modern gentleman.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold">Shop</h4>
                        <ul class="mt-2 space-y-2 text-sm text-muted-foreground">
                            <li><Link href="/products/category/suits" class="hover:text-foreground">Suits</Link></li>
                            <li><Link href="/products/category/shirts" class="hover:text-foreground">Shirts</Link></li>
                            <li><Link href="/products" class="hover:text-foreground">All Products</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold">Account</h4>
                        <ul class="mt-2 space-y-2 text-sm text-muted-foreground">
                            <li><Link href="/login" class="hover:text-foreground">Login</Link></li>
                            <li><Link href="/register" class="hover:text-foreground">Register</Link></li>
                            <li><Link href="/orders" class="hover:text-foreground">My Orders</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold">Contact</h4>
                        <ul class="mt-2 space-y-2 text-sm text-muted-foreground">
                            <li>support@elegance.com</li>
                            <li>1-800-ELEGANCE</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t pt-8 text-center text-sm text-muted-foreground">
                    <p>&copy; {{ new Date().getFullYear() }} Elegance. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
