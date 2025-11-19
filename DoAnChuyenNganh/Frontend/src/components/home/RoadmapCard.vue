<template>
  <!-- Roadmap Card - Thẻ lộ trình học tập -->
  <div 
    class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-primary transition-colors cursor-pointer"
    @click="$emit('click', roadmap.id)"
  >
    <!-- Icon -->
    <div 
      class="w-16 h-16 bg-gradient-to-br from-primary to-purple-500 rounded-xl flex items-center justify-center mb-4"
    >
      <span class="text-white text-3xl">{{ roadmap.icon }}</span>
    </div>
    
    <!-- Title -->
    <h3 class="text-xl font-bold text-gray-800 mb-2">
      {{ roadmap.title }}
    </h3>
    
    <!-- Description -->
    <p class="text-gray-600 mb-4">
      {{ roadmap.description }}
    </p>
    
    <!-- Course count -->
    <div class="flex items-center gap-2 text-sm text-gray-500">
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke-width="1.5" 
        stroke="currentColor" 
        class="w-5 h-5"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" 
        />
      </svg>
      {{ roadmap.courses }} khóa học
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  // Roadmap data
  roadmap: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.hasOwnProperty('id') && 
             value.hasOwnProperty('title') &&
             value.hasOwnProperty('icon')
    }
  }
})

// Emits
const emit = defineEmits(['click'])
</script>

<style scoped>
/* Hover animation */
div:hover .w-16 {
  transform: scale(1.05);
  transition: transform 0.2s ease;
}
</style>
