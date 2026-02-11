<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import OutletForm from '../../../Components/Dashboard/OutletForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { outletSchema } from '../../../validation/outletSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { OutletFormData, OutletCreateProps } from '../../../types';

const props = defineProps<OutletCreateProps>();

const { show, close, redirect } = useModal();

const isOpen = computed({
    get: () => show.value,
    set: (val: boolean) => {
        if (!val) {
            close();
            redirect();
        }
    },
});

const form = useForm<OutletFormData>({
    name: '',
    outlet_type: '',
    address: '',
    phone: '',
    email: '',
    logo: '',
    image_url: '',
    google_map_url: '',
    url_deeplink: '',
    status: 'active',
    schedule_mode: '',
    schedule_days: '',
    schedule_start_time: '',
    schedule_end_time: '',
    schedule_start_date: '',
    schedule_end_date: '',
    schedule_status: '',
});

// Use shared validation composable
const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    outletSchema,
    ['name', 'outlet_type'] 
);

// Get form data for validation
const getFormData = () => ({
    name: form.name,
    outlet_type: form.outlet_type,
    address: form.address || null,
    phone: form.phone || null,
    email: form.email || null,
    logo: form.logo || null,
    image_url: form.image_url || null,
    google_map_url: form.google_map_url || null,
    url_deeplink: form.url_deeplink || null,
    status: form.status,
    schedule_mode: form.schedule_mode || null,
    schedule_days: form.schedule_days || null,
    schedule_start_time: form.schedule_start_time || null,
    schedule_end_time: form.schedule_end_time || null,
    schedule_start_date: form.schedule_start_date || null,
    schedule_end_date: form.schedule_end_date || null,
    schedule_status: form.schedule_status || null,
});

// Watch form changes to validate in real-time
watch(() => [form.name, form.outlet_type], () => {
    if (form.name || form.outlet_type) validateForm(getFormData());
});

// Check if form is valid for submit button state
const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.post('/dashboard/outlets', {
            onSuccess: () => {
                close();
                redirect();
            },
        });
    });
};

const handleCancel = () => {
    close();
    redirect();
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Create Outlet"
        description="Add a new outlet to your business"
        mode="create"
        size="xl"
        submit-text="Create Outlet"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <OutletForm v-model="form" mode="create" :type-outlets="props.typeOutlets" />
    </ModalForm>
</template>
