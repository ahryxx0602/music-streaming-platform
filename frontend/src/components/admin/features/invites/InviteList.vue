<script setup lang="ts">
import { useArtistInviteStore } from '@/stores/artistInviteStore';
import { IconCopy, IconTrash, IconCheck } from '@tabler/icons-vue';
import { ref } from 'vue';

const inviteStore = useArtistInviteStore();

const copiedId = ref<number | null>(null);

const copyLink = async (invite: any) => {
 if (!navigator.clipboard) return;
 // Dựng URL registration: frontend_url + /register/artist?token=...
 // Thường Backend sẽ trả về `registration_url`, hoặc ta tự ghép từ `token`
 const url = invite.registration_url || `${window.location.origin}/register/artist?token=${invite.token}`;
 
 try {
 await navigator.clipboard.writeText(url);
 copiedId.value = invite.id;
 setTimeout(() => {
 copiedId.value = null;
 }, 2000);
 } catch (err) {
 console.error('Failed to copy: ', err);
 }
};

const handleRevoke = async (invite: any) => {
 if (confirm('Bạn có chắc chắn muốn thu hồi và xóa mã mời này? Hành động này không thể hoàn tác.')) {
 await inviteStore.revokeInvite(invite.id);
 }
};

const formatDate = (dateString: string) => {
 if (!dateString) return '';
 return new Date(dateString).toLocaleString('vi-VN', {
 day: '2-digit', month: '2-digit', year: 'numeric',
 hour: '2-digit', minute: '2-digit'
 });
};
</script>

<template>
 <div class="bg-theme-surface rounded-xl shadow-[var(--shadow-glow)] border border-theme-border overflow-hidden flex flex-col h-full">
 <div class="flex-1 overflow-auto">
 <table class="w-full text-left border-collapse min-w-[1000px]">
 <thead>
 <tr class="bg-theme-surface-hover border-b border-theme-border sticky top-0 z-10">
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">{{ $t('admin.invites.table.email') }}</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider w-80">{{ $t('admin.invites.table.link') }}</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">{{ $t('admin.invites.table.creator') }}</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">{{ $t('admin.invites.table.expires_at') }}</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider">{{ $t('admin.invites.table.status') }}</th>
 <th class="px-4 py-3 text-xs font-semibold text-theme-text-sec uppercase tracking-wider text-right">{{ $t('admin.invites.table.actions') }}</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-theme-border">
 <tr v-if="inviteStore.isLoading && inviteStore.invites.length === 0">
 <td colspan="6" class="px-4 py-8 text-center text-theme-text-sec">Đang tải dữ liệu...</td>
 </tr>
 <tr v-else-if="inviteStore.invites.length === 0">
 <td colspan="6" class="px-4 py-16 text-center text-theme-text-sec">
 Không có mã mời nào được tìm thấy.
 </td>
 </tr>
 
 <tr v-for="invite in inviteStore.invites" :key="invite.id" class="hover:bg-theme-surface-hover transition-colors">
 <!-- Email -->
 <td class="px-4 py-3">
 <span v-if="invite.email" class="text-sm font-medium text-theme-text">{{ invite.email }}</span>
 <span v-else class="text-sm italic text-theme-text-sec">N/A</span>
 </td>
 
 <!-- Link -->
 <td class="px-4 py-3">
 <div class="flex items-center gap-2">
 <div class="flex-1 font-mono text-xs bg-theme-surface-hover text-theme-text-sec px-2 py-1.5 rounded truncate border border-theme-border">
 {{ invite.registration_url || invite.token }}
 </div>
 <button 
 @click="copyLink(invite)"
 class="p-1.5 rounded text-theme-text-sec hover:text-theme-primary hover:bg-theme-primary/10 transition-colors shrink-0"
 :title="copiedId === invite.id ? 'Đã copy' : 'Copy link'"
 >
 <IconCheck v-if="copiedId === invite.id" size="18" class="text-theme-success" />
 <IconCopy v-else size="18" />
 </button>
 </div>
 </td>

 <!-- Creator -->
 <td class="px-4 py-3 text-sm text-theme-text-sec">
 {{ invite.created_by?.name || 'Hệ thống' }}
 </td>

 <!-- Expires -->
 <td class="px-4 py-3 text-sm text-theme-text-sec">
 {{ formatDate(invite.expires_at) }}
 </td>

 <!-- Status -->
 <td class="px-4 py-3">
 <span v-if="invite.status === 'Used' || invite.used_at" class="inline-flex px-2 py-1 bg-theme-primary/15 text-theme-primary text-xs font-bold rounded-full">
 {{ $t('admin.invites.status.used') }}
 </span>
 <span v-else-if="invite.status === 'Expired' || new Date(invite.expires_at) < new Date()" class="inline-flex px-2 py-1 bg-theme-danger/15 text-theme-danger text-xs font-bold rounded-full">
 {{ $t('admin.invites.status.expired') }}
 </span>
 <span v-else class="inline-flex px-2 py-1 bg-theme-accent/15 text-theme-accent text-xs font-bold rounded-full">
 {{ $t('admin.invites.status.valid') }}
 </span>
 </td>

 <!-- Actions -->
 <td class="px-4 py-3 text-right">
 <button 
 v-if="!(invite.status === 'Used' || invite.used_at)"
 @click="handleRevoke(invite)" 
 class="p-1.5 text-theme-text-sec hover:text-theme-danger bg-theme-surface-hover hover:bg-theme-danger/10 rounded transition-colors"
 title="Thu hồi mã"
 >
 <IconTrash size="18" />
 </button>
 </td>
 </tr>
 </tbody>
 </table>
 </div>

 <!-- Pagination -->
 <div class="p-3 border-t border-theme-border flex items-center justify-between text-sm text-theme-text-sec bg-theme-surface-hover shrink-0">
 <div>Hiển thị <span class="font-bold text-theme-text">{{ inviteStore.invites.length }}</span> mã mời</div>
 <div class="flex items-center gap-1">
 <button 
 :disabled="inviteStore.meta.current_page === 1"
 @click="inviteStore.fetchInvites(inviteStore.meta.current_page - 1)"
 class="px-3 py-1.5 border border-theme-border rounded-lg hover:bg-theme-surface disabled:opacity-50 transition-colors"
 >Trước</button>
 <span class="px-3">Trang {{ inviteStore.meta.current_page }} / {{ inviteStore.meta.last_page }}</span>
 <button 
 :disabled="inviteStore.meta.current_page === inviteStore.meta.last_page"
 @click="inviteStore.fetchInvites(inviteStore.meta.current_page + 1)"
 class="px-3 py-1.5 border border-theme-border rounded-lg hover:bg-theme-surface disabled:opacity-50 transition-colors"
 >Sau</button>
 </div>
 </div>
 </div>
</template>
