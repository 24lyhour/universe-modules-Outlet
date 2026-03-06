<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { ModalForm } from '@/components/shared';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { AlertTriangle, Store } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

interface OutletItem {
    id: number;
    uuid: string;
    name: string;
}

interface Props {
    outletItems: OutletItem[];
}

const props = defineProps<Props>();
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

const confirmDelete = ref(false);

const form = useForm({
    uuids: props.outletItems.map((o) => o.uuid),
});

watch(confirmDelete, () => {
    form.clearErrors();
});

const canSubmit = computed(() => confirmDelete.value === true);

const handleSubmit = () => {
    form.delete('/dashboard/outlets/bulk-delete', {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(`${props.outletItems.length} outlet(s) deleted successfully.`);
            setTimeout(() => {
                close();
                redirect();
            }, 100);
        },
        onError: (errors) => {
            if (errors.uuids) {
                toast.error(errors.uuids);
            }
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
        :title="`Delete ${outletItems.length} Outlet${outletItems.length > 1 ? 's' : ''}`"
        description="This action will move the selected outlets to trash"
        mode="delete"
        size="md"
        :submit-text="`Delete ${outletItems.length} Outlet${outletItems.length > 1 ? 's' : ''}`"
        :loading="form.processing"
        :disabled="!canSubmit"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-6">
            <!-- Outlet List -->
            <div class="space-y-2">
                <p class="text-sm font-medium text-muted-foreground">
                    The following outlets will be deleted:
                </p>
                <div class="max-h-48 space-y-2 overflow-y-auto rounded-lg border p-3">
                    <div
                        v-for="outlet in outletItems"
                        :key="outlet.uuid"
                        class="flex items-center gap-3 rounded-md bg-muted/30 p-2"
                    >
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                            <Store class="h-4 w-4 text-primary" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ outlet.name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning Banner -->
            <div class="flex items-start gap-3 rounded-lg border border-destructive/50 bg-destructive/10 p-4">
                <AlertTriangle class="mt-0.5 h-5 w-5 shrink-0 text-destructive" />
                <div class="space-y-1">
                    <p class="text-sm font-medium text-destructive">
                        You are about to delete {{ outletItems.length }} outlet{{ outletItems.length > 1 ? 's' : '' }}
                    </p>
                    <p class="text-sm text-muted-foreground">
                        Items will be moved to trash and can be restored within 30 days.
                    </p>
                </div>
            </div>

            <!-- Confirmation Checkbox -->
            <div class="flex items-start space-x-3 rounded-lg border p-4">
                <Checkbox
                    id="bulk-confirmed"
                    :model-value="confirmDelete"
                    @update:model-value="(val: boolean | 'indeterminate') => confirmDelete = val === true"
                />
                <div class="space-y-1">
                    <Label for="bulk-confirmed" class="cursor-pointer font-medium">
                        I confirm this bulk deletion
                    </Label>
                    <p class="text-sm text-muted-foreground">
                        I understand that {{ outletItems.length }} outlet{{ outletItems.length > 1 ? 's' : '' }} will be deleted.
                    </p>
                </div>
            </div>

            <p v-if="form.errors.uuids" class="text-sm text-destructive">
                {{ form.errors.uuids }}
            </p>
        </div>
    </ModalForm>
</template>
