<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { type VNode } from 'vue';
import {
    ArrowLeft,
    Pencil,
    Trash2,
    Store,
    Phone,
    Mail,
    MapPin,
    Clock,
    Link,
    ExternalLink,
} from 'lucide-vue-next';

import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { type BreadcrumbItem } from '@/types';
import type { OutletShowProps } from '../../../types';

defineOptions({
    layout: (h: (type: unknown, props: unknown, children: unknown) => VNode, page: VNode) =>
        h(AppLayout, { breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Outlets', href: '/dashboard/outlets' },
            { title: 'View Outlet', href: '#' },
        ] as BreadcrumbItem[] }, () => page),
});

const props = defineProps<OutletShowProps>();

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

const getOutletTypeLabel = (type: string | null) => {
    if (!type) return 'Not specified';
    return type.charAt(0).toUpperCase() + type.slice(1);
};

const handleBack = () => {
    router.visit('/dashboard/outlets');
};

const handleEdit = () => {
    router.visit(`/dashboard/outlets/${props.outlet.id}/edit`);
};

const handleDelete = () => {
    router.visit(`/dashboard/outlets/${props.outlet.id}/delete`);
};
</script>

<template>
    <Head :title="outlet.name" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <Button variant="ghost" @click="handleBack">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Back to Outlets
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

        <div class="grid gap-4 lg:grid-cols-3">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-4">
                <Card>
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="text-2xl">{{ outlet.name }}</CardTitle>
                                <CardDescription v-if="outlet.outlet_type">
                                    {{ getOutletTypeLabel(outlet.outlet_type) }}
                                </CardDescription>
                            </div>
                            <Badge :variant="getStatusVariant(outlet.status)">
                                {{ outlet.status }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <!-- Logo -->
                            <div>
                                <h4 class="text-sm font-medium text-muted-foreground mb-2">Logo</h4>
                                <div v-if="outlet.logo" class="w-32 h-32 overflow-hidden rounded-lg bg-muted">
                                    <img
                                        :src="outlet.logo"
                                        :alt="`${outlet.name} logo`"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <div v-else class="flex items-center justify-center w-32 h-32 bg-muted rounded-lg">
                                    <Store class="h-12 w-12 text-muted-foreground" />
                                </div>
                            </div>

                            <Separator />

                            <!-- Contact Information -->
                            <div>
                                <h4 class="text-sm font-medium text-muted-foreground mb-3">Contact Information</h4>
                                <div class="space-y-3">
                                    <div v-if="outlet.address" class="flex items-start gap-3">
                                        <MapPin class="h-4 w-4 text-muted-foreground mt-0.5" />
                                        <span>{{ outlet.address }}</span>
                                    </div>
                                    <div v-if="outlet.phone" class="flex items-center gap-3">
                                        <Phone class="h-4 w-4 text-muted-foreground" />
                                        <a :href="`tel:${outlet.phone}`" class="hover:underline">
                                            {{ outlet.phone }}
                                        </a>
                                    </div>
                                    <div v-if="outlet.email" class="flex items-center gap-3">
                                        <Mail class="h-4 w-4 text-muted-foreground" />
                                        <a :href="`mailto:${outlet.email}`" class="hover:underline">
                                            {{ outlet.email }}
                                        </a>
                                    </div>
                                    <div v-if="!outlet.address && !outlet.phone && !outlet.email" class="text-muted-foreground text-sm">
                                        No contact information available
                                    </div>
                                </div>
                            </div>

                            <Separator />

                            <!-- Links -->
                            <div>
                                <h4 class="text-sm font-medium text-muted-foreground mb-3">Links</h4>
                                <div class="space-y-3">
                                    <div v-if="outlet.google_map_url" class="flex items-center gap-3">
                                        <MapPin class="h-4 w-4 text-muted-foreground" />
                                        <a
                                            :href="outlet.google_map_url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="flex items-center gap-1 hover:underline text-primary"
                                        >
                                            View on Google Maps
                                            <ExternalLink class="h-3 w-3" />
                                        </a>
                                    </div>
                                    <div v-if="outlet.url_deeplink" class="flex items-center gap-3">
                                        <Link class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm break-all">{{ outlet.url_deeplink }}</span>
                                    </div>
                                    <div v-if="!outlet.google_map_url && !outlet.url_deeplink" class="text-muted-foreground text-sm">
                                        No links available
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <!-- Schedule -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center gap-2">
                            <Clock class="h-4 w-4" />
                            Schedule
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div v-if="outlet.schedule_mode" class="flex justify-between">
                            <span class="text-muted-foreground">Mode</span>
                            <span class="font-medium capitalize">{{ outlet.schedule_mode }}</span>
                        </div>
                        <div v-if="outlet.schedule_days" class="flex justify-between">
                            <span class="text-muted-foreground">Days</span>
                            <span class="font-medium">{{ outlet.schedule_days }}</span>
                        </div>
                        <div v-if="outlet.schedule_start_time || outlet.schedule_end_time" class="flex justify-between">
                            <span class="text-muted-foreground">Hours</span>
                            <span class="font-medium">
                                {{ outlet.schedule_start_time || '--' }} - {{ outlet.schedule_end_time || '--' }}
                            </span>
                        </div>
                        <div v-if="outlet.schedule_start_date" class="flex justify-between">
                            <span class="text-muted-foreground">Start Date</span>
                            <span class="text-sm">
                                {{ new Date(outlet.schedule_start_date).toLocaleDateString() }}
                            </span>
                        </div>
                        <div v-if="outlet.schedule_end_date" class="flex justify-between">
                            <span class="text-muted-foreground">End Date</span>
                            <span class="text-sm">
                                {{ new Date(outlet.schedule_end_date).toLocaleDateString() }}
                            </span>
                        </div>
                        <div v-if="outlet.schedule_status" class="flex justify-between">
                            <span class="text-muted-foreground">Schedule Status</span>
                            <Badge variant="outline" class="capitalize">
                                {{ outlet.schedule_status }}
                            </Badge>
                        </div>
                        <div
                            v-if="!outlet.schedule_mode && !outlet.schedule_days && !outlet.schedule_start_time && !outlet.schedule_status"
                            class="text-muted-foreground text-sm text-center py-2"
                        >
                            No schedule configured
                        </div>
                    </CardContent>
                </Card>

                <!-- Details -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg">Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Type</span>
                            <span class="font-medium">{{ getOutletTypeLabel(outlet.outlet_type) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Status</span>
                            <Badge :variant="getStatusVariant(outlet.status)">
                                {{ outlet.status }}
                            </Badge>
                        </div>
                        <Separator />
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Created</span>
                            <span class="text-sm">
                                {{ new Date(outlet.created_at).toLocaleDateString() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Updated</span>
                            <span class="text-sm">
                                {{ new Date(outlet.updated_at).toLocaleDateString() }}
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
