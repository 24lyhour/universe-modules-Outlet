<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import { ImageUpload } from '@/components/shared';
import type { InertiaForm } from '@inertiajs/vue3';
import type { OutletFormData, TypeOutletOption } from '../../types';

interface Props {
    mode?: 'create' | 'edit';
    typeOutlets?: TypeOutletOption[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    typeOutlets: () => [],
});

const model = defineModel<InertiaForm<OutletFormData>>({ required: true });

// Convert logo string to array for ImageUpload component
const logoImages = computed({
    get: () => model.value.logo ? [model.value.logo] : [],
    set: (value: string[]) => {
        model.value.logo = value.length > 0 ? value[0] : '';
    },
});

// Convert status string to boolean for Switch component
const isActive = computed({
    get: () => model.value.status === 'active',
    set: (value: boolean) => {
        model.value.status = value ? 'active' : 'inactive';
    },
});
</script>

<template>
    <div class="space-y-6">
        <!-- Basic Information Section -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Basic Information</h3>
                <p class="text-sm text-muted-foreground">
                    {{ mode === 'create' ? 'Enter the outlet details' : 'Update the outlet details' }}
                </p>
            </div>
            <Separator />

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="name">Outlet Name <span class="text-destructive">*</span></Label>
                    <Input
                        id="name"
                        v-model="model.name"
                        type="text"
                        placeholder="Enter outlet name"
                    />
                    <p v-if="model.errors.name" class="text-sm text-destructive">
                        {{ model.errors.name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="outlet_type">Outlet Type <span class="text-destructive">*</span></Label>
                    <Select v-model="model.outlet_type">
                        <SelectTrigger>
                            <SelectValue placeholder="Select outlet type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="type in props.typeOutlets"
                                :key="type.id"
                                :value="type.id.toString()"
                            >
                                {{ type.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="model.errors.outlet_type" class="text-sm text-destructive">
                        {{ model.errors.outlet_type }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="phone">Phone</Label>
                    <Input
                        id="phone"
                        v-model="model.phone"
                        type="text"
                        placeholder="Enter phone number"
                    />
                    <p v-if="model.errors.phone" class="text-sm text-destructive">
                        {{ model.errors.phone }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        v-model="model.email"
                        type="email"
                        placeholder="Enter email address"
                    />
                    <p v-if="model.errors.email" class="text-sm text-destructive">
                        {{ model.errors.email }}
                    </p>
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <Label for="address">Address</Label>
                    <Textarea
                        id="address"
                        v-model="model.address"
                        placeholder="Enter outlet address"
                        rows="2"
                    />
                    <p v-if="model.errors.address" class="text-sm text-destructive">
                        {{ model.errors.address }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="status">Status <span class="text-destructive">*</span></Label>
                    <div class="flex items-center space-x-2 pt-2">
                        <Switch id="status" v-model:checked="isActive" />
                        <Label for="status" class="font-normal">
                            {{ isActive ? 'Active' : 'Inactive' }}
                        </Label>
                    </div>
                    <p v-if="model.errors.status" class="text-sm text-destructive">
                        {{ model.errors.status }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Logo Section -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Logo</h3>
                <p class="text-sm text-muted-foreground">Upload outlet logo</p>
            </div>
            <Separator />

            <ImageUpload
                v-model="logoImages"
                label=""
                :multiple="false"
                :max-files="1"
                :max-size="5"
                :error="model.errors.logo"
            />
        </div>

        <!-- Links Section -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Links</h3>
                <p class="text-sm text-muted-foreground">External links and integrations</p>
            </div>
            <Separator />

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2 sm:col-span-2">
                    <Label for="google_map_url">Google Maps URL</Label>
                    <Input
                        id="google_map_url"
                        v-model="model.google_map_url"
                        type="url"
                        placeholder="https://maps.google.com/..."
                    />
                    <p v-if="model.errors.google_map_url" class="text-sm text-destructive">
                        {{ model.errors.google_map_url }}
                    </p>
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <Label for="url_deeplink">Deep Link URL</Label>
                    <Input
                        id="url_deeplink"
                        v-model="model.url_deeplink"
                        type="text"
                        placeholder="Enter deep link URL"
                    />
                    <p v-if="model.errors.url_deeplink" class="text-sm text-destructive">
                        {{ model.errors.url_deeplink }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
