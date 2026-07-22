<script setup lang="ts">
import { ref, computed } from 'vue'
import draggable from 'vuedraggable'
import { useRouter, useRoute } from 'vue-router'
import { IconArrowLeft, IconSearch, IconGripVertical, IconTrash, IconDeviceFloppy } from '@tabler/icons-vue'

const router = useRouter()
const route = useRoute()

// Mock data
const albumInfo = ref({
  id: route.params.id,
  title: 'Midnight Vibes',
  type: 'Album',
  cover: 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=500&auto=format&fit=crop'
})

const unassignedTracks = ref([
  { id: 101, title: 'Lost in the City', duration: '03:45' },
  { id: 102, title: 'Neon Lights', duration: '04:12' },
  { id: 103, title: 'Cyber Pulse', duration: '02:58' },
  { id: 104, title: 'Midnight Drive', duration: '05:01' }
])

const albumTracks = ref([
  { id: 201, title: 'Intro (The Awakening)', duration: '01:30' },
  { id: 202, title: 'Synthwave Dreams', duration: '04:20' }
])

const searchQuery = ref('')

const filteredUnassigned = computed(() => {
  if (!searchQuery.value) return unassignedTracks.value
  const q = searchQuery.value.toLowerCase()
  return unassignedTracks.value.filter(t => t.title.toLowerCase().includes(q))
})

const goBack = () => router.back()

const isSaving = ref(false)
const saveChanges = () => {
  isSaving.value = true
  setTimeout(() => {
    isSaving.value = false
    // Show toast success here in real app
  }, 1000)
}

const removeTrack = (index: number, track: any) => {
  albumTracks.value.splice(index, 1)
  unassignedTracks.value.push(track)
}
</script>

<template>
  <div class="h-full flex flex-col space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <button @click="goBack" class="p-2 bg-white/5 hover:bg-white/10 rounded-lg text-gray-400 hover:text-white transition-colors border border-white/10">
          <IconArrowLeft :size="20" />
        </button>
        <div class="flex items-center space-x-4">
          <img :src="albumInfo.cover" class="w-12 h-12 rounded shadow-lg object-cover" />
          <div>
            <h1 class="text-xl font-righteous text-white flex items-center space-x-2">
              <span>{{ albumInfo.title }}</span>
              <span class="text-[10px] bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 px-2 py-0.5 rounded uppercase font-sans tracking-wide">
                {{ albumInfo.type }}
              </span>
            </h1>
            <p class="text-sm text-gray-400 font-poppins">Kéo thả để sắp xếp bài hát</p>
          </div>
        </div>
      </div>
      <button 
        @click="saveChanges"
        class="flex items-center space-x-2 bg-green-500 hover:bg-green-600 text-black px-4 py-2 rounded-lg font-semibold transition-all duration-300"
        :class="{ 'opacity-75 cursor-not-allowed': isSaving }"
      >
        <IconDeviceFloppy :size="20" />
        <span>{{ isSaving ? 'Đang lưu...' : 'Lưu thay đổi' }}</span>
      </button>
    </div>

    <!-- Split Screen Panels -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-6 min-h-[500px]">
      
      <!-- Left Panel: Unassigned Tracks -->
      <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-righteous text-white">Thư viện nhạc tự do</h2>
          <span class="text-xs text-gray-400">{{ filteredUnassigned.length }} bài</span>
        </div>
        
        <div class="relative mb-4">
          <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" :size="16" />
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Tìm kiếm bài hát..." 
            class="w-full bg-black/40 border border-gray-700 rounded-lg pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:border-green-500 transition-colors font-poppins"
          >
        </div>

        <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
          <draggable 
            v-model="unassignedTracks" 
            group="tracks" 
            item-key="id"
            class="space-y-2 min-h-[200px]"
            :animation="200"
            ghost-class="opacity-50"
          >
            <template #item="{ element }">
              <div class="flex items-center justify-between p-3 bg-gray-800/50 hover:bg-gray-700/50 border border-white/5 rounded-lg cursor-grab active:cursor-grabbing group transition-colors">
                <div class="flex items-center space-x-3">
                  <IconGripVertical :size="16" class="text-gray-600 group-hover:text-gray-400" />
                  <span class="text-gray-200 font-medium text-sm font-poppins">{{ element.title }}</span>
                </div>
                <span class="text-xs text-gray-500 font-mono">{{ element.duration }}</span>
              </div>
            </template>
          </draggable>
          
          <div v-if="filteredUnassigned.length === 0" class="text-center py-10 text-gray-500 text-sm italic">
            Không tìm thấy bài hát nào.
          </div>
        </div>
      </div>

      <!-- Right Panel: Album Tracks -->
      <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 flex flex-col relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-green-500/5 to-transparent pointer-events-none"></div>
        <div class="flex items-center justify-between mb-4 relative z-10">
          <h2 class="text-lg font-righteous text-green-400">Tracklist Album</h2>
          <span class="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded">{{ albumTracks.length }} Tracks</span>
        </div>

        <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar relative z-10">
          <draggable 
            v-model="albumTracks" 
            group="tracks" 
            item-key="id"
            class="space-y-2 min-h-[400px] pb-10"
            :animation="200"
            ghost-class="shadow-[0_0_15px_#22C55E] border-green-500 bg-gray-800 scale-[1.02]"
            drag-class="cursor-grabbing"
          >
            <template #item="{ element, index }">
              <div class="flex items-center justify-between p-3 bg-gray-900 border border-white/10 rounded-lg cursor-grab active:cursor-grabbing group hover:border-white/20 transition-all">
                <div class="flex items-center space-x-4">
                  <span class="text-gray-600 font-mono text-xs w-4 text-right">{{ index + 1 }}</span>
                  <IconGripVertical :size="16" class="text-gray-600 group-hover:text-gray-400" />
                  <span class="text-white font-medium text-sm font-poppins">{{ element.title }}</span>
                </div>
                <div class="flex items-center space-x-4">
                  <span class="text-xs text-gray-400 font-mono">{{ element.duration }}</span>
                  <button @click="removeTrack(index, element)" class="text-gray-600 hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100 p-1">
                    <IconTrash :size="16" />
                  </button>
                </div>
              </div>
            </template>
          </draggable>
          
          <div v-if="albumTracks.length === 0" class="absolute inset-0 flex items-center justify-center pointer-events-none border-2 border-dashed border-white/10 rounded-xl m-4">
            <div class="text-center">
              <p class="text-gray-400 font-poppins">Kéo thả bài hát vào đây</p>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.02);
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}
</style>
