<template>
  <!-- Section Header với Title, Badge, Description và Link "Xem tất cả" -->
  <div class="flex items-center justify-between mb-8">
    <!-- Left: Title + Badge + Description -->
    <div>
      <div class="flex items-center gap-3 mb-2">
        <h2 class="text-3xl font-bold text-gray-800">{{ title }}</h2>
        
        <!-- Badge (optional) -->
        <span 
          v-if="badge"
          class="px-3 py-1 text-sm font-semibold rounded-full"
          :class="badgeClass"
        >
          {{ badge }}
        </span>
      </div>
      
      <!-- Description -->
      <p v-if="description" class="text-gray-600">{{ description }}</p>
    </div>

    <!-- Right: View All Link -->
    <a 
      v-if="showViewAll"
      :href="viewAllLink" 
      class="text-primary hover:text-primary-600 font-semibold flex items-center gap-2 transition-colors"
      @click.prevent="$emit('view-all')"
    >
      {{ viewAllText }}
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke-width="2" 
        stroke="currentColor" 
        class="w-5 h-5"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          d="m8.25 4.5 7.5 7.5-7.5 7.5" 
        />
      </svg>
    </a>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  // Tiêu đề chính
  title: {
    type: String,
    required: true
  },
  // Mô tả phụ
  description: {
    type: String,
    default: ''
  },
  // Nội dung badge (vd: "⭐ Premium", "🎁 Miễn phí")
  badge: {
    type: String,
    default: ''
  },
  // Kiểu badge: 'premium', 'free', hoặc custom class
  badgeType: {
    type: String,
    default: 'premium',
    validator: (value) => ['premium', 'free', 'custom'].includes(value)
  },
  // Custom badge class (nếu badgeType = 'custom')
  customBadgeClass: {
    type: String,
    default: ''
  },
  // Hiển thị link "Xem tất cả"
  showViewAll: {
    type: Boolean,
    default: true
  },
  // Text của link
  viewAllText: {
    type: String,
    default: 'Xem tất cả'
  },
  // URL của link (nếu không dùng event)
  viewAllLink: {
    type: String,
    default: '#'
  }
})

// Emits
const emit = defineEmits(['view-all'])

// Computed badge class
const badgeClass = computed(() => {
  if (props.badgeType === 'custom') {
    return props.customBadgeClass
  }
  
  const classes = {
    premium: 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white',
    free: 'bg-green-500 text-white'
  }
  
  return classes[props.badgeType] || classes.premium
})
</script>

<style scoped>
/* Animation for arrow icon */
a:hover svg {
  transform: translateX(4px);
  transition: transform 0.2s ease;
}
</style>
