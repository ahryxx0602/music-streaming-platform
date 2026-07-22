<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { IconPlus, IconDisc, IconMusic, IconCalendar } from '@tabler/icons-vue'

const router = useRouter()

// Mock data
const albums = ref([
  {
    id: 1,
    title: 'Midnight Vibes',
    type: 'Album',
    trackCount: 12,
    releaseDate: '2026-10-15',
    cover: 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=500&auto=format&fit=crop'
  },
  {
    id: 2,
    title: 'Neon Dreams',
    type: 'EP',
    trackCount: 5,
    releaseDate: '2026-11-20',
    cover: 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?q=80&w=500&auto=format&fit=crop'
  }
])
// set to true to test empty state
const isEmpty = ref(false)

const navigateToCreate = () => {
  router.push('/artist/albums/create')
}

const navigateToDetail = (id: number) => {
  router.push(`/artist/albums/${id}`)
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-righteous text-white">Quản lý Album & EP</h1>
        <p class="text-gray-400 mt-1 font-poppins text-sm">Danh sách đĩa nhạc và ấn phẩm của bạn.</p>
      </div>
      <button 
        @click="navigateToCreate"
        class="flex items-center space-x-2 bg-green-500 hover:bg-green-600 text-black px-4 py-2 rounded-lg font-semibold transition-all duration-300 shadow-[0_0_15px_rgba(34,197,94,0.4)] hover:shadow-[0_0_25px_rgba(34,197,94,0.6)]"
      >
        <IconPlus :size="20" />
        <span>Tạo Album mới</span>
      </button>
    </div>

    <!-- Empty State -->
    <div v-if="isEmpty || albums.length === 0" class="border-2 border-dashed border-gray-600 rounded-2xl flex flex-col items-center justify-center py-24 bg-white/5 backdrop-blur-md">
      <div class="bg-gray-800 p-4 rounded-full mb-4">
        <IconDisc :size="48" class="text-gray-400" />
      </div>
      <h3 class="text-xl font-righteous text-white mb-2">Chưa có Album nào</h3>
      <p class="text-gray-400 mb-6 font-poppins text-sm text-center max-w-md">
        Bạn chưa phát hành hoặc tạo bất kỳ Album/EP nào. Hãy bắt đầu gom nhóm các bản nhạc tuyệt vời của bạn lại nhé!
      </p>
      <button 
        @click="navigateToCreate"
        class="bg-white/10 hover:bg-white/20 border border-white/20 text-white px-6 py-2 rounded-lg font-medium transition-colors"
      >
        Tạo kiệt tác mới
      </button>
    </div>

    <!-- Grid View -->
    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
      <div 
        v-for="album in albums" 
        :key="album.id"
        @click="navigateToDetail(album.id)"
        class="group relative bg-white/5 backdrop-blur-md border border-white/10 rounded-xl overflow-hidden cursor-pointer hover:-translate-y-1 hover:border-white/20 transition-all duration-300 shadow-lg"
      >
        <div class="aspect-square w-full overflow-hidden relative">
          <img 
            :src="album.cover" 
            :alt="album.title"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
          >
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
            <span class="text-white font-medium text-sm">Chỉnh sửa</span>
          </div>
          <!-- Type Badge -->
          <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-sm border border-white/20 text-xs px-2 py-1 rounded text-white font-medium uppercase tracking-wider">
            {{ album.type }}
          </div>
        </div>
        <div class="p-4">
          <h3 class="font-righteous text-white text-lg truncate mb-2" :title="album.title">{{ album.title }}</h3>
          <div class="flex items-center text-gray-400 text-xs font-poppins space-x-3">
            <span class="flex items-center space-x-1">
              <IconMusic :size="14" />
              <span>{{ album.trackCount }} bài</span>
            </span>
            <span class="flex items-center space-x-1">
              <IconCalendar :size="14" />
              <span>{{ new Date(album.releaseDate).getFullYear() }}</span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
