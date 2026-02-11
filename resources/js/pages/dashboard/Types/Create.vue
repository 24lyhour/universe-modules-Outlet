<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import TypeOutletForm from '../../../Components/Dashboard/TypeOutletForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed } from 'vue';
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

const handleSubmit = () => {
    form.post('/dashboard/outlet-types', {
        onSuccess: () => {
            close();
            redirect();
        },
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
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <TypeOutletForm v-model="form" mode="create" />
    </ModalForm>
</template>
