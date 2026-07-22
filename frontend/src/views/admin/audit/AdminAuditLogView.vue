<script setup lang="ts">
import { ref } from 'vue'
import { 
  IconHistory, 
  IconSearch, 
  IconFilter, 
  IconEye,
  IconX
} from '@tabler/icons-vue'

// Mock Data cho Bảng
const logs = ref([
  {
    id: 101,
    user: 'Admin Nguyen (ID: 1)',
    action: 'CREATE',
    target: 'Song (ID: 45)',
    ip: '192.168.1.5',
    timestamp: '2026-07-22 14:30:00'
  },
  {
    id: 102,
    user: 'Moderator Le (ID: 4)',
    action: 'UPDATE',
    target: 'Album (ID: 12)',
    ip: '14.232.12.99',
    timestamp: '2026-07-22 15:45:12'
  },
  {
    id: 103,
    user: 'Admin Nguyen (ID: 1)',
    action: 'DELETE',
    target: 'User (ID: 89)',
    ip: '192.168.1.5',
    timestamp: '2026-07-22 16:10:05'
  }
])

const getActionColor = (action: string) => {
  switch(action) {
    case 'CREATE': return 'bg-green-500/20 text-green-400 border-green-500/30'
    case 'UPDATE': return 'bg-blue-500/20 text-blue-400 border-blue-500/30'
    case 'DELETE': return 'bg-red-500/20 text-red-400 border-red-500/30'
    default: return 'bg-gray-500/20 text-gray-400 border-gray-500/30'
  }
}

// Modal State
const showDetailModal = ref(false)
const selectedLog = ref<any>(null)

// Dữ liệu JSON Mock cho Modal (UPDATE Diff)
const mockOldValues = {
  "title": "Bản tình ca buồn",
  "status": "draft",
  "is_premium": false,
  "genre_id": 4
}

const mockNewValues = {
  "title": "Bản tình ca buồn (Remix)",
  "status": "published",
  "is_premium": true,
  "genre_id": 4
}

const openDetail = (log: any) => {
  selectedLog.value = log
  showDetailModal.value = true
}

const closeModal = () => {
  showDetailModal.value = false
  selectedLog.value = null
}
</script>

