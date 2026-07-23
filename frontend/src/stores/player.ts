import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import api from '@/services/api'

export interface Song {
  id: number
  title: string
  artist: string
  cover_image: string
  audio_url: string
}

export const usePlayerStore = defineStore('player', () => {
  // HTML5 Audio API (chỉ khởi tạo trên client)
  const audio = typeof window !== 'undefined' ? new Audio() : null
  
  // State
  const currentSong = ref<Song | null>(null)
  const isPlaying = ref(false)
  const queue = ref<Song[]>([])
  
  const currentTime = ref(0)
  const duration = ref(0)
  const volume = ref(1) // 0 to 1
  const isMuted = ref(false)
  const hasTrackedPlay = ref(false) // Track if the current song has been recorded as a "play"
  
  // Lắng nghe sự kiện audio
  if (audio) {
    audio.addEventListener('timeupdate', () => {
      currentTime.value = audio.currentTime
      
      // Auto-track stream count after 30 seconds
      if (currentTime.value >= 30 && !hasTrackedPlay.value && currentSong.value) {
        hasTrackedPlay.value = true
        api.post(`/v1/listener/songs/${currentSong.value.id}/track-play`).catch(e => console.error("Error tracking play:", e))
      }
    })
    
    audio.addEventListener('loadedmetadata', () => {
      duration.value = audio.duration
    })
    
    audio.addEventListener('ended', () => {
      next()
    })
    
    audio.addEventListener('play', () => {
      isPlaying.value = true
    })
    
    audio.addEventListener('pause', () => {
      isPlaying.value = false
    })
  }
  
  // Actions
  const playSong = (song: Song) => {
    if (!audio) return
    
    currentSong.value = song
    hasTrackedPlay.value = false // Reset tracking flag for new song
    
    audio.src = song.audio_url
    audio.load()
    audio.play().catch(e => console.error("Error playing audio:", e))
    isPlaying.value = true
  }

  const fetchAndPlaySong = async (id: number) => {
    try {
      const { data } = await api.get(`/v1/listener/songs/${id}`)
      if (data.success) {
        const songData = data.data
        const mappedSong: Song = {
          id: songData.id,
          title: songData.title,
          artist: songData.artist_name || (songData.artist ? songData.artist.stage_name : 'Unknown Artist'),
          cover_image: songData.cover_url || "https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=200&auto=format&fit=crop",
          audio_url: songData.audio_url
        }
        playSong(mappedSong)
      }
    } catch (e) {
      console.error("Error fetching song details:", e)
    }
  }
  
  const togglePlay = () => {
    if (!audio || !currentSong.value) return
    
    if (isPlaying.value) {
      audio.pause()
    } else {
      audio.play().catch(e => console.error("Error playing audio:", e))
    }
  }


  
  const next = () => {
    // Tạm thời mockup chức năng next. Trong thực tế sẽ lấy từ queue
    console.log("Play next song in queue")
    isPlaying.value = false
  }
  
  const prev = () => {
    // Tạm thời mockup prev
    console.log("Play prev song")
    if (audio) {
      audio.currentTime = 0
    }
  }
  
  const seek = (time: number) => {
    if (!audio) return
    audio.currentTime = time
    currentTime.value = time
  }
  
  const setVolume = (val: number) => {
    if (!audio) return
    volume.value = val
    audio.volume = val
    if (val > 0) isMuted.value = false
  }
  
  const toggleMute = () => {
    if (!audio) return
    isMuted.value = !isMuted.value
    audio.muted = isMuted.value
  }

  // Chạy Mockup 1 bài hát lúc khởi động để test dễ dàng
  const loadMockSong = () => {
    const mock: Song = {
      id: 1,
      title: "Midnight Vibes",
      artist: "Neon Horizon",
      cover_image: "https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?q=80&w=200&auto=format&fit=crop",
      audio_url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"
    }
    currentSong.value = mock
    if (audio) {
      audio.src = mock.audio_url
      audio.load()
    }
  }
  
  return {
    currentSong,
    isPlaying,
    queue,
    currentTime,
    duration,
    volume,
    isMuted,
    playSong,
    togglePlay,
    next,
    prev,
    seek,
    setVolume,
    toggleMute,
    loadMockSong,
    fetchAndPlaySong
  }
})
