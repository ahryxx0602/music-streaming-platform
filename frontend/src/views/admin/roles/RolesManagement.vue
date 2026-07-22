<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoleStore } from '@/stores/roleStore';
import RoleFormModal from '@/components/admin/features/roles/RoleFormModal.vue';
import BaseAdminButton from '@/components/admin/ui/button/BaseAdminButton.vue';
import { IconShieldCheck, IconPlus, IconEdit, IconTrash, IconUsersGroup } from '@tabler/icons-vue';

const roleStore = useRoleStore();
const isFormOpen = ref(false);
const selectedRole = ref<any>(null);

onMounted(() => {
 roleStore.fetchRoles();
 roleStore.fetchPermissions();
});

const openCreate = () => {
 selectedRole.value = null;
 isFormOpen.value = true;
};

const openEdit = (role: any) => {
 selectedRole.value = role;
 isFormOpen.value = true;
};

const handleDelete = async (role: any) => {
 if (isSystemRole(role.name)) {
 alert("Không thể xóa vai trò hệ thống!");
 return;
 }
 
 if (confirm('Bạn có chắc chắn muốn xóa vai trò này? Người dùng thuộc vai trò này sẽ bị thu hồi quyền.')) {
 await roleStore.deleteRole(role.id);
 }
};

const isSystemRole = (name: string) => {
 return ['admin', 'artist', 'user'].includes(name.toLowerCase());
};
</script>

<template>
 <div class="h-full flex flex-col p-6 max-w-[1400px] mx-auto w-full">
 <!-- Header -->
 <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold text-theme-text mb-1 flex items-center gap-2">
 <IconShieldCheck class="text-theme-primary" size="28" />
 {{ $t('admin.roles.title') }}
 </h1>
 <p class="text-sm text-theme-text-sec">
 {{ $t('admin.roles.subtitle') }}
 </p>
 </div>
 
 <BaseAdminButton variant="primary" :icon="IconPlus" @click="openCreate">
 {{ $t('admin.roles.create_new') }}
 </BaseAdminButton>
 </div>

 <!-- Grid Content -->
 <div class="flex-1 overflow-auto pb-6">
 
 <div v-if="roleStore.isLoading && roleStore.roles.length === 0" class="text-center py-12 text-theme-text-sec">
 {{ $t('common.loading') }}
 </div>
 
 <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
 
 <!-- Role Card -->
 <div 
 v-for="role in roleStore.roles" 
 :key="role.id"
 class="bg-theme-surface rounded-2xl border border-theme-border shadow-sm hover:shadow-[var(--shadow-glow)] transition-shadow flex flex-col overflow-hidden"
 >
 <!-- Card Header -->
 <div class="p-5 border-b border-theme-border flex items-start justify-between">
 <div>
 <h3 class="text-lg font-bold text-theme-text uppercase tracking-wide">{{ role.name }}</h3>
 <div class="flex items-center gap-1.5 mt-1 text-sm text-theme-text-sec font-medium">
 <IconUsersGroup size="16" class="text-theme-text-sec" />
 {{ $t('admin.roles.users_count', { count: role.users_count || 0 }) }}
 </div>
 </div>
 
 <div v-if="isSystemRole(role.name)" class="px-2 py-1 bg-theme-primary/10 text-theme-primary text-[10px] font-bold rounded uppercase tracking-wider">
 System
 </div>
 </div>
 
 <!-- Permissions Preview -->
 <div class="p-5 flex-1 bg-theme-bg/50">
 <h4 class="text-xs font-bold text-theme-text-sec uppercase mb-3">Quyền hạn ({{ role.permissions?.length || 0 }})</h4>
 <div class="flex flex-wrap gap-2">
 <span 
 v-for="(perm, idx) in (role.permissions || []).slice(0, 5)" 
 :key="perm.id"
 class="px-2.5 py-1 bg-theme-surface border border-theme-border text-theme-text text-xs font-medium rounded-md shadow-sm"
 >
 {{ perm.name }}
 </span>
 <span v-if="(role.permissions?.length || 0) > 5" class="px-2.5 py-1 bg-theme-surface-hover text-theme-text-sec text-xs font-bold rounded-md border border-dashed border-theme-border">
 +{{ role.permissions.length - 5 }}
 </span>
 
 <span v-if="!role.permissions || role.permissions.length === 0" class="text-xs text-theme-text-sec italic">
 Chưa phân quyền
 </span>
 </div>
 </div>
 
 <!-- Actions Footer -->
 <div class="p-4 border-t border-theme-border bg-theme-surface flex justify-between items-center">
 <span class="text-[11px] text-theme-text-sec font-mono">ID: {{ role.id }}</span>
 <div class="flex gap-2">
 <button 
 @click="openEdit(role)"
 class="p-2 rounded-lg transition-colors flex items-center justify-center text-theme-text-sec hover:text-theme-accent hover:bg-theme-accent/10 focus:outline-none focus:ring-2 focus:ring-theme-accent cursor-pointer"
 :class="{ 'opacity-50 !cursor-not-allowed': isSystemRole(role.name) && role.name === 'admin' }"
 :disabled="isSystemRole(role.name) && role.name === 'admin'"
 :title="$t('common.edit')"
 >
 <IconEdit size="18" />
 </button>
 <button 
 @click="handleDelete(role)"
 :disabled="isSystemRole(role.name)"
 class="p-2 rounded-lg transition-colors flex items-center justify-center text-theme-text-sec hover:text-theme-danger hover:bg-theme-danger/10 disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-theme-text-sec disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-theme-danger cursor-pointer"
 :title="$t('common.delete')"
 >
 <IconTrash size="18" />
 </button>
 </div>
 </div>
 </div>

 </div>
 </div>

 <!-- Modals -->
 <RoleFormModal 
 v-model:is-open="isFormOpen" 
 :role-data="selectedRole" 
 />
 </div>
</template>