<template>
  <div class="h-full flex flex-col space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-end">
      <div>
        <h1 class="text-3xl font-righteous text-white flex items-center gap-3">
          <IconHistory :size="32" class="text-indigo-400" />
          Nhật ký Hệ thống (Audit Logs)
        </h1>
        <p class="text-gray-400 mt-2 font-poppins text-sm">Theo dõi mọi thay đổi dữ liệu trong hệ thống.</p>
      </div>
    </div>

    <!-- Toolbar: Search & Filter -->
    <div class="flex flex-col sm:flex-row gap-4 bg-white/5 backdrop-blur-md border border-white/10 p-4 rounded-xl">
      <div class="flex-1 relative">
        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" :size="20" />
        <input 
          type="text" 
          placeholder="Tìm kiếm theo người dùng, đối tượng..." 
          class="w-full bg-black/40 border border-gray-700 rounded-lg pl-10 pr-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 transition-colors font-poppins text-sm"
        >
      </div>
      <div class="flex space-x-3">
        <select class="bg-black/40 border border-gray-700 rounded-lg px-4 py-2.5 text-gray-300 focus:outline-none focus:border-indigo-500 font-poppins text-sm">
          <option value="">Tất cả Hành động</option>
          <option value="CREATE">CREATE</option>
          <option value="UPDATE">UPDATE</option>
          <option value="DELETE">DELETE</option>
        </select>
        <button class="flex items-center space-x-2 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 px-4 py-2.5 rounded-lg transition-colors font-medium">
          <IconFilter :size="20" />
          <span>Lọc thêm</span>
        </button>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl overflow-hidden flex-1">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-black/40 text-gray-400 font-poppins text-sm border-b border-white/10">
              <th class="p-4 font-medium uppercase tracking-wider">ID</th>
              <th class="p-4 font-medium uppercase tracking-wider">Thời gian</th>
              <th class="p-4 font-medium uppercase tracking-wider">Người thực hiện</th>
              <th class="p-4 font-medium uppercase tracking-wider">Hành động</th>
              <th class="p-4 font-medium uppercase tracking-wider">Đối tượng (Target)</th>
              <th class="p-4 font-medium uppercase tracking-wider">IP Address</th>
              <th class="p-4 font-medium uppercase tracking-wider text-right">Chi tiết</th>
            </tr>
          </thead>
          <tbody class="text-sm font-poppins text-gray-200">
            <tr 
              v-for="log in logs" 
              :key="log.id" 
              class="border-b border-white/5 hover:bg-white/5 transition-colors"
            >
              <td class="p-4 text-gray-500 font-mono">#{{ log.id }}</td>
              <td class="p-4 text-gray-400 font-mono text-xs">{{ log.timestamp }}</td>
              <td class="p-4">{{ log.user }}</td>
              <td class="p-4">
                <span 
                  class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider border rounded-md"
                  :class="getActionColor(log.action)"
                >
                  {{ log.action }}
                </span>
              </td>
              <td class="p-4 font-medium text-indigo-300">{{ log.target }}</td>
              <td class="p-4 text-gray-500 font-mono text-xs">{{ log.ip }}</td>
              <td class="p-4 text-right">
                <button 
                  @click="openDetail(log)"
                  class="p-2 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 rounded-lg transition-colors"
                >
                  <IconEye :size="18" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Detail Modal (JSON Diff) -->
    <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
      <div class="bg-gray-900 border border-white/10 rounded-2xl w-full max-w-4xl shadow-2xl flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-white/10">
          <h2 class="text-xl font-righteous text-white flex items-center space-x-3">
            <span>Chi tiết Log #{{ selectedLog?.id }}</span>
            <span 
              v-if="selectedLog"
              class="px-2 py-0.5 text-xs font-bold uppercase tracking-wider border rounded-md"
              :class="getActionColor(selectedLog.action)"
            >
              {{ selectedLog.action }}
            </span>
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
            <IconX :size="24" />
          </button>
        </div>
        
        <!-- Modal Body (JSON Diff View) -->
        <div class="p-6 overflow-y-auto font-poppins space-y-6 flex-1">
          <div class="grid grid-cols-2 gap-4 text-sm bg-black/40 p-4 rounded-xl border border-white/5">
            <div><span class="text-gray-500">Người thực hiện:</span> <span class="text-white ml-2">{{ selectedLog?.user }}</span></div>
            <div><span class="text-gray-500">Đối tượng:</span> <span class="text-indigo-400 ml-2 font-medium">{{ selectedLog?.target }}</span></div>
            <div><span class="text-gray-500">Thời gian:</span> <span class="text-white ml-2">{{ selectedLog?.timestamp }}</span></div>
            <div><span class="text-gray-500">IP Address:</span> <span class="text-white ml-2">{{ selectedLog?.ip }}</span></div>
          </div>

          <div v-if="selectedLog?.action === 'UPDATE'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Old Values -->
            <div class="space-y-2">
              <h3 class="text-red-400 font-medium text-sm px-2 flex items-center">
                <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Dữ liệu cũ (Old Values)
              </h3>
              <div class="bg-[#1e1111] border border-red-900/50 rounded-xl p-4 font-mono text-sm overflow-x-auto text-red-200/80 leading-relaxed shadow-inner">
<pre><code>{
  "title": "<span class="bg-red-900/40 px-1 rounded text-red-300">Bản tình ca buồn</span>",
  "status": "<span class="bg-red-900/40 px-1 rounded text-red-300">draft</span>",
  "is_premium": <span class="bg-red-900/40 px-1 rounded text-red-300">false</span>,
  "genre_id": 4
}</code></pre>
              </div>
            </div>

            <!-- New Values -->
            <div class="space-y-2">
              <h3 class="text-green-400 font-medium text-sm px-2 flex items-center">
                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> Dữ liệu mới (New Values)
              </h3>
              <div class="bg-[#111e15] border border-green-900/50 rounded-xl p-4 font-mono text-sm overflow-x-auto text-green-200/80 leading-relaxed shadow-inner">
<pre><code>{
  "title": "<span class="bg-green-900/40 px-1 rounded text-green-300">Bản tình ca buồn (Remix)</span>",
  "status": "<span class="bg-green-900/40 px-1 rounded text-green-300">published</span>",
  "is_premium": <span class="bg-green-900/40 px-1 rounded text-green-300">true</span>,
  "genre_id": 4
}</code></pre>
              </div>
            </div>
          </div>

          <!-- Cho trường hợp CREATE/DELETE -->
          <div v-else class="space-y-2">
            <h3 class="text-gray-300 font-medium text-sm px-2">Dữ liệu Payload</h3>
            <div class="bg-black/60 border border-white/10 rounded-xl p-4 font-mono text-sm overflow-x-auto text-gray-300 shadow-inner">
<pre><code>{
  "name": "Admin Nguyen",
  "role_id": 1
}</code></pre>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-5 border-t border-white/10 flex justify-end bg-black/40 rounded-b-2xl">
          <button @click="closeModal" class="px-5 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors font-medium text-sm">
            Đóng cửa sổ
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
