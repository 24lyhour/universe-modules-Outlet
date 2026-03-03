<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import type { InertiaForm } from '@inertiajs/vue3';
import type { TypeOutletFormData } from '../../types';
import TiptapEditor from '@/components/TiptapEditor.vue';

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
            <TiptapEditor
                v-model="model.description"
                placeholder="Enter description"
                max-height="400px"
                min-height="250px"
            />
            <p v-if="model.errors.description" class="text-sm text-destructive">
                {{ model.errors.description }}
            </p>
        </div>

        <div class="space-y-2">
            <Label for="status">Status</Label>
            <div class="flex items-center space-x-2 pt-2">
                <Switch id="status" v-model="isActive" />
                <Label for="status" class="font-normal">
                    {{ isActive ? 'Active' : 'Inactive' }}
                </Label>
            </div>
            <p v-if="model.errors.status" class="text-sm text-destructive">
                {{ model.errors.status }}
            </p>
        </div>
    </div>
</template>
