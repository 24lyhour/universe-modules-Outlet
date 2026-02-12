<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer } from '@/components/ui/chart';
import {
    VisXYContainer,
    VisStackedBar,
    VisAxis,
} from '@unovis/vue';
import {
    Store,
    CheckCircle,
    XCircle,
    RefreshCw,
    Calendar,
} from 'lucide-vue-next';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { useChartColors } from '@/composables/useChartColors';

export interface OutletMetrics {
    total: number;
    active: number;
    inactive: number;
}

export interface OutletWidgetProps {
    metrics: OutletMetrics;
    dateRange?: string;
    loading?: boolean;
}

const props = withDefaults(defineProps<OutletWidgetProps>(), {
    dateRange: '30d',
    loading: false,
});

const emit = defineEmits<{
    (e: 'dateRangeChange', value: string): void;
    (e: 'refresh'): void;
}>();

const selectedDateRange = ref(props.dateRange);
const { chartColors } = useChartColors();

// Date range options
const dateRangeOptions = [
    { value: 'today', label: 'Today' },
    { value: '7d', label: 'Last 7 Days' },
    { value: '30d', label: 'Last 30 Days' },
    { value: '90d', label: 'Last 90 Days' },
    { value: 'year', label: 'This Year' },
];

/**
 * Outlet bar chart data
 */
const outletBarData = computed(() => {
    return [
        { label: 'Total', value: props.metrics.total },
        { label: 'Active', value: props.metrics.active },
        { label: 'Inactive', value: props.metrics.inactive },
    ];
});

const outletChartConfig = computed(() => ({
    value: { label: 'Count', color: chartColors.value.chart2 },
}));

// Watch date range changes
watch(selectedDateRange, (newValue) => {
    emit('dateRangeChange', newValue);
});

// Methods
const handleRefresh = () => {
    emit('refresh');
};

const formatNumber = (num: number) => {
    return new Intl.NumberFormat().format(num);
};

const formatPercent = (num: number) => {
    return `${num.toFixed(1)}%`;
};
</script>

<template>
    <div class="space-y-6">
        <!-- Header with Date Filter -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight">Outlet Performance Metrics</h2>
                <p class="text-sm text-muted-foreground">Track outlets and locations overview</p>
            </div>
            <div class="flex items-center gap-2">
                <Select v-model="selectedDateRange">
                    <SelectTrigger class="w-[160px]">
                        <Calendar class="mr-2 h-4 w-4" />
                        <SelectValue placeholder="Select period" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in dateRangeOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <Button variant="outline" size="icon" @click="handleRefresh" :disabled="loading">
                    <RefreshCw class="h-4 w-4" :class="{ 'animate-spin': loading }" />
                </Button>
            </div>
        </div>

        <!-- Key Metrics Grid -->
        <div class="grid gap-4 md:grid-cols-3">
            <!-- Total Outlets -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">Total Outlets</CardTitle>
                    <Store class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ formatNumber(metrics.total) }}</div>
                    <p class="text-xs text-muted-foreground">All outlets in system</p>
                </CardContent>
            </Card>

            <!-- Active Outlets -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">Active</CardTitle>
                    <CheckCircle class="h-4 w-4 text-green-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-green-600">{{ formatNumber(metrics.active) }}</div>
                    <p class="text-xs text-muted-foreground">
                        {{ formatPercent((metrics.active / metrics.total) * 100) }} of total
                    </p>
                </CardContent>
            </Card>

            <!-- Inactive Outlets -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">Inactive</CardTitle>
                    <XCircle class="h-4 w-4 text-yellow-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-yellow-600">{{ formatNumber(metrics.inactive) }}</div>
                    <p class="text-xs text-muted-foreground">
                        {{ formatPercent((metrics.inactive / metrics.total) * 100) }} of total
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Outlet Bar Chart -->
        <Card>
            <CardHeader>
                <CardTitle>Outlet Statistics</CardTitle>
                <CardDescription>Overview of outlet data</CardDescription>
            </CardHeader>
            <CardContent>
                <ChartContainer :config="outletChartConfig" class="h-[300px]">
                    <VisXYContainer :data="outletBarData">
                        <VisStackedBar
                            :x="(_: any, i: number) => i"
                            :y="(d: any) => d.value"
                            :color="chartColors.chart2"
                            :barPadding="0.3"
                            :roundedCorners="4"
                        />
                        <VisAxis
                            type="x"
                            :tickFormat="(i: number) => outletBarData[i]?.label || ''"
                        />
                        <VisAxis type="y" />
                    </VisXYContainer>
                </ChartContainer>
            </CardContent>
        </Card>
    </div>
</template>