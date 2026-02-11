<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import TypeOutletForm from '../../../Components/Dashboard/TypeOutletForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed } from 'vue';
import type { TypeOutletFormData, TypeOutletEditProps } from '../../../types';

const props = defineProps<TypeOutletEditProps>();

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
    name: props.typeOutlet.name,
    description: props.typeOutlet.description || '',
    status: props.typeOutlet.status,
});

const handleSubmit = () => {
    form.put(`/dashboard/outlet-types/${props.typeOutlet.id}`, {
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
        title="Edit Outlet Type"
        description="Update outlet type information"
        mode="edit"
        size="md"
        submit-text="Save Changes"
        :loading="form.processing"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <TypeOutletForm v-model="form" mode="edit" />
    </ModalForm>
</template>
