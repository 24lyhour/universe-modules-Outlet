<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { ModalForm } from '@/components/shared';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import { Clock, Calendar, CalendarDays, CalendarRange } from 'lucide-vue-next';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface ScheduleData {
    schedule_mode: string;
    schedule_days: string;
    schedule_start_time: string;
    schedule_end_time: string;
    schedule_start_date: string;
    schedule_end_date: string;
    schedule_status: string;
}

interface Props {
    scheduleData: ScheduleData;
    loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    loading: false,
});

const open = defineModel<boolean>('open', { default: false });

const emit = defineEmits<{
    (e: 'save', data: ScheduleData): void;
}>();

const DAYS_OF_WEEK = [
    { value: 'monday', label: 'Mon' },
    { value: 'tuesday', label: 'Tue' },
    { value: 'wednesday', label: 'Wed' },
    { value: 'thursday', label: 'Thu' },
    { value: 'friday', label: 'Fri' },
    { value: 'saturday', label: 'Sat' },
    { value: 'sunday', label: 'Sun' },
];

const SCHEDULE_MODES = [
    { value: 'always', label: 'Always Open', description: 'Open 24/7', icon: Clock },
    { value: 'daily', label: 'Daily Schedule', description: 'Same hours every day', icon: Calendar },
    { value: 'weekly', label: 'Weekly Schedule', description: 'Different hours per day', icon: CalendarDays },
    { value: 'date_range', label: 'Date Range', description: 'Temporary schedule', icon: CalendarRange },
];

// Local state
const localData = ref<ScheduleData>({
    schedule_mode: '',
    schedule_days: '',
    schedule_start_time: '',
    schedule_end_time: '',
    schedule_start_date: '',
    schedule_end_date: '',
    schedule_status: '',
});

const selectedDays = ref<string[]>([]);

// Watch for prop changes to sync local state
watch(() => props.scheduleData, (newVal) => {
    localData.value = { ...newVal };
    // Parse days from JSON string or comma-separated string
    try {
        const days = newVal.schedule_days ? JSON.parse(newVal.schedule_days) : [];
        selectedDays.value = Array.isArray(days) ? days : [];
    } catch {
        selectedDays.value = newVal.schedule_days ? newVal.schedule_days.split(',') : [];
    }
}, { immediate: true });

const isScheduleEnabled = computed({
    get: () => localData.value.schedule_status === 'active',
    set: (value: boolean) => {
        localData.value.schedule_status = value ? 'active' : 'inactive';
    },
});

const toggleDay = (day: string) => {
    const index = selectedDays.value.indexOf(day);
    if (index > -1) {
        selectedDays.value.splice(index, 1);
    } else {
        selectedDays.value.push(day);
    }
};

const isDaySelected = (day: string) => selectedDays.value.includes(day);

const selectedModeInfo = computed(() => {
    return SCHEDULE_MODES.find(m => m.value === localData.value.schedule_mode);
});

const handleSubmit = () => {
    // Convert selected days to JSON string
    localData.value.schedule_days = JSON.stringify(selectedDays.value);
    emit('save', { ...localData.value });
    open.value = false;
};

const handleCancel = () => {
    open.value = false;
};
</script>

<template>
    <ModalForm
        v-model:open="open"
        title="Manage Schedules"
        description="Configure the outlet's operating schedule"
        mode="edit"
        size="lg"
        submit-text="Save Schedule"
        :loading="loading"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-6">
            <!-- Enable Schedule -->
            <div class="flex items-center justify-between rounded-lg border p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full" :class="isScheduleEnabled ? 'bg-primary/10' : 'bg-muted'">
                        <Clock class="h-5 w-5" :class="isScheduleEnabled ? 'text-primary' : 'text-muted-foreground'" />
                    </div>
                    <div>
                        <Label class="text-base font-medium">Enable Schedule</Label>
                        <p class="text-sm text-muted-foreground">
                            {{ isScheduleEnabled ? 'Schedule is active' : 'Turn on to set operating hours' }}
                        </p>
                    </div>
                </div>
                <Switch v-model="isScheduleEnabled" />
            </div>

            <Separator />

            <!-- Schedule Mode -->
            <div class="space-y-3">
                <Label class="text-base font-medium">Schedule Mode</Label>
                <Select v-model="localData.schedule_mode">
                    <SelectTrigger class="w-full h-12">
                        <SelectValue placeholder="Select schedule mode">
                            <div v-if="selectedModeInfo" class="flex items-center gap-2">
                                <component :is="selectedModeInfo.icon" class="h-4 w-4" />
                                <span>{{ selectedModeInfo.label }}</span>
                            </div>
                        </SelectValue>
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="mode in SCHEDULE_MODES"
                            :key="mode.value"
                            :value="mode.value"
                        >
                            <div class="flex items-center gap-3">
                                <component :is="mode.icon" class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <div class="font-medium">{{ mode.label }}</div>
                                    <div class="text-xs text-muted-foreground">{{ mode.description }}</div>
                                </div>
                            </div>
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <!-- Days Selection (for weekly mode) -->
            <div v-if="localData.schedule_mode === 'weekly'" class="space-y-3">
                <Label class="text-base font-medium">Operating Days</Label>
                <p class="text-sm text-muted-foreground">Select the days your outlet is open</p>
                <div class="grid grid-cols-7 gap-2">
                    <Button
                        v-for="day in DAYS_OF_WEEK"
                        :key="day.value"
                        type="button"
                        size="sm"
                        class="h-10"
                        :variant="isDaySelected(day.value) ? 'default' : 'outline'"
                        @click="toggleDay(day.value)"
                    >
                        {{ day.label }}
                    </Button>
                </div>
            </div>

            <!-- Time Range -->
            <div v-if="localData.schedule_mode && localData.schedule_mode !== 'always'" class="space-y-3">
                <Label class="text-base font-medium">Operating Hours</Label>
                <p class="text-sm text-muted-foreground">Set opening and closing times</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="schedule_start_time" class="text-sm">Opening Time</Label>
                        <Input
                            id="schedule_start_time"
                            v-model="localData.schedule_start_time"
                            type="time"
                            class="h-11"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="schedule_end_time" class="text-sm">Closing Time</Label>
                        <Input
                            id="schedule_end_time"
                            v-model="localData.schedule_end_time"
                            type="time"
                            class="h-11"
                        />
                    </div>
                </div>
            </div>

            <!-- Date Range (for date_range mode) -->
            <div v-if="localData.schedule_mode === 'date_range'" class="space-y-3">
                <Label class="text-base font-medium">Date Range</Label>
                <p class="text-sm text-muted-foreground">Set the start and end dates for this schedule</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="schedule_start_date" class="text-sm">Start Date</Label>
                        <Input
                            id="schedule_start_date"
                            v-model="localData.schedule_start_date"
                            type="date"
                            class="h-11"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="schedule_end_date" class="text-sm">End Date</Label>
                        <Input
                            id="schedule_end_date"
                            v-model="localData.schedule_end_date"
                            type="date"
                            class="h-11"
                        />
                    </div>
                </div>
            </div>
        </div>
    </ModalForm>
</template>
