<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    Box,
    Folder,
    Home,
    Menu,
    Package,
    Settings,
    ShoppingCart,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const sidebarOpen = ref(false);

const navigation = [
    { name: 'Dashboard', href: '/admin', icon: BarChart3 },
    { name: 'Products', href: '/admin/products', icon: Package },
    { name: 'Categories', href: '/admin/categories', icon: Folder },
    { name: 'Orders', href: '/admin/orders', icon: ShoppingCart },
];

const currentPath = computed(() => {
    const url = page.url;
    return url.split('?')[0];
});

const isActive = (href: string) => {
    if (href === '/admin') {
        return currentPath.value === '/admin';
    }
    return currentPath.value.startsWith(href);
};

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};
</script>

<template>
    <div class="min-h-screen bg-muted/30">
        <!-- Mobile sidebar backdrop -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-background/80 backdrop-blur-sm lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 transform bg-card border-r transition-transform duration-200 lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <div class="flex h-16 items-center justify-between border-b px-6">
                <Link href="/admin" class="flex items-center gap-2">
                    <Box class="h-6 w-6" />
                    <span class="font-bold">Admin</span>
                </Link>
                <Button variant="ghost" size="icon" class="lg:hidden" @click="sidebarOpen = false">
                    <X class="h-5 w-5" />
                </Button>
            </div>

            <nav class="flex-1 space-y-1 p-4">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors',
                        isActive(item.href)
                            ? 'bg-primary text-primary-foreground'
                            : 'text-muted-foreground hover:bg-accent hover:text-foreground',
                    ]"
                >
                    <component :is="item.icon" class="h-5 w-5" />
                    {{ item.name }}
                </Link>
            </nav>

            <div class="border-t p-4">
                <Link
                    href="/"
                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-accent hover:text-foreground"
                >
                    <Home class="h-5 w-5" />
                    Back to Store
                </Link>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top bar -->
            <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b bg-background px-4 lg:px-6">
                <Button variant="ghost" size="icon" class="lg:hidden" @click="sidebarOpen = true">
                    <Menu class="h-5 w-5" />
                </Button>

                <div class="flex-1" />

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" class="relative h-9 w-9 rounded-full">
                            <Avatar class="h-9 w-9">
                                <AvatarFallback>{{ user ? getInitials(user.name) : 'U' }}</AvatarFallback>
                            </Avatar>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <div class="flex items-center justify-start gap-2 p-2">
                            <div class="flex flex-col space-y-1 leading-none">
                                <p class="font-medium">{{ user?.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ user?.email }}</p>
                            </div>
                        </div>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem as-child>
                            <Link href="/settings/profile" class="w-full cursor-pointer">
                                <Settings class="mr-2 h-4 w-4" />
                                Settings
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem as-child>
                            <Link href="/logout" method="post" as="button" class="w-full cursor-pointer text-left">
                                Logout
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </header>

            <!-- Page content -->
            <main class="p-4 lg:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
