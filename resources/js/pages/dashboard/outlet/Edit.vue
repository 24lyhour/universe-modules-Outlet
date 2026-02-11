<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed } from 'vue';
import OutletForm from '../../../Components/Dashboard/OutletForm.vue';
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

const handleSubmit = () => {
    form.put(`/dashboard/outlets/${props.outlet.id}`, {
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
        title="Edit Outlet"
        description="Update outlet information"
        mode="edit"
        size="xl"
        submit-text="Save Changes"
        :loading="form.processing"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <OutletForm v-model="form" mode="edit" :type-outlets="props.typeOutlets" />
    </ModalForm>
</template>
