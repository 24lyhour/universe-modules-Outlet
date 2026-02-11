<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import OutletForm from '../../../Components/Dashboard/OutletForm.vue';
import { outletSchema } from '../../../validation/outletSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { OutletFormData, OutletEditProps } from '../../../types';

const props = defineProps<OutletEditProps>();

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
    name: props.outlet.name,
    outlet_type: props.outlet.outlet_type || '',
    address: props.outlet.address || '',
    phone: props.outlet.phone || '',
    email: props.outlet.email || '',
    logo: props.outlet.logo || '',
    image_url: props.outlet.image_url || '',
    google_map_url: props.outlet.google_map_url || '',
    url_deeplink: props.outlet.url_deeplink || '',
    status: props.outlet.status,
    schedule_mode: props.outlet.schedule_mode || '',
    schedule_days: props.outlet.schedule_days || '',
    schedule_start_time: props.outlet.schedule_start_time || '',
    schedule_end_time: props.outlet.schedule_end_time || '',
    schedule_start_date: props.outlet.schedule_start_date || '',
    schedule_end_date: props.outlet.schedule_end_date || '',
    schedule_status: props.outlet.schedule_status || '',
});

// Use shared validation composable
const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    outletSchema,
    ['name', 'outlet_type'] // Required fields
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
watch(() => [form.name, form.outlet_type], () => validateForm(getFormData()));

// Check if form is valid for submit button state
const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/outlets/${props.outlet.id}`, {
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
        title="Edit Outlet"
        description="Update outlet information"
        mode="edit"
        size="xl"
        submit-text="Save Changes"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <OutletForm v-model="form" mode="edit" :type-outlets="props.typeOutlets" />
    </ModalForm>
</template>
