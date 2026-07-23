<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '@/services/api'
import { 
  IconSettings, 
  IconDeviceFloppy,
  IconExclamationCircle,
  IconX,
  IconUpload,
  IconPhotoOff
} from '@tabler/icons-vue'

const router = useRouter()
const toast = useToast()

// Tabs definition
const tabs = [
  { id: 'general', name: 'Cài đặt Chung' },
  { id: 'finance', name: 'Tài chính & Hoa hồng' },
  { id: 'system', name: 'Hệ thống' }
]
const activeTab = ref('general')

const isLoadingData = ref(true)

// Mock Settings Data (will be overwritten by API)
const form = ref({
  // General
  site_name: '',
  site_description: '',
  support_email: '',
  // Finance
  artist_revenue_share: 70, // mapped to backend key artist_revenue_share
  min_withdrawal_amount: 50, 
  // System
  enable_registration: true,
  enable_uploads: true,
  maintenance_mode: false
})

let originalForm = JSON.stringify(form.value)
const hasUnsavedChanges = ref(false)

watch(form, (newVal) => {
  if (isLoadingData.value) return
  hasUnsavedChanges.value = JSON.stringify(newVal) !== originalForm
}, { deep: true })

const logoPreview = ref('https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=200&auto=format&fit=crop')
const errorMsg = ref('')

const logoFile = ref<File | null>(null)

const handleLogoUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  errorMsg.value = ''
  if (file.size > 2 * 1024 * 1024) {
    errorMsg.value = 'Dung lượng Logo không được vượt quá 2MB.'
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    logoPreview.value = e.target?.result as string
    logoFile.value = file
    hasUnsavedChanges.value = true
  }
  reader.readAsDataURL(file)
}

onMounted(async () => {
  try {
    const { data } = await api.get('/v1/admin/settings')
    if (data.success && data.data) {
      const settings = data.data
      
      // Chuyển đổi dữ liệu từ dạng mảng [{key: value}, ...] hoặc object
      // Giả sử backend trả về data object format: { site_name: "Music", artist_revenue_share: "70", maintenance_mode: "0", ... }
      form.value.site_name = settings.site_name || ''
      form.value.site_description = settings.site_description || ''
      form.value.support_email = settings.support_email || ''
      
      form.value.artist_revenue_share = Number(settings.artist_revenue_share || 70)
      form.value.min_withdrawal_amount = Number(settings.min_withdrawal_amount || 50)
      
      form.value.enable_registration = settings.enable_registration == '1' || settings.enable_registration === 'true' || settings.enable_registration === true
      form.value.enable_uploads = settings.enable_uploads == '1' || settings.enable_uploads === 'true' || settings.enable_uploads === true
      form.value.maintenance_mode = settings.maintenance_mode == '1' || settings.maintenance_mode === 'true' || settings.maintenance_mode === true

      originalForm = JSON.stringify(form.value)
    }
  } catch (error) {
    toast.error('Lỗi khi tải cấu hình hệ thống')
  } finally {
    isLoadingData.value = false
  }
})

// Modal State
const showLeaveWarning = ref(false)
let pendingRouteLeave: any = null

const isSaving = ref(false)
const saveChanges = async () => {
  if (!form.value.site_name) {
    errorMsg.value = 'Tên Website không được để trống.'
    return
  }
  
  isSaving.value = true
  try {
    const payload = {
      settings: {
        site_name: form.value.site_name,
        site_description: form.value.site_description,
        support_email: form.value.support_email,
        artist_revenue_share: form.value.artist_revenue_share,
        min_withdrawal_amount: form.value.min_withdrawal_amount,
        enable_registration: form.value.enable_registration,
        enable_uploads: form.value.enable_uploads,
        maintenance_mode: form.value.maintenance_mode
      }
    }

    await api.put('/v1/admin/settings', payload)
    
    originalForm = JSON.stringify(form.value)
    hasUnsavedChanges.value = false
    toast.success('Lưu cài đặt thành công!')
  } catch (error) {
    toast.error('Có lỗi xảy ra khi lưu cài đặt.')
  } finally {
    isSaving.value = false
  }
}

