<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import TypeOutletForm from '../../../Components/Dashboard/TypeOutletForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { typeOutletSchema } from '../../../validation/typeOutletSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { TypeOutletFormData } from '../../../types';

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

const form = useForm<TypeOutletFormData>({
    name: '',
    description: '',
    status: 'active',
});

// Use shared validation composable
const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    typeOutletSchema,
    ['name'] // Required fields
);

// Get form data for validation
const getFormData = () => ({
    name: form.name,
    description: form.description || null,
    status: form.status,
});

// Watch form changes to validate in real-time
watch(() => form.name, () => {
    if (form.name) validateForm(getFormData());
});

// Check if form is valid for submit button state
const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.post('/dashboard/outlet-types', {
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
        title="Create Outlet Type"
        description="Add a new outlet type"
        mode="create"
        size="md"
        submit-text="Create Type"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <TypeOutletForm v-model="form" mode="create" />
    </ModalForm>
</template>
