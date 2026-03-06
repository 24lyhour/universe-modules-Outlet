<script setup lang="ts">
import { ref, computed, type VNode } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { TableReusable, StatsCard } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import { Plus, Store, CheckCircle, XCircle, Eye, Pencil, Trash2, Clock, CalendarClock, X, Download, Database } from 'lucide-vue-next';
import { ButtonGroup } from '@/components/shared';
import type { BreadcrumbItem } from '@/types';
import type { OutletIndexProps, Outlet } from '../../../types';
import { toast } from '@/composables/useToast';

// Persistent layout for momentum-modal
defineOptions({
    layout: (h: (type: unknown, props: unknown, children: unknown) => VNode, page: VNode) =>
        h(AppLayout, {
            breadcrumbs: [
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Outlets', href: '/dashboard/outlets' },
            ] as BreadcrumbItem[]
        }, () => page),
});

const props = defineProps<OutletIndexProps>();

// Filter state
const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const typeOutletFilter = ref(props.filters.type_outlet_id?.toString() || '');

// Selection state for bulk operations
const selectedUuids = ref<(string | number)[]>([]);

const columns: TableColumn<Outlet>[] = [
    {
        key: 'logo',
        label: 'Image',
        width: '80px',
    },
    {
        key: 'name',
        label: 'Outlet',
        render: (outlet) => outlet.name,
    },
    {
        key: 'type_outlet',
        label: 'Type',
        render: (outlet) => outlet.type_outlet?.name || '-',
    },
    {
        key: 'phone',
        label: 'Phone',
        render: (outlet) => outlet.phone || '-',
    },
    {
        key: 'email',
        label: 'Email',
        render: (outlet) => outlet.email || '-',
    },
    {
        key: 'schedule',
        label: 'Schedule',
    },
    {
        key: 'status',
        label: 'Status',
        render: (outlet) => outlet.status,
    },
];

const actions: TableAction<Outlet>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (outlet) => router.visit(`/dashboard/outlets/${outlet.uuid}`),
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (outlet) => router.visit(`/dashboard/outlets/${outlet.uuid}/edit`),
    },
    {
        label: 'Schedule',
        icon: Clock,
        onClick: (outlet) => router.visit(`/dashboard/outlets/${outlet.uuid}/schedule`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (outlet) => router.visit(`/dashboard/outlets/${outlet.uuid}/delete`),
        variant: 'destructive',
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.outlets.meta.current_page,
    last_page: props.outlets.meta.last_page,
    per_page: props.outlets.meta.per_page,
    total: props.outlets.meta.total,
}));

