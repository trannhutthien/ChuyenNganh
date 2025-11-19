<template>
  <!-- Video container -->
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke-width="1.5" 
        stroke="currentColor" 
        class="w-5 h-5 text-primary"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" 
        />
      </svg>
      {{ title }}
    </h3>

    <!-- Video player -->
    <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden relative">
      <!-- Video element nếu có videoUrl thực -->
      <video 
        v-if="isRealVideo"
        ref="videoPlayer"
        :src="videoUrl"
        controls
        class="w-full h-full object-cover"
        :poster="posterUrl"
        @loadedmetadata="onVideoLoaded"
        @error="onVideoError"
      >
        Trình duyệt của bạn không hỗ trợ video tag.
      </video>

      <!-- Placeholder khi chưa có video thực hoặc đang load -->
      <div 
        v-else 
        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200"
      >
        <div class="text-center">
          <!-- Icon video -->
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke-width="1.5" 
            stroke="currentColor" 
            class="w-16 h-16 text-gray-400 mx-auto mb-2"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" 
            />
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112Z" 
            />
          </svg>
          
          <p class="text-gray-500 text-sm">{{ placeholderText }}</p>
          
          <!-- Error message nếu có -->
          <p v-if="hasError" class="text-red-500 text-xs mt-2">
            Không thể tải video. Vui lòng thử lại sau.
          </p>
        </div>
      </div>

      <!-- Loading overlay -->
      <div 
        v-if="isLoading" 
        class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center"
      >
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto"></div>
          <p class="text-white text-sm mt-2">Đang tải video...</p>
        </div>
      </div>
    </div>

    <!-- Video info (optional) -->
    <div v-if="showInfo && (duration || fileSize)" class="mt-3 flex items-center gap-4 text-sm text-gray-500">
      <span v-if="duration" class="flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        {{ duration }}
      </span>
      <span v-if="fileSize" class="flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        {{ fileSize }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Props
const props = defineProps({
  // URL video
  videoUrl: {
    type: String,
    required: true
  },
  // Tiêu đề video
  title: {
    type: String,
    default: 'Video bài học'
  },
  // URL poster (thumbnail)
  posterUrl: {
    type: String,
    default: ''
  },
  // Thời lượng video (text)
  duration: {
    type: String,
    default: ''
  },
  // Kích thước file
  fileSize: {
    type: String,
    default: ''
  },
  // Hiển thị thông tin video
  showInfo: {
    type: Boolean,
    default: false
  },
  // Text placeholder
  placeholderText: {
    type: String,
    default: 'Video sẽ được cập nhật sớm'
  }
})

// Refs
const videoPlayer = ref(null)
const isLoading = ref(false)
const hasError = ref(false)

// Computed
const isRealVideo = computed(() => {
  // Kiểm tra xem có phải video thật không (có extension .mp4, .webm, .ogg, etc.)
  const videoExtensions = ['.mp4', '.webm', '.ogg', '.mov', '.avi', '.mkv', 'm3u8']
  return videoExtensions.some(ext => props.videoUrl.toLowerCase().includes(ext))
})

// Methods
const onVideoLoaded = () => {
  isLoading.value = false
  hasError.value = false
}

const onVideoError = (error) => {
  isLoading.value = false
  hasError.value = true
  console.error('Video load error:', error)
}

// Emits (nếu cần tracking)
const emit = defineEmits([
  'video-loaded',
  'video-error',
  'video-play',
  'video-pause'
])
</script>

<style scoped>
/* Aspect ratio 16:9 cho video */
.aspect-video {
  aspect-ratio: 16 / 9;
}

/* Video player styling */
video {
  display: block;
  max-width: 100%;
  height: 100%;
}

/* Custom controls (nếu cần) */
video::-webkit-media-controls-panel {
  background-color: rgba(0, 0, 0, 0.8);
}

/* Loading spinner animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Hover effect cho placeholder */
.bg-gradient-to-br {
  transition: all 0.3s ease;
}

.bg-gradient-to-br:hover {
  background: linear-gradient(to bottom right, #e5e7eb, #d1d5db);
}
</style>
