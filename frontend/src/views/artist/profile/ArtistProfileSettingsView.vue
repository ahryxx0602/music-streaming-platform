<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { 
  IconBrandInstagram, 
  IconBrandYoutube, 
  IconBrandTwitter, 
  IconUpload, 
  IconDeviceFloppy,
  IconExclamationCircle,
  IconX
} from '@tabler/icons-vue'

const router = useRouter()

// Mock data form
const form = ref({
  stage_name: 'Neon Horizon',
  bio: 'Synthwave and electronic music producer from the future.',
  contact_email: 'booking@neonhorizon.com',
  social_instagram: 'neonhorizon',
  social_youtube: 'neonhorizonmusic',
  social_twitter: 'neonhorizon_ofc'
})

// Original state to detect changes
const originalForm = JSON.stringify(form.value)
const hasUnsavedChanges = ref(false)

watch(form, (newVal) => {
  hasUnsavedChanges.value = JSON.stringify(newVal) !== originalForm
}, { deep: true })

const avatarPreview = ref('https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=200&auto=format&fit=crop')
const bannerPreview = ref('https://images.unsplash.com/photo-1557672172-298e090bd0f1?q=80&w=1200&auto=format&fit=crop')
const errorMsg = ref('')

// Modal state
const showLeaveWarning = ref(false)
let pendingRouteLeave: any = null

const handleImageUpload = (event: Event, type: 'avatar' | 'banner') => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  errorMsg.value = ''
  
  if (file.size > 5 * 1024 * 1024) {
    errorMsg.value = `Dung lượng ${type === 'avatar' ? 'ảnh đại diện' : 'ảnh bìa'} không được vượt quá 5MB.`
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    if (type === 'avatar') {
      avatarPreview.value = e.target?.result as string
    } else {
      bannerPreview.value = e.target?.result as string
    }
    hasUnsavedChanges.value = true
  }
  reader.readAsDataURL(file)
}