const handlePageChange = (page: number) => {
    router.get('/dashboard/outlets', {
        page,
        per_page: pagination.value.per_page,
        search: searchQuery.value || undefined,
        status: statusFilter.value || undefined,
        type_outlet_id: typeOutletFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/outlets', {
        page: 1,
        per_page: perPage,
        search: searchQuery.value || undefined,
        status: statusFilter.value || undefined,
        type_outlet_id: typeOutletFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const handleSearch = (search: string) => {
    searchQuery.value = search;
    router.get('/dashboard/outlets', {
        search: search || undefined,
        status: statusFilter.value || undefined,
        type_outlet_id: typeOutletFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const handleStatusFilter = (status: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    const statusStr = String(status || 'all');
    statusFilter.value = statusStr === 'all' ? '' : statusStr;
    router.get('/dashboard/outlets', {
        search: searchQuery.value || undefined,
        status: statusStr === 'all' ? undefined : statusStr,
        type_outlet_id: typeOutletFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const handleTypeOutletFilter = (typeId: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    const typeStr = String(typeId || 'all');
    const actualId = typeStr === 'all' ? '' : typeStr;
    typeOutletFilter.value = actualId;
    router.get('/dashboard/outlets', {
        search: searchQuery.value || undefined,
        status: statusFilter.value || undefined,
        type_outlet_id: actualId || undefined,
    }, { preserveState: true, preserveScroll: true });
};

// Check if any filters are active
const hasActiveFilters = computed(() => {
    return !!(searchQuery.value || statusFilter.value || typeOutletFilter.value);
});

const handleClearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    typeOutletFilter.value = '';
    router.get('/dashboard/outlets', {}, { preserveState: true, preserveScroll: true });
};

const handleCreate = () => {
    router.visit('/dashboard/outlets/create');
};

const openBulkDeleteDialog = () => {
    const params = new URLSearchParams();
    selectedUuids.value.forEach((uuid) => {
        params.append('uuids[]', String(uuid));
    });
    router.visit(`/dashboard/outlets/bulk-delete?${params.toString()}`);
};

const handleStatusToggle = (outlet: Outlet, newStatus: boolean) => {
    router.put(`/dashboard/outlets/${outlet.uuid}/toggle-status`, {
        status: newStatus ? 'active' : 'inactive',
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            toast.success(`Outlet ${newStatus ? 'activated' : 'deactivated'} successfully.`);
        },
    });
};
</script>

<template>
    <div>
        <Head title="Outlets" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Stats -->
        <div class="grid gap-4 md:grid-cols-3">
            <StatsCard
                title="Total Outlets"
                :value="props.outlets.meta.total"
                :icon="Store"
            />
            <StatsCard
                title="Active"
                :value="props.stats.active"
                :icon="CheckCircle"
                variant="success"
            />
            <StatsCard
                title="Inactive"
                :value="props.stats.inactive"
                :icon="XCircle"
                variant="warning"
            />
        </div>

        <!-- Main Content -->
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Outlets</h2>
                    <p class="text-sm text-muted-foreground">Manage your outlets</p>
                </div>
                <div class="flex items-center gap-2">
                    <ButtonGroup>
                        <Button variant="default">
                            <Database class="mr-2 h-4 w-4" />
                            All
                        </Button>
                        <Button variant="outline" @click="router.visit('/dashboard/outlets/trash')">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Trash
                        </Button>
                    </ButtonGroup>
                    <Button variant="outline" as="a" href="/dashboard/outlets/export">
                        <Download class="mr-2 h-4 w-4" />
                        Export
                    </Button>
                    <Button @click="handleCreate">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Outlet
                    </Button>
                </div>
            </div>

            <!-- Table -->
            <TableReusable
                v-model:selected="selectedUuids"
                :data="props.outlets.data"
                :columns="columns"
                :actions="actions"
                :pagination="pagination"
                :searchable="true"
                :selectable="true"
                select-key="uuid"
                search-placeholder="Search outlets..."
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
                @search="handleSearch"
            >
                <!-- Bulk Actions -->
                <template #bulk-actions>
                    <Button variant="destructive" size="sm" @click="openBulkDeleteDialog">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete Selected
                    </Button>
                </template>

                <!-- Toolbar slot for filters -->
                <template #toolbar>
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Type Outlet Filter -->
                        <Select :model-value="typeOutletFilter || 'all'" @update:model-value="handleTypeOutletFilter">
                            <SelectTrigger class="w-[180px]">
                                <SelectValue placeholder="All Types" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All Types</SelectItem>
                                <SelectItem
                                    v-for="type in props.typeOutlets"
                                    :key="type.id"
                                    :value="type.id.toString()"
                                >
                                    {{ type.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        <!-- Status Filter -->
                        <Select :model-value="statusFilter || 'all'" @update:model-value="handleStatusFilter">
                            <SelectTrigger class="w-[150px]">
                                <SelectValue placeholder="All Status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All Status</SelectItem>
                                <SelectItem value="active">Active</SelectItem>
                                <SelectItem value="inactive">Inactive</SelectItem>
                            </SelectContent>
                        </Select>

                        <!-- Clear Filters Button -->
                        <Button
                            v-if="hasActiveFilters"
                            variant="ghost"
                            size="sm"
                            @click="handleClearFilters"
                            class="text-muted-foreground hover:text-foreground"
                        >
                            <X class="mr-1 h-4 w-4" />
                            Clear Filters
                        </Button>
                    </div>
                </template>
                <template #cell-logo="{ item }">
                    <div v-if="item.logo" class="h-10 w-10 overflow-hidden rounded-lg">
                        <img
                            :src="item.logo"
                            :alt="item.name"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div v-else class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                        <Store class="h-5 w-5 text-primary" />
                    </div>
                </template>
                <template #cell-schedule="{ item }">
                    <Badge
                        v-if="item.schedule_status === 'active'"
                        variant="default"
                        class="gap-1.5 cursor-pointer hover:bg-primary/80 transition-colors"
                        @click.stop="router.visit(`/dashboard/outlets/${item.uuid}/schedule`)"
                    >
                        <CalendarClock class="h-3 w-3" />
                        {{ item.schedule_mode === 'always' ? 'Always' : item.schedule_start_time ? `${item.schedule_start_time} - ${item.schedule_end_time}` : 'Configured' }}
                    </Badge>
                    <Badge
                        v-else
                        variant="outline"
                        class="gap-1.5 cursor-pointer hover:bg-muted transition-colors text-muted-foreground"
                        @click.stop="router.visit(`/dashboard/outlets/${item.uuid}/schedule`)"
                    >
                        <Clock class="h-3 w-3" />
                        Not Set
                    </Badge>
                </template>
                <template #cell-status="{ item }">
                    <div class="flex items-center gap-2" @click.stop>
                        <Switch
                            :model-value="item.status === 'active'"
                            @update:model-value="handleStatusToggle(item, $event)"
                        />
                        <span class="text-sm text-muted-foreground">
                            {{ item.status === 'active' ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </template>
            </TableReusable>
        </div>
        </div>
    </div>
</template>
