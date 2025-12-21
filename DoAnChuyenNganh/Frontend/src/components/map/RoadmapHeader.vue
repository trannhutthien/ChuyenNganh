<template>
  <div class="px-6 py-12 text-white bg-gradient-to-r to-purple-600 from-primary">
    <div class="mx-auto max-w-4xl">
      <!-- Breadcrumb -->
      <nav v-if="showBreadcrumb" class="flex gap-2 items-center mb-6 text-sm text-white/70">
        <router-link to="/" class="hover:text-white">Trang chủ</router-link>
        <span>/</span>
        <router-link to="/lo-trinh" class="hover:text-white">Lộ trình</router-link>
        <template v-if="title">
          <span>/</span>
          <span class="text-white">{{ title }}</span>
        </template>
      </nav>

      <div class="flex gap-6 items-start">
        <!-- Icon -->
        <div 
          v-if="icon"
          class="hidden justify-center items-center w-24 h-24 text-5xl rounded-2xl md:flex bg-white/20"
        >
          {{ icon }}
        </div>

        <!-- Info -->
        <div class="flex-1">
          <!-- Level Badge -->
          <div v-if="levelText" class="flex gap-3 items-center mb-2">
            <span 
              class="px-3 py-1 text-sm font-medium rounded-full"
              :class="getLevelClass(level)"
            >
              {{ levelText }}
            </span>
          </div>

          <!-- Title -->
          <h1 class="mb-3 text-3xl font-bold md:text-4xl">{{ title }}</h1>
          
          <!-- Description -->
          <p v-if="description" class="mb-6 text-lg text-white/90">{{ description }}</p>

          <!-- Stats -->
          <div v-if="stats && stats.length > 0" class="flex flex-wrap gap-6">
            <div 
              v-for="(stat, index) in stats" 
              :key="index"
              class="flex gap-2 items-center"
            >
              <!-- Icon slot hoặc default icons -->
              <component v-if="stat.icon" :is="stat.icon" class="w-5 h-5" />
              <span v-else-if="stat.iconType === 'book'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
              </span>
              <span v-else-if="stat.iconType === 'clock'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
              </span>
              <span v-else-if="stat.iconType === 'check'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
              </span>
              <span>{{ stat.text }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  // Tiêu đề chính
  title: {
    type: String,
    required: true
  },
  // Mô tả
  description: {
    type: String,
    default: ''
  },
  // Icon emoji
  icon: {
    type: String,
    default: ''
  },
  // Cấp độ (1, 2, 3)
  level: {
    type: Number,
    default: 0
  },
  // Text cấp độ (Beginner, Intermediate, Advanced)
  levelText: {
    type: String,
    default: ''
  },
  // Hiển thị breadcrumb
  showBreadcrumb: {
    type: Boolean,
    default: true
  },
  // Mảng stats: [{ iconType: 'book'|'clock'|'check', text: '...' }]
  stats: {
    type: Array,
    default: () => []
  }
})

const getLevelClass = (level) => {
  const classes = {
    1: 'bg-green-500/20 text-green-100',
    2: 'bg-yellow-500/20 text-yellow-100',
    3: 'bg-red-500/20 text-red-100'
  }
  return classes[level] || classes[1]
}
</script>
