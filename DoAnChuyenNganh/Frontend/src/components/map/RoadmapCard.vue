<template>
  <router-link
    :to="`/lo-trinh/${roadmap.slug}`"
    class="overflow-hidden bg-white rounded-2xl border border-gray-100 shadow-sm transition-all duration-300 group hover:shadow-lg"
  >
    <!-- Thumbnail -->
    <div class="flex relative justify-center items-center h-40 bg-gradient-to-br from-primary/10 to-purple-500/10">
      <span class="text-6xl transition-transform duration-300 group-hover:scale-110">
        {{ roadmap.icon || '📚' }}
      </span>
      <!-- Level Badge -->
      <span
        class="absolute top-3 right-3 px-2 py-1 text-xs font-medium rounded-full"
        :class="levelClass"
      >
        {{ roadmap.levelText }}
      </span>
    </div>

    <!-- Content -->
    <div class="p-5">
      <h3 class="mb-2 text-lg font-bold text-gray-800 transition-colors group-hover:text-primary">
        {{ roadmap.title }}
      </h3>
      <p class="mb-4 text-sm text-gray-600 line-clamp-2">
        {{ roadmap.description }}
      </p>

      <!-- Stats -->
      <div class="flex gap-4 items-center text-sm text-gray-500">
        <div class="flex gap-1 items-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
          </svg>
          <span>{{ roadmap.totalCourses }} khóa học</span>
        </div>
        <div class="flex gap-1 items-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
          <span>{{ roadmap.duration || 'Đang cập nhật' }}</span>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  roadmap: {
    type: Object,
    required: true
  }
})

const levelClass = computed(() => {
  const classes = {
    1: 'bg-green-100 text-green-700',
    2: 'bg-yellow-100 text-yellow-700',
    3: 'bg-red-100 text-red-700'
  }
  return classes[props.roadmap.level] || classes[1]
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
