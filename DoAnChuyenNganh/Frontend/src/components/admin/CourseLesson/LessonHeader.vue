<template>
  <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
    <div class="flex items-start gap-4">
      <img 
        :src="thumbnail || 'https://via.placeholder.com/120x80'" 
        :alt="title"
        class="w-24 h-16 object-cover rounded-lg"
      />
      <div class="flex-1">
        <h1 class="text-2xl font-bold text-gray-800">{{ title }}</h1>
        <p class="text-gray-600 mt-1">{{ subtitle }}</p>
        <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
          <span class="flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
            </svg>
            {{ lessonCount }} bài học
          </span>
          <span class="flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ totalDuration }} phút
          </span>
          <!-- Extra slot for additional info -->
          <slot name="extra"></slot>
        </div>
      </div>
      <!-- Actions slot -->
      <slot name="actions"></slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: 'Quản lý bài học của khóa học này'
  },
  thumbnail: {
    type: String,
    default: ''
  },
  lessonCount: {
    type: Number,
    default: 0
  },
  totalDuration: {
    type: Number,
    default: 0
  }
})
</script>
