<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import type { InertiaForm } from '@inertiajs/vue3';
import type { TypeOutletFormData } from '../../types';

interface Props {
    mode?: 'create' | 'edit';
}

withDefaults(defineProps<Props>(), {
    mode: 'create',
});

const model = defineModel<InertiaForm<TypeOutletFormData>>({ required: true });

// Convert status string to boolean for Switch
const isActive = computed({
    get: () => model.value.status === 'active',
    set: (value: boolean) => {
        model.value.status = value ? 'active' : 'inactive';
    },
});
</script>

<template>
    <div class="space-y-4">
        <div class="space-y-2">
            <Label for="name">Name <span class="text-destructive">*</span></Label>
            <Input
                id="name"
                v-model="model.name"
                type="text"
                placeholder="Enter type name"
            />
            <p v-if="model.errors.name" class="text-sm text-destructive">
                {{ model.errors.name }}
            </p>
        </div>

        <div class="space-y-2">
            <Label for="description">Description</Label>
            <Textarea
                id="description"
                v-model="model.description"
                placeholder="Enter description"
                rows="3"
            />
            <p v-if="model.errors.description" class="text-sm text-destructive">
                {{ model.errors.description }}
            </p>
        </div>

        <div class="flex items-center justify-between">
            <div class="space-y-0.5">
                <Label for="status">Status</Label>
                <p class="text-sm text-muted-foreground">
                    {{ isActive ? 'Active' : 'Inactive' }}
                </p>
            </div>
            <Switch
                id="status"
                :checked="isActive"
                @update:checked="isActive = $event"
            />
        </div>
        <p v-if="model.errors.status" class="text-sm text-destructive">
            {{ model.errors.status }}
        </p>
    </div>
</template>