const isSaving = ref(false)
const saveChanges = () => {
  if (!form.value.stage_name) {
    errorMsg.value = 'Nghệ danh không được để trống.'
    return
  }
  
  isSaving.value = true
  // Mock API call
  setTimeout(() => {
    isSaving.value = false
    hasUnsavedChanges.value = false
    // Toast success can be implemented here
  }, 1000)
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
  <div class="space-y-6 max-w-6xl mx-auto pb-10">
    <div class="flex justify-between items-end">
      <div>
        <h1 class="text-3xl font-righteous text-white">Hồ sơ Nghệ sĩ</h1>
        <p class="text-gray-400 mt-1 font-poppins text-sm">Cập nhật thông tin công khai và hình ảnh của bạn.</p>
      </div>
      <button 
        @click="saveChanges"
        :disabled="isSaving || !hasUnsavedChanges"
        class="flex items-center space-x-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300"
        :class="hasUnsavedChanges && !isSaving ? 'bg-green-500 hover:bg-green-600 text-black shadow-[0_0_15px_rgba(34,197,94,0.4)]' : 'bg-white/10 text-gray-500 cursor-not-allowed'"
      >
        <IconDeviceFloppy :size="20" />
        <span>{{ isSaving ? 'Đang lưu...' : 'Lưu thay đổi' }}</span>
      </button>
    </div>

    <!-- Layout 2 cột -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Cột Trái: Upload Hình ảnh -->
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 flex flex-col items-center">
          <h2 class="text-lg font-righteous text-white self-start mb-4">Hình ảnh</h2>
          
          <!-- Avatar -->
          <div class="relative group cursor-pointer mb-8">
            <div class="w-32 h-32 rounded-full overflow-hidden border-2 border-white/20 group-hover:border-green-500 transition-colors shadow-lg">
              <img :src="avatarPreview" class="w-full h-full object-cover" />
            </div>
            <div class="absolute inset-0 bg-black/60 rounded-full flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
              <IconUpload class="text-white mb-1" :size="24" />
              <span class="text-xs text-white font-medium">Thay Avatar</span>
            </div>
            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" @change="e => handleImageUpload(e, 'avatar')" />
          </div>

          <!-- Banner -->
          <div class="w-full relative group cursor-pointer rounded-xl overflow-hidden border-2 border-white/20 hover:border-green-500 transition-colors h-32">
            <img :src="bannerPreview" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
              <IconUpload class="text-white mb-1" :size="24" />
              <span class="text-xs text-white font-medium">Thay ảnh bìa (Banner)</span>
            </div>
            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" @change="e => handleImageUpload(e, 'banner')" />
          </div>
          <p class="text-xs text-gray-500 mt-2 text-center w-full">Hỗ trợ JPG, PNG. Tối đa 5MB.</p>
          <p v-if="errorMsg" class="text-red-400 text-xs font-medium mt-2">{{ errorMsg }}</p>
        </div>
      </div>

      <!-- Cột Phải: Form Thông tin -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 md:p-8 space-y-6">
          <h2 class="text-lg font-righteous text-white border-b border-white/10 pb-2">Thông tin Cơ bản</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-300 font-poppins">Nghệ danh (Stage Name) <span class="text-red-500">*</span></label>
              <input v-model="form.stage_name" type="text" class="w-full bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-green-500 transition-colors font-poppins">
            </div>
            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-300 font-poppins">Email Liên hệ (Booking)</label>
              <input v-model="form.contact_email" type="email" class="w-full bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-green-500 transition-colors font-poppins">
            </div>
          </div>
          
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-300 font-poppins">Tiểu sử (Bio)</label>
            <textarea v-model="form.bio" rows="4" class="w-full bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-green-500 transition-colors font-poppins resize-none"></textarea>
          </div>
        </div>

        <!-- Social Links -->
        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 md:p-8 space-y-6">
          <h2 class="text-lg font-righteous text-white border-b border-white/10 pb-2">Liên kết Mạng xã hội</h2>
          
          <div class="space-y-4">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg text-white">
                <IconBrandInstagram :size="20" />
              </div>
              <div class="flex-1 relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-poppins text-sm">instagram.com/</span>
                <input v-model="form.social_instagram" type="text" class="w-full bg-black/40 border border-gray-700 rounded-lg pl-32 pr-4 py-2 text-white focus:outline-none focus:border-pink-500 transition-colors font-poppins text-sm">
              </div>
            </div>

            <div class="flex items-center space-x-3">
              <div class="p-2 bg-red-600 rounded-lg text-white">
                <IconBrandYoutube :size="20" />
              </div>
              <div class="flex-1 relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-poppins text-sm">youtube.com/@</span>
                <input v-model="form.social_youtube" type="text" class="w-full bg-black/40 border border-gray-700 rounded-lg pl-28 pr-4 py-2 text-white focus:outline-none focus:border-red-500 transition-colors font-poppins text-sm">
              </div>
            </div>

            <div class="flex items-center space-x-3">
              <div class="p-2 bg-blue-500 rounded-lg text-white">
                <IconBrandTwitter :size="20" />
              </div>
              <div class="flex-1 relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-poppins text-sm">x.com/</span>
                <input v-model="form.social_twitter" type="text" class="w-full bg-black/40 border border-gray-700 rounded-lg pl-16 pr-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors font-poppins text-sm">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Warning Modal -->
    <div v-if="showLeaveWarning" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
      <div class="bg-gray-900 border border-white/10 rounded-2xl p-6 max-w-md w-full shadow-2xl relative">
        <button @click="cancelLeave" class="absolute top-4 right-4 text-gray-400 hover:text-white">
          <IconX :size="20" />
        </button>
        <div class="flex items-center space-x-4 mb-6 text-yellow-500">
          <IconExclamationCircle :size="32" />
          <h3 class="text-xl font-righteous text-white">Bạn chưa lưu thay đổi!</h3>
        </div>
        <p class="text-gray-300 font-poppins text-sm mb-8">Bạn có chắc chắn muốn rời khỏi trang này? Tất cả những thay đổi vừa nhập sẽ bị mất.</p>
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
