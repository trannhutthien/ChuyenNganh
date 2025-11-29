<template>
  <!-- Roadmap Card - Thẻ lộ trình học tập -->
  <div 
    class="bg-white rounded-lg p-4 border-2 border-gray-200 hover:border-primary transition-colors cursor-pointer"
    @click="$emit('click', roadmap.id)"
  >
    <!-- Icon -->
    <div 
      class="w-12 h-12 bg-gradient-to-br from-primary to-purple-500 rounded-lg flex items-center justify-center mb-3"
    >
      <span class="text-white text-xl">{{ roadmap.icon }}</span>
    </div>
    
    <!-- Title -->
    <h3 class="text-base font-bold text-gray-800 mb-1 line-clamp-1">
      {{ roadmap.title }}
    </h3>
    
    <!-- Description -->
    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
      {{ roadmap.description }}
    </p>
    
    <!-- Course count -->
    <div class="flex items-center gap-1 text-xs text-gray-500">
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke-width="1.5" 
        stroke="currentColor" 
        class="w-4 h-4"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" 
        />
      </svg>
      {{ roadmap.courses }} khóa
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
div:hover .w-12 {
  transform: scale(1.05);
  transition: transform 0.2s ease;
}

/* Line clamp utilities */
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
