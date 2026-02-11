<script setup lang="ts">
import { ModalConfirm } from '@/components/shared';
import { router } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, ref } from 'vue';
import type { TypeOutletShowProps } from '../../../types';

const props = defineProps<TypeOutletShowProps>();

const { show, close, redirect } = useModal();

const isDeleting = ref(false);

const isOpen = computed({
    get: () => show.value,
    set: (val: boolean) => {
        if (!val) {
            close();
            redirect();
        }
    },
});

const handleDelete = () => {
    isDeleting.value = true;
    router.delete(`/dashboard/outlet-types/${props.typeOutlet.id}`, {
        onSuccess: () => {
            close();
            redirect();
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};

const handleCancel = () => {
    close();
    redirect();
};
</script>

<template>
    <ModalConfirm
        v-model:open="isOpen"
        title="Delete Outlet Type"
        :description="`Are you sure you want to delete '${typeOutlet.name}'? This action cannot be undone.`"
        confirm-text="Delete"
        variant="danger"
        :loading="isDeleting"
        @confirm="handleDelete"
        @cancel="handleCancel"
    />
</template>
