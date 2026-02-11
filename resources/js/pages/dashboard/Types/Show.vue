<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { type VNode } from 'vue';
import { ArrowLeft, Pencil, Trash2, Tag } from 'lucide-vue-next';

import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { type BreadcrumbItem } from '@/types';
import type { TypeOutletShowProps } from '../../../types';

defineOptions({
    layout: (h: (type: unknown, props: unknown, children: unknown) => VNode, page: VNode) =>
        h(AppLayout, { breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Outlets', href: '/dashboard/outlets' },
            { title: 'Types', href: '/dashboard/outlet-types' },
            { title: 'View Type', href: '#' },
        ] as BreadcrumbItem[] }, () => page),
});

const props = defineProps<TypeOutletShowProps>();

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'active':
            return 'default';
        case 'inactive':
            return 'secondary';
        default:
            return 'outline';
    }
};

const handleBack = () => {
    router.visit('/dashboard/outlet-types');
};

const handleEdit = () => {
    router.visit(`/dashboard/outlet-types/${props.typeOutlet.id}/edit`);
};

const handleDelete = () => {
    router.visit(`/dashboard/outlet-types/${props.typeOutlet.id}/delete`);
};
</script>

<template>
    <Head :title="typeOutlet.name" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <Button variant="ghost" @click="handleBack">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Back to Types
            </Button>
            <div class="flex gap-2">
                <Button variant="outline" @click="handleEdit">
                    <Pencil class="mr-2 h-4 w-4" />
                    Edit
                </Button>
                <Button variant="destructive" @click="handleDelete">
                    <Trash2 class="mr-2 h-4 w-4" />
                    Delete
                </Button>
            </div>
        </div>

        <div class="max-w-2xl">
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                            <Tag class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <div class="flex-1">
                            <CardTitle class="text-xl">{{ typeOutlet.name }}</CardTitle>
                        </div>
                        <Badge :variant="getStatusVariant(typeOutlet.status)">
                            {{ typeOutlet.status }}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-muted-foreground">Description</h4>
                        <p class="mt-1">{{ typeOutlet.description || 'No description' }}</p>
                    </div>

                    <Separator />

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-muted-foreground">Created</span>
                            <p class="font-medium">
                                {{ new Date(typeOutlet.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                        <div>
                            <span class="text-muted-foreground">Updated</span>
                            <p class="font-medium">
                                {{ new Date(typeOutlet.updated_at).toLocaleDateString() }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