// Router Guard
onBeforeRouteLeave((to, from, next) => {
  if (hasUnsavedChanges.value) {
    showLeaveWarning.value = true
    pendingRouteLeave = next
  } else {
    next()
  }
})

const confirmLeave = () => {
  showLeaveWarning.value = false
  if (pendingRouteLeave) pendingRouteLeave()
}

const cancelLeave = () => {
  showLeaveWarning.value = false
  if (pendingRouteLeave) pendingRouteLeave(false)
  pendingRouteLeave = null
}
</script>

<template>
  <div class="h-full flex flex-col space-y-6 max-w-6xl mx-auto pb-10">
    <!-- Header -->
    <div class="flex justify-between items-end">
      <div>
        <h1 class="text-3xl font-righteous text-white flex items-center gap-3">
          <IconSettings :size="32" class="text-indigo-400" />
          Cài đặt Hệ thống
        </h1>
        <p class="text-gray-400 mt-2 font-poppins text-sm">Cấu hình tham số cốt lõi của toàn bộ nền tảng.</p>
      </div>
      <button 
        @click="saveChanges"
        :disabled="isSaving || !hasUnsavedChanges"
        class="flex items-center space-x-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300"
        :class="hasUnsavedChanges && !isSaving ? 'bg-indigo-500 hover:bg-indigo-600 text-white shadow-[0_0_15px_rgba(99,102,241,0.4)]' : 'bg-white/10 text-gray-500 cursor-not-allowed'"
      >
        <IconDeviceFloppy :size="20" />
        <span>{{ isSaving ? 'Đang lưu...' : 'Lưu cài đặt' }}</span>
      </button>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex space-x-2 bg-white/5 backdrop-blur-md p-1.5 rounded-xl border border-white/10 w-fit">
      <button 
        v-for="tab in tabs" 
        :key="tab.id"
        @click="activeTab = tab.id"
        class="px-6 py-2 rounded-lg font-medium text-sm transition-all font-poppins"
        :class="activeTab === tab.id ? 'bg-indigo-500 text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-white/5'"
      >
        {{ tab.name }}
      </button>
    </div>

    <!-- Tab Content -->
    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 md:p-8 flex-1">
      
      <!-- Tab: General -->
      <div v-show="activeTab === 'general'" class="space-y-8 animate-fade-in">
        <h2 class="text-xl font-righteous text-white border-b border-white/10 pb-3">Cài đặt Cơ bản</h2>
        
        <div class="flex flex-col md:flex-row gap-8">
          <!-- Logo Upload -->
          <div class="space-y-4">
            <label class="block text-sm font-medium text-gray-300 font-poppins">Logo Nền tảng</label>
            <div class="relative group cursor-pointer w-40 h-40">
              <div class="w-full h-full rounded-2xl overflow-hidden border-2 border-dashed border-gray-600 group-hover:border-indigo-500 transition-colors shadow-lg bg-black/40 flex items-center justify-center">
                <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-cover" />
                <IconUpload v-else class="text-gray-500" :size="32" />
              </div>
              <div class="absolute inset-0 bg-black/60 rounded-2xl flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <IconUpload class="text-white mb-1" :size="24" />
                <span class="text-xs text-white font-medium">Thay Logo</span>
              </div>
              <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" @change="handleLogoUpload" />
            </div>
            <p v-if="errorMsg" class="text-red-400 text-xs font-medium">{{ errorMsg }}</p>
          </div>

          <!-- Form Info -->
          <div class="flex-1 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-300 font-poppins">Tên Website</label>
                <input v-model="form.site_name" type="text" class="w-full bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 transition-colors font-poppins">
              </div>
              <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-300 font-poppins">Email Hỗ trợ (Support)</label>
                <input v-model="form.support_email" type="email" class="w-full bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 transition-colors font-poppins">
              </div>
            </div>
            
            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-300 font-poppins">Mô tả SEO (Meta Description)</label>
              <textarea v-model="form.site_description" rows="3" class="w-full bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 transition-colors font-poppins resize-none"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab: Finance -->
      <div v-show="activeTab === 'finance'" class="space-y-8 animate-fade-in">
        <h2 class="text-xl font-righteous text-white border-b border-white/10 pb-3">Cấu hình Tài chính</h2>
        
        <div class="max-w-xl space-y-8">
          <!-- Commission Range -->
          <div class="space-y-4">
            <div class="flex justify-between">
              <label class="block text-sm font-medium text-gray-300 font-poppins">Tỷ lệ Hoa hồng Nghệ sĩ (%)</label>
              <span class="text-indigo-400 font-bold font-mono">{{ form.artist_revenue_share }}%</span>
            </div>
            <input 
              v-model.number="form.artist_revenue_share" 
              type="range" 
              min="0" max="100" step="1"
              class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-indigo-500"
            >
            <p class="text-xs text-gray-500 font-poppins">Tỷ lệ phần trăm doanh thu từ lượt nghe sẽ được trả cho Nghệ sĩ (Platform giữ phần còn lại).</p>
          </div>

          <!-- Min Withdrawal -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-300 font-poppins">Hạn mức rút tiền tối thiểu ($)</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
              <input 
                v-model.number="form.min_withdrawal_amount" 
                type="number" 
                min="0"
                class="w-full bg-black/40 border border-gray-700 rounded-lg pl-8 pr-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 transition-colors font-poppins"
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Tab: System -->
      <div v-show="activeTab === 'system'" class="space-y-8 animate-fade-in">
        <h2 class="text-xl font-righteous text-white border-b border-white/10 pb-3">Tính năng Cốt lõi</h2>
        
        <div class="max-w-xl space-y-6">
          <!-- Switch 1 -->
          <div class="flex items-center justify-between p-4 bg-black/20 border border-white/5 rounded-xl hover:bg-black/40 transition-colors">
            <div>
              <h4 class="text-white font-medium font-poppins">Cho phép Đăng ký</h4>
              <p class="text-sm text-gray-500 font-poppins mt-1">Mở cửa cho người dùng và nghệ sĩ mới tạo tài khoản.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="form.enable_registration" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
            </label>
          </div>
          
          <!-- Switch 2 -->
          <div class="flex items-center justify-between p-4 bg-black/20 border border-white/5 rounded-xl hover:bg-black/40 transition-colors">
            <div>
              <h4 class="text-white font-medium font-poppins">Cho phép Upload Nhạc</h4>
              <p class="text-sm text-gray-500 font-poppins mt-1">Nghệ sĩ có thể tải lên các bản thu mới.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="form.enable_uploads" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
            </label>
          </div>

          <!-- Switch 3 (Danger) -->
          <div class="flex items-center justify-between p-4 bg-red-500/5 border border-red-500/20 rounded-xl hover:bg-red-500/10 transition-colors">
            <div>
              <h4 class="text-red-400 font-medium font-poppins">Chế độ Bảo trì (Maintenance Mode)</h4>
              <p class="text-sm text-red-500/70 font-poppins mt-1">Đóng băng hệ thống, chỉ Admin được truy cập.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="form.maintenance_mode" class="sr-only peer">
              <div class="w-11 h-6 bg-red-950 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
            </label>
          </div>
        </div>
      </div>

    </div>

    <!-- Warning Modal (Guard) -->
    <div v-if="showLeaveWarning" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
      <div class="bg-gray-900 border border-white/10 rounded-2xl p-6 max-w-md w-full shadow-2xl relative">
        <button @click="cancelLeave" class="absolute top-4 right-4 text-gray-400 hover:text-white">
          <IconX :size="20" />
        </button>
        <div class="flex items-center space-x-4 mb-6 text-yellow-500">
          <IconExclamationCircle :size="32" />
          <h3 class="text-xl font-righteous text-white">Bạn chưa lưu thay đổi!</h3>
        </div>
        <p class="text-gray-300 font-poppins text-sm mb-8">Bạn có chắc chắn muốn rời khỏi trang này? Tất cả các cấu hình vừa chỉnh sửa sẽ bị mất.</p>
        <div class="flex justify-end space-x-3">
          <button @click="cancelLeave" class="px-4 py-2 rounded-lg text-white bg-white/10 hover:bg-white/20 font-medium transition-colors">
            Hủy, ở lại
          </button>
          <button @click="confirmLeave" class="px-4 py-2 rounded-lg text-white bg-red-500 hover:bg-red-600 font-medium transition-colors shadow-lg shadow-red-500/20">
            Rời đi
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
