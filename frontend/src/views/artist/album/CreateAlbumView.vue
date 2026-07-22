<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { IconArrowLeft, IconUpload, IconPhotoOff, IconCheck } from '@tabler/icons-vue'

const router = useRouter()

const form = ref({
  title: '',
  type: 'Album',
  releaseDate: '',
  description: '',
  isPublic: true
})

const coverFile = ref<File | null>(null)
const coverPreview = ref<string | null>(null)
const errorMsg = ref('')
const isSubmitting = ref(false)
const showToast = ref(false)

const goBack = () => router.back()

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return
  
  validateAndSetFile(file)
}

const handleDrop = (event: DragEvent) => {
  const file = event.dataTransfer?.files?.[0]
  if (!file) return
  validateAndSetFile(file)
}

const validateAndSetFile = (file: File) => {
  errorMsg.value = ''
  
  // Validation
  const validTypes = ['image/jpeg', 'image/png', 'image/webp']
  if (!validTypes.includes(file.type)) {
    errorMsg.value = 'Chỉ hỗ trợ định dạng JPG, PNG hoặc WEBP.'
    return
  }
  
  if (file.size > 5 * 1024 * 1024) {
    errorMsg.value = 'Dung lượng ảnh phải dưới 5MB.'
    return
  }
  
  coverFile.value = file
  
  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    coverPreview.value = e.target?.result as string
  }
  reader.readAsDataURL(file)
}

const removeCover = () => {
  coverFile.value = null
  coverPreview.value = null
  errorMsg.value = ''
}

const submitForm = () => {
  if (!form.value.title || !coverFile.value) {
    errorMsg.value = 'Vui lòng nhập tên Album và chọn Ảnh bìa.'
    return
  }
  
  isSubmitting.value = true
  
  // Mock API call
  setTimeout(() => {
    isSubmitting.value = false
    showToast.value = true
    setTimeout(() => {
      router.push('/artist/albums')
    }, 1500)
  }, 1000)
}
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
      <button @click="goBack" class="p-2 bg-white/5 hover:bg-white/10 rounded-lg text-gray-400 hover:text-white transition-colors border border-white/10">
        <IconArrowLeft :size="20" />
      </button>
      <div>
        <h1 class="text-3xl font-righteous text-white">Tạo Album / EP Mới</h1>
        <p class="text-gray-400 mt-1 font-poppins text-sm">Điền thông tin và đăng tải ấn phẩm âm nhạc của bạn.</p>
      </div>
    </div>

    <!-- Form Content -->
    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 md:p-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        
        <!-- Left: Cover Upload -->
        <div class="space-y-4">
          <label class="block text-sm font-medium text-gray-300 font-poppins">Ảnh bìa (Cover Image) <span class="text-red-500">*</span></label>
          
          <div 
            class="aspect-square w-full relative border-2 border-dashed rounded-xl flex flex-col items-center justify-center overflow-hidden transition-all duration-300 bg-black/20"
            :class="[
              coverPreview ? 'border-green-500' : 'border-gray-600 hover:border-gray-400',
              {'border-red-500 bg-red-500/5': errorMsg && !coverPreview}
            ]"
            @dragover.prevent
            @drop.prevent="handleDrop"
          >
            <!-- Preview -->
            <img v-if="coverPreview" :src="coverPreview" class="absolute inset-0 w-full h-full object-cover" />
            <div v-if="coverPreview" class="absolute inset-0 bg-black/50 opacity-0 hover:opacity-100 flex items-center justify-center transition-opacity duration-300">
              <button @click.prevent="removeCover" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center space-x-2">
                <IconPhotoOff :size="16" />
                <span>Xóa ảnh</span>
              </button>
            </div>
            
            <!-- Upload Prompt -->
            <div v-else class="text-center p-6">
              <div class="bg-gray-800 p-4 rounded-full inline-block mb-4">
                <IconUpload :size="32" class="text-gray-400" />
              </div>
              <p class="text-gray-300 font-medium mb-1">Kéo thả ảnh vào đây</p>
              <p class="text-gray-500 text-xs mb-4">Hỗ trợ JPG, PNG (Tối đa 5MB)</p>
              <label class="cursor-pointer bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Chọn file từ máy
                <input type="file" class="hidden" accept="image/jpeg, image/png, image/webp" @change="handleFileUpload" />
              </label>
            </div>
          </div>
          
          <p v-if="errorMsg" class="text-red-400 text-sm font-medium">{{ errorMsg }}</p>
        </div>

        <!-- Right: Details Form -->
        <div class="space-y-5 flex flex-col">
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-300 font-poppins">Tên Album / EP <span class="text-red-500">*</span></label>
            <input v-model="form.title" type="text" placeholder="Ví dụ: Neon Dreams" class="w-full bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-green-500 transition-colors font-poppins">
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-300 font-poppins">Phân loại</label>
              <select v-model="form.type" class="w-full bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-green-500 transition-colors font-poppins appearance-none">
                <option value="Album">Album</option>
                <option value="EP">EP (Extended Play)</option>
                <option value="Single">Single</option>
              </select>
            </div>
            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-300 font-poppins">Ngày phát hành</label>
              <input v-model="form.releaseDate" type="date" class="w-full bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-green-500 transition-colors font-poppins color-scheme-dark">
            </div>
          </div>
          
          <div class="space-y-1 flex-1">
            <label class="block text-sm font-medium text-gray-300 font-poppins">Mô tả (Tùy chọn)</label>
            <textarea v-model="form.description" rows="4" placeholder="Nguồn cảm hứng, lời cảm ơn..." class="w-full h-full bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-green-500 transition-colors font-poppins resize-none"></textarea>
          </div>
          
          <div class="pt-4 mt-auto">
            <button 
              @click="submitForm"
              :disabled="isSubmitting"
              class="w-full flex items-center justify-center space-x-2 bg-green-500 hover:bg-green-600 disabled:bg-green-500/50 disabled:cursor-not-allowed text-black px-4 py-3 rounded-lg font-bold text-lg transition-all shadow-[0_0_15px_rgba(34,197,94,0.3)]"
            >
              <span v-if="!isSubmitting">Tạo Album</span>
              <span v-else class="animate-pulse">Đang xử lý...</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Toast Success (Simplistic) -->
    <div v-if="showToast" class="fixed bottom-10 right-10 bg-gray-900 border border-green-500/50 shadow-[0_0_20px_rgba(34,197,94,0.3)] rounded-lg p-4 flex items-center space-x-3 transform animate-bounce">
      <div class="bg-green-500/20 text-green-400 p-2 rounded-full">
        <IconCheck :size="20" />
      </div>
      <div>
        <p class="text-white font-medium">Thành công!</p>
        <p class="text-gray-400 text-sm">Đã tạo Album mới.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
input[type="date"]::-webkit-calendar-picker-indicator {
  filter: invert(1);
}
</style>
