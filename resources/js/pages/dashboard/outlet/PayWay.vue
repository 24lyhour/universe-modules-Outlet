<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { ModalForm } from '@/components/shared';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import {
    CreditCard,
    Key,
    ShieldCheck,
    Loader2,
    CheckCircle,
    XCircle,
    Trash2,
} from 'lucide-vue-next';
import type { Outlet } from '../../../types';

interface Props {
    outlet: Outlet;
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

const form = useForm({
    payway_merchant_id: props.outlet.payway_merchant_id || '',
    payway_api_key: '',
    payway_enabled: props.outlet.payway_enabled ?? false,
});

const isTesting = ref(false);
const testResult = ref<{ success: boolean; message: string } | null>(null);
const isRemoving = ref(false);

const hasExistingCredentials = computed(() => {
    return !!props.outlet.payway_merchant_id && props.outlet.has_payway_key;
});

const isPayWayEnabled = computed({
    get: () => form.payway_enabled,
    set: (value: boolean) => {
        form.payway_enabled = value;
    },
});

const handleSubmit = () => {
    // New setup — api_key is required
    if (!form.payway_api_key && !hasExistingCredentials.value) {
        form.setError('payway_api_key', 'API key is required');
        return;
    }

    form.put(`/dashboard/outlets/${props.outlet.uuid}/payway`, {
        onSuccess: () => {
            close();
            redirect();
        },
    });
};

const handleTest = async () => {
    isTesting.value = true;
    testResult.value = null;

    try {
        const response = await fetch(
            `/dashboard/outlets/${props.outlet.uuid}/payway/test`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':
                        document.querySelector<HTMLMetaElement>(
                            'meta[name="csrf-token"]',
                        )?.content || '',
                    Accept: 'application/json',
                },
            },
        );
        const data = await response.json();
        testResult.value = {
            success: data.success ?? response.ok,
            message: data.message || 'Connection test completed.',
        };
    } catch {
        testResult.value = {
            success: false,
            message: 'Network error. Please try again.',
        };
    } finally {
        isTesting.value = false;
    }
};

const handleRemove = () => {
    if (!confirm('Are you sure you want to remove PayWay credentials?')) return;

    isRemoving.value = true;
    router.delete(`/dashboard/outlets/${props.outlet.uuid}/payway`, {
        onSuccess: () => {
            close();
            redirect();
        },
        onFinish: () => {
            isRemoving.value = false;
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
        title="PayWay Settings"
        description="Configure ABA PayWay merchant account for this outlet"
        mode="edit"
        size="lg"
        submit-text="Save Credentials"
        :loading="form.processing"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-6">
            <!-- Enable PayWay -->
            <div
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="h-10 w-10 rounded-full overflow-hidden"
                    >
                        <img
                            src="/images/payments/aba_payway.svg"
                            alt="ABA PayWay"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div>
                        <Label class="text-base font-medium"
                            >Enable PayWay</Label
                        >
                        <p class="text-sm text-muted-foreground">
                            {{
                                isPayWayEnabled
                                    ? 'ABA PayWay is active for this outlet'
                                    : 'Turn on to accept ABA PayWay payments'
                            }}
                        </p>
                    </div>
                </div>
                <Switch v-model="isPayWayEnabled" />
            </div>

            <!-- Status Badge -->
            <div
                v-if="hasExistingCredentials"
                class="flex items-center gap-2"
            >
                <Badge
                    :variant="
                        outlet.payway_enabled ? 'default' : 'secondary'
                    "
                >
                    <ShieldCheck class="mr-1 h-3 w-3" />
                    {{
                        outlet.payway_enabled
                            ? 'Credentials configured'
                            : 'Credentials set but disabled'
                    }}
                </Badge>
            </div>

            <Separator />

            <!-- Merchant ID -->
            <div class="space-y-2">
                <Label for="payway_merchant_id" class="text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <CreditCard class="h-4 w-4 text-muted-foreground" />
                        Merchant ID
                    </div>
                </Label>
                <Input
                    id="payway_merchant_id"
                    v-model="form.payway_merchant_id"
                    placeholder="e.g. ec474815"
                    class="h-11"
                />
                <p
                    v-if="form.errors.payway_merchant_id"
                    class="text-sm text-destructive"
                >
                    {{ form.errors.payway_merchant_id }}
                </p>
                <p class="text-xs text-muted-foreground">
                    Provided by ABA Bank after merchant registration
                </p>
            </div>

            <!-- API Key -->
            <div class="space-y-2">
                <Label for="payway_api_key" class="text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <Key class="h-4 w-4 text-muted-foreground" />
                        API Key (Public Key)
                    </div>
                </Label>
                <Input
                    id="payway_api_key"
                    v-model="form.payway_api_key"
                    type="password"
                    :placeholder="
                        hasExistingCredentials
                            ? '••••••••••  (leave blank to keep current)'
                            : 'Enter API key'
                    "
                    class="h-11"
                />
                <p
                    v-if="form.errors.payway_api_key"
                    class="text-sm text-destructive"
                >
                    {{ form.errors.payway_api_key }}
                </p>
                <p class="text-xs text-muted-foreground">
                    Secret key for HMAC hash generation. Never shared publicly.
                </p>
            </div>

            <Separator />

            <!-- Test Connection -->
            <div class="space-y-3">
                <Label class="text-sm font-medium">Test Connection</Label>
                <p class="text-xs text-muted-foreground">
                    Save credentials first, then test the connection to verify
                    they are valid.
                </p>
                <Button
                    type="button"
                    variant="outline"
                    :disabled="!hasExistingCredentials || isTesting"
                    @click="handleTest"
                >
                    <Loader2
                        v-if="isTesting"
                        class="mr-2 h-4 w-4 animate-spin"
                    />
                    <ShieldCheck v-else class="mr-2 h-4 w-4" />
                    {{ isTesting ? 'Testing...' : 'Test Connection' }}
                </Button>

                <!-- Test Result -->
                <div
                    v-if="testResult"
                    class="flex items-start gap-2 rounded-lg border p-3"
                    :class="
                        testResult.success
                            ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950'
                            : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950'
                    "
                >
                    <CheckCircle
                        v-if="testResult.success"
                        class="h-4 w-4 text-green-600 mt-0.5"
                    />
                    <XCircle
                        v-else
                        class="h-4 w-4 text-red-600 mt-0.5"
                    />
                    <span
                        class="text-sm"
                        :class="
                            testResult.success
                                ? 'text-green-700 dark:text-green-300'
                                : 'text-red-700 dark:text-red-300'
                        "
                    >
                        {{ testResult.message }}
                    </span>
                </div>
            </div>

            <!-- Remove Credentials -->
            <div v-if="hasExistingCredentials">
                <Separator />
                <div class="pt-4">
                    <Button
                        type="button"
                        variant="destructive"
                        size="sm"
                        :disabled="isRemoving"
                        @click="handleRemove"
                    >
                        <Loader2
                            v-if="isRemoving"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        <Trash2 v-else class="mr-2 h-4 w-4" />
                        Remove Credentials
                    </Button>
                </div>
            </div>
        </div>
    </ModalForm>
</template>
