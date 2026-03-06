<script setup lang="ts">
import { ref, computed, type VNode } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { TableReusable, StatsCard, ButtonGroup } from '@/components/shared';
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
import { Plus, Tag, CheckCircle, XCircle, Eye, Pencil, Trash2, X, Download, Database } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { TypeOutlet, TypeOutletIndexProps } from '../../../types';
import { toast } from '@/composables/useToast';

// Persistent layout for momentum-modal
defineOptions({
    layout: (h: (type: unknown, props: unknown, children: unknown) => VNode, page: VNode) =>
        h(AppLayout, {
            breadcrumbs: [
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Outlets', href: '/dashboard/outlets' },
                { title: 'Types', href: '/dashboard/outlet-types' },
            ] as BreadcrumbItem[]
        }, () => page),
});

const props = defineProps<TypeOutletIndexProps>();

// Filter state
const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

// Selection state for bulk operations
const selectedIds = ref<(string | number)[]>([]);

const columns: TableColumn<TypeOutlet>[] = [
    {
        key: 'name',
        label: 'Name',
        render: (type) => type.name,
    },
    {
        key: 'description',
        label: 'Description',
        render: (type) => type.description || '-',
    },
    {
        key: 'status',
        label: 'Status',
    },
];

const actions: TableAction<TypeOutlet>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (type) => router.visit(`/dashboard/outlet-types/${type.id}`),
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (type) => router.visit(`/dashboard/outlet-types/${type.id}/edit`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (type) => router.visit(`/dashboard/outlet-types/${type.id}/delete`),
        variant: 'destructive',
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.typeOutlets.meta.current_page,
    last_page: props.typeOutlets.meta.last_page,
    per_page: props.typeOutlets.meta.per_page,
    total: props.typeOutlets.meta.total,
}));

const handlePageChange = (page: number) => {
    router.get('/dashboard/outlet-types', {
        page,
        per_page: pagination.value.per_page,
        search: searchQuery.value || undefined,
        status: statusFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/outlet-types', {
        page: 1,
        per_page: perPage,
        search: searchQuery.value || undefined,
        status: statusFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const handleSearch = (search: string) => {
    searchQuery.value = search;
    router.get('/dashboard/outlet-types', {
        search: search || undefined,
        status: statusFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const handleStatusFilter = (status: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    const statusStr = String(status || 'all');
    statusFilter.value = statusStr === 'all' ? '' : statusStr;
    router.get('/dashboard/outlet-types', {
        search: searchQuery.value || undefined,
        status: statusStr === 'all' ? undefined : statusStr,
    }, { preserveState: true, preserveScroll: true });
};

// Check if any filters are active
const hasActiveFilters = computed(() => {
    return !!(searchQuery.value || statusFilter.value);
});

const handleClearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    router.get('/dashboard/outlet-types', {}, { preserveState: true, preserveScroll: true });
};

const handleCreate = () => {
    router.visit('/dashboard/outlet-types/create');
};

const openBulkDeleteDialog = () => {
    const params = new URLSearchParams();
    selectedIds.value.forEach((id) => {
        params.append('ids[]', String(id));
    });
    router.visit(`/dashboard/outlet-types/bulk-delete?${params.toString()}`);
};

const handleStatusToggle = (type: TypeOutlet, newStatus: boolean) => {
    router.put(`/dashboard/outlet-types/${type.id}/toggle-status`, {
        status: newStatus ? 'active' : 'inactive',
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            toast.success(`Type ${newStatus ? 'activated' : 'deactivated'} successfully.`);
        },
    });
};
</script>

<template>
    <div>
        <Head title="Outlet Types" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatsCard
                    title="Total Types"
                    :value="props.stats.total"
                    :icon="Tag"
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
                        <h2 class="text-lg font-semibold">Outlet Types</h2>
                        <p class="text-sm text-muted-foreground">Manage outlet types</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <ButtonGroup>
                            <Button variant="default">
                                <Database class="mr-2 h-4 w-4" />
                                All
                            </Button>
                            <Button variant="outline" @click="router.visit('/dashboard/outlet-types/trash')">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Trash
                            </Button>
                        </ButtonGroup>
                        <Button variant="outline" as="a" href="/dashboard/outlet-types/export">
                            <Download class="mr-2 h-4 w-4" />
                            Export
                        </Button>
                        <Button @click="handleCreate">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Type
                        </Button>
                    </div>
                </div>

                <!-- Table -->
                <TableReusable
                    v-model:selected="selectedIds"
                    :data="props.typeOutlets.data"
                    :columns="columns"
                    :actions="actions"
                    :pagination="pagination"
                    :searchable="true"
                    :selectable="true"
                    select-key="id"
                    search-placeholder="Search types..."
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
