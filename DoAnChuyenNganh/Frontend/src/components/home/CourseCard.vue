<template>
  <!-- Course Card - Thẻ khóa học -->
  <div 
    class="bg-white rounded-xl overflow-hidden cursor-pointer group transition-all"
    :class="cardClass"
    @click="$emit('click', course.id)"
  >
    <!-- Thumbnail -->
    <div 
      class="h-48 flex items-center justify-center relative overflow-hidden"
      :class="thumbnailClass"
    >
      <span 
        class="text-white text-4xl font-bold group-hover:scale-110 transition-transform"
      >
        {{ course.icon }}
      </span>
      
      <!-- Badge (PRO/FREE) -->
      <div 
        class="absolute top-3 right-3 px-2 py-1 text-xs font-bold rounded-md"
        :class="badgeClass"
      >
        {{ badgeText }}
      </div>
    </div>
    
    <!-- Content -->
    <div class="p-4">
      <!-- Title -->
      <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">
        {{ course.title }}
      </h3>
      
      <!-- Description -->
      <p class="text-sm text-gray-600 mb-3 line-clamp-2">
        {{ course.description }}
      </p>
      
      <!-- Stats & Rating -->
      <div class="flex items-center justify-between mb-3">
        <!-- Students count -->
        <span class="text-xs text-gray-500 flex items-center gap-1">
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
              d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" 
            />
          </svg>
          {{ course.students }} học viên
        </span>
        
        <!-- Rating stars -->
        <div class="flex items-center gap-1">
          <svg 
            v-for="i in 5" 
            :key="i" 
            xmlns="http://www.w3.org/2000/svg" 
            viewBox="0 0 24 24" 
            fill="currentColor" 
            class="w-4 h-4 text-yellow-400"
          >
            <path 
              fill-rule="evenodd" 
              d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" 
              clip-rule="evenodd" 
            />
          </svg>
        </div>
      </div>
      
      <!-- Price Section -->
      <div class="pt-3 border-t border-gray-200">
        <slot name="price">
          <!-- Default price display -->
          <div class="flex items-center justify-between">
            <!-- Free course -->
            <span 
              v-if="!course.price || course.price === 0" 
              class="text-lg font-bold text-green-600"
            >
              Miễn phí
            </span>
            
            <!-- Paid course -->
            <div v-else>
              <!-- With discount -->
              <div v-if="course.originalPrice" class="flex flex-col">
                <span class="text-xs text-gray-400 line-through">
                  {{ formatPrice(course.originalPrice) }}
                </span>
                <span class="text-lg font-bold text-primary">
                  {{ formatPrice(course.price) }}
                </span>
              </div>
              <!-- Without discount -->
              <span v-else class="text-lg font-bold text-primary">
                {{ formatPrice(course.price) }}
              </span>
            </div>
            
            <!-- Discount badge -->
            <span 
              v-if="course.originalPrice && course.price" 
              class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded"
            >
              -{{ calculateDiscount(course.originalPrice, course.price) }}%
            </span>
          </div>
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  // Course data
  course: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.hasOwnProperty('id') && 
             value.hasOwnProperty('title')
    }
  },
  // Card type: 'pro' hoặc 'free'
  type: {
    type: String,
    default: 'free',
    validator: (value) => ['pro', 'free'].includes(value)
  }
})

// Emits
const emit = defineEmits(['click'])

// Computed classes
const cardClass = computed(() => {
  return props.type === 'pro' 
    ? 'border-2 border-gray-200 hover:shadow-xl hover:border-primary' 
    : 'border border-gray-200 hover:shadow-lg'
})

const thumbnailClass = computed(() => {
  return props.type === 'pro'
    ? 'bg-gradient-to-br from-blue-400 to-purple-500'
    : 'bg-gradient-to-br from-green-400 to-blue-500'
})

const badgeClass = computed(() => {
  return props.type === 'pro'
    ? 'bg-yellow-400 text-yellow-900'
    : 'bg-green-500 text-white'
})

const badgeText = computed(() => {
  return props.type === 'pro' ? 'PRO' : 'FREE'
})

// Helper functions
const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}

const calculateDiscount = (originalPrice, currentPrice) => {
  return Math.round(((originalPrice - currentPrice) / originalPrice) * 100)
}
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
