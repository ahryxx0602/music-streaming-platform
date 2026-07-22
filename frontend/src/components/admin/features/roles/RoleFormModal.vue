<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { useRoleStore } from '@/stores/roleStore';
import BaseModal from '@/components/admin/ui/modal/BaseModal.vue';
import BaseAdminInput from '@/components/admin/ui/form/BaseAdminInput.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconShieldCheck } from '@tabler/icons-vue';

const props = defineProps<{
 isOpen: boolean;
 roleData?: any;
}>();

const emit = defineEmits(['update:isOpen', 'success']);
const roleStore = useRoleStore();

const form = ref({
 id: null as number | null,
 name: '',
 permission_ids: [] as number[]
});

watch(() => props.isOpen, (newVal) => {
 if (newVal) {
 if (props.roleData) {
 form.value = {
 id: props.roleData.id,
 name: props.roleData.name,
 // Map permission_ids từ roleData
 permission_ids: props.roleData.permissions?.map((p: any) => p.id) || []
 };
 } else {
 form.value = { id: null, name: '', permission_ids: [] };
 }
 roleStore.error = null;
 
 // Đảm bảo permissions data được load
 if (roleStore.permissions.length === 0) {
 roleStore.fetchPermissions();
 }
 }
});

const isSystemRole = computed(() => {
 return ['admin', 'artist', 'user'].includes(form.value.name.toLowerCase());
});

const toggleSelectAll = () => {
 if (form.value.permission_ids.length === roleStore.permissions.length) {
 form.value.permission_ids = [];
 } else {
 form.value.permission_ids = roleStore.permissions.map(p => p.id);
 }
};

const handleSubmit = async () => {
 if (!form.value.name.trim()) return;
 
 try {
 if (form.value.id) {
 await roleStore.updateRole(form.value.id, {
 name: form.value.name,
 permission_ids: form.value.permission_ids
 });
 } else {
 await roleStore.createRole({
 name: form.value.name,
 permission_ids: form.value.permission_ids
 });
 }
 emit('success');
 emit('update:isOpen', false);
 } catch (e) {
 // Lỗi đã được store quản lý
 }
};
</script>

<template>
 <BaseModal
 :modelValue="isOpen"
 @update:modelValue="emit('update:isOpen', $event)"
 :title="form.id ? 'Sửa Vai trò' : $t('admin.roles.create_new')"
 size="lg"
 >
 <form @submit.prevent="handleSubmit" class="space-y-6">
 
 <!-- Warning nếu là System Role -->
 <div v-if="isSystemRole" class="p-3 bg-theme-secondary/20 text-theme-secondary rounded-lg text-sm font-medium border border-theme-secondary/30">
 {{ $t('admin.roles.system_role_warning') }}
 </div>

 <BaseAdminInput 
 v-model="form.name" 
 :label="$t('admin.roles.form.name_label')" 
 :placeholder="$t('admin.roles.form.name_placeholder')" 
 :disabled="isSystemRole"
 required 
 />

 <!-- Permissions Grid -->
 <div>
 <div class="flex items-center justify-between mb-3">
 <label class="block text-sm font-medium text-[var(--color-label)]">{{ $t('admin.roles.form.permissions_label') }}</label>
 <button 
 type="button" 
 @click="toggleSelectAll"
 class="text-xs font-semibold text-theme-primary hover:text-theme-accent cursor-pointer transition-colors"
 >
 {{ form.permission_ids.length === roleStore.permissions.length ? $t('admin.roles.form.deselect_all') : $t('admin.roles.form.select_all') }}
 </button>
 </div>
 
 <!-- Hiển thị Checkboxes dạng Grid 2 cột hoặc 3 cột -->
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
 <label 
 v-for="perm in roleStore.permissions" 
 :key="perm.id"
 class="flex items-start gap-3 p-3 rounded-xl border transition-colors cursor-pointer"
 :class="form.permission_ids.includes(perm.id) ? 'border-theme-primary bg-theme-primary/10' : 'border-[var(--color-input-border)] bg-[var(--color-input-bg)] hover:border-[var(--color-input-border-hover)]'"
 >
 <div class="flex items-center h-5">
 <input 
 type="checkbox" 
 v-model="form.permission_ids" 
 :value="perm.id"
 class="w-4 h-4 text-theme-primary border-theme-border rounded focus:ring-theme-primary cursor-pointer bg-[var(--color-input-bg)]"
 >
 </div>
 <div class="flex flex-col">
 <span class="text-sm font-semibold text-theme-text">{{ perm.name }}</span>
 </div>
 </label>
 </div>
 
 <div v-if="roleStore.permissions.length === 0" class="text-center py-4 text-sm text-theme-text-sec">
 {{ $t('common.loading') }}
 </div>
 </div>

 <div v-if="roleStore.error" class="p-3 bg-theme-danger/10 text-theme-danger text-sm rounded-lg font-medium">
 {{ roleStore.error }}
 </div>

 <div class="flex justify-end gap-3 pt-4 border-t border-theme-border">
 <BaseAdminButton type="button" variant="secondary" @click="emit('update:isOpen', false)">{{ $t('common.cancel') }}</BaseAdminButton>
 <BaseAdminButton type="submit" variant="primary" :loading="roleStore.isLoading" class="min-w-[120px] justify-center">
 <IconShieldCheck size="18" class="mr-2" />
 {{ $t('admin.roles.form.submit') }}
 </BaseAdminButton>
 </div>

 </form>
 </BaseModal>
</template>
