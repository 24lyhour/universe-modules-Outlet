<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import OutletForm from '../../../Components/Dashboard/OutletForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed } from 'vue';
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

const handleSubmit = () => {
    form.post('/dashboard/outlets', {
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
        title="Create Outlet"
        description="Add a new outlet to your business"
        mode="create"
        size="xl"
        submit-text="Create Outlet"
        :loading="form.processing"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <OutletForm v-model="form" mode="create" :type-outlets="props.typeOutlets" />
    </ModalForm>
</template>
