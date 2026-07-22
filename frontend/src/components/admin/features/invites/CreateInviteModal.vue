<script setup lang="ts">
import { ref, watch } from 'vue';
import { useArtistInviteStore } from '@/stores/artistInviteStore';
import BaseModal from '@/components/admin/ui/modal/BaseModal.vue';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconLink } from '@tabler/icons-vue';

const props = defineProps<{
 isOpen: boolean;
}>();

const emit = defineEmits(['update:isOpen', 'success']);
const inviteStore = useArtistInviteStore();

const form = ref({
 email: '',
 valid_days: 7
});

const isCopying = ref(false);

watch(() => props.isOpen, (newVal) => {
 if (newVal) {
 form.value = { email: '', valid_days: 7 };
 inviteStore.error = null;
 }
});

const handleSubmit = async () => {
 try {
 const registrationUrl = await inviteStore.createInvite({
 email: form.value.email || undefined,
 valid_days: form.value.valid_days
 });
 
 // Copy link
 if (registrationUrl && navigator.clipboard) {
 await navigator.clipboard.writeText(registrationUrl);
 isCopying.value = true;
 setTimeout(() => {
 isCopying.value = false;
 emit('success', registrationUrl);
 emit('update:isOpen', false);
 }, 1000);
 } else {
 emit('success', registrationUrl);
 emit('update:isOpen', false);
 }
 } catch (e) {
 // Error is handled in store
 }
};
</script>

<template>
 <BaseModal
 :modelValue="isOpen"
 @update:modelValue="emit('update:isOpen', $event)"
 :title="$t('admin.invites.create_new')"
 size="sm"
 >
 <form @submit.prevent="handleSubmit" class="space-y-5">
 
 <BaseAdminInput 
 v-model="form.email" 
 :label="$t('admin.invites.form.email_label')" 
 type="email"
 :placeholder="$t('admin.invites.form.email_placeholder')" 
 />

 <div>
 <label class="block text-sm font-medium text-theme-text-sec mb-2">{{ $t('admin.invites.form.validity_label') }}</label>
 <div class="grid grid-cols-3 gap-3">
 <!-- 1 Day -->
 <label class="cursor-pointer">
 <input type="radio" v-model="form.valid_days" :value="1" class="peer sr-only" />
 <div class="px-3 py-2 text-center text-sm rounded-lg border border-theme-border peer-checked:border-theme-primary peer-checked:bg-theme-primary/10 peer-checked:text-theme-primary font-medium text-theme-text-sec transition-colors">
 {{ $t('admin.invites.form.days_1') }}
 </div>
 </label>
 <!-- 7 Days -->
 <label class="cursor-pointer">
 <input type="radio" v-model="form.valid_days" :value="7" class="peer sr-only" />
 <div class="px-3 py-2 text-center text-sm rounded-lg border border-theme-border peer-checked:border-theme-primary peer-checked:bg-theme-primary/10 peer-checked:text-theme-primary font-medium text-theme-text-sec transition-colors">
 {{ $t('admin.invites.form.days_7') }}
 </div>
 </label>
 <!-- 30 Days -->
 <label class="cursor-pointer">
 <input type="radio" v-model="form.valid_days" :value="30" class="peer sr-only" />
 <div class="px-3 py-2 text-center text-sm rounded-lg border border-theme-border peer-checked:border-theme-primary peer-checked:bg-theme-primary/10 peer-checked:text-theme-primary font-medium text-theme-text-sec transition-colors">
 {{ $t('admin.invites.form.days_30') }}
 </div>
 </label>
 </div>
 </div>

 <div v-if="inviteStore.error" class="p-3 bg-theme-danger/10 text-theme-danger text-sm rounded-lg">
 {{ inviteStore.error }}
 </div>

 <div class="flex justify-end pt-4 border-t border-theme-border">
 <BaseAdminButton type="submit" variant="primary" :loading="inviteStore.isLoading" class="w-full justify-center">
 <IconLink size="18" class="mr-2" />
 {{ isCopying ? $t('admin.invites.messages.create_success') : $t('admin.invites.form.submit') }}
 </BaseAdminButton>
 </div>

 </form>
 </BaseModal>
</template>
