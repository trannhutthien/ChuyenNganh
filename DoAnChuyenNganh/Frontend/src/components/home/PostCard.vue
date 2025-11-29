<template>
  <!-- Post Card - Thẻ bài viết -->
  <div 
    class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow cursor-pointer"
    @click="$emit('click', post.id)"
  >
    <!-- Thumbnail -->
    <div 
      class="h-32 bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center"
    >
      <span class="text-white text-3xl">{{ post.icon || '📝' }}</span>
    </div>
    
    <!-- Content -->
    <div class="p-3">
      <!-- Category & Date -->
      <div class="flex items-center gap-1 mb-2">
        <span class="px-2 py-0.5 bg-primary/10 text-primary text-xs rounded-full font-semibold">
          {{ post.category }}
        </span>
        <span class="text-xs text-gray-500">{{ post.date }}</span>
      </div>
      
      <!-- Title -->
      <h3 class="text-sm font-bold text-gray-800 mb-1 line-clamp-2">
        {{ post.title }}
      </h3>
      
      <!-- Excerpt -->
      <p class="text-xs text-gray-600 line-clamp-2 mb-2">
        {{ post.excerpt }}
      </p>
      
      <!-- Author -->
      <div class="flex items-center gap-1 text-xs text-gray-500">
        <img 
          :src="post.author.avatar" 
          :alt="post.author.name" 
          class="w-5 h-5 rounded-full object-cover"
        >
        <span>{{ post.author.name }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  // Post data
  post: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.hasOwnProperty('id') && 
             value.hasOwnProperty('title') &&
             value.hasOwnProperty('author')
    }
  }
})

// Emits
const emit = defineEmits(['click'])
</script>

<style scoped>
/* Line clamp utilities */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
