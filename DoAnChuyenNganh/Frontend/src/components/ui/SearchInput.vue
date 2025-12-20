<template>
  <div class="relative" :class="containerClass" ref="containerRef">
    <!-- Icon tìm kiếm -->
    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
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
          d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" 
        />
      </svg>
    </div>
    
    <!-- Input tìm kiếm -->
    <input 
      :value="modelValue"
      @input="handleInput"
      @keyup.enter="handleEnter"
      @keydown.down.prevent="navigateDown"
      @keydown.up.prevent="navigateUp"
      @keydown.escape="closeDropdown"
      @focus="handleFocus"
      type="text" 
      :placeholder="placeholder"
      :class="[
        'w-full pl-10 pr-4 rounded-xl border border-gray-300',
        'focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
        'text-sm transition-all',
        sizeClasses
      ]"
    />

    <!-- Loading spinner -->
    <div v-if="isLoading" class="absolute right-3 top-1/2 -translate-y-1/2">
      <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>

    <!-- Nút clear (hiện khi có text và không loading) -->
    <button
      v-else-if="modelValue && showClear"
      @click="handleClear"
      type="button"
      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
    >
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
          d="M6 18 18 6M6 6l12 12" 
        />
      </svg>
    </button>

    <!-- Dropdown kết quả tìm kiếm -->
    <Transition name="dropdown">
      <div 
        v-if="showDropdown && (searchResults.length > 0 || (modelValue && !isLoading))"
        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl border border-gray-200 shadow-lg z-50 overflow-hidden max-h-80 overflow-y-auto"
      >
        <!-- Có kết quả -->
        <template v-if="searchResults.length > 0">
          <div
            v-for="(item, index) in searchResults"
            :key="item.id"
            @click.stop="selectItem(item)"
            @mouseenter="highlightedIndex = index"
            class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-colors"
            :class="{ 'bg-primary/10': highlightedIndex === index, 'hover:bg-gray-50': highlightedIndex !== index }"
          >
            <!-- Thumbnail -->
            <img 
              v-if="item.thumbnail" 
              :src="item.thumbnail" 
              :alt="item.title"
              class="w-12 h-12 rounded-lg object-cover flex-shrink-0"
            />
            <div v-else class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
              <span class="text-xl">{{ item.icon || '📚' }}</span>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-800 truncate">{{ item.title }}</p>
              <p class="text-xs text-gray-500 truncate">{{ item.description }}</p>
              <div class="flex items-center gap-2 mt-1">
                <span class="text-xs text-primary font-medium">
                  {{ item.price > 0 ? formatPrice(item.price) : 'Miễn phí' }}
                </span>
                <span class="text-xs text-gray-400">•</span>
                <span class="text-xs text-gray-400">{{ item.lessons }} bài học</span>
              </div>
            </div>
          </div>
        </template>

        <!-- Không có kết quả -->
        <div v-else-if="modelValue && !isLoading" class="px-4 py-6 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto text-gray-300 mb-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
          <p class="text-sm text-gray-500">Không tìm thấy khóa học "{{ modelValue }}"</p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Tìm kiếm...'
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  containerClass: {
    type: String,
    default: ''
  },
  showClear: {
    type: Boolean,
    default: true
  },
  // Bật tính năng autocomplete
  enableAutocomplete: {
    type: Boolean,
    default: false
  },
  // Hàm search trả về Promise
  searchFunction: {
    type: Function,
    default: null
  },
  // Debounce delay (ms)
  debounce: {
    type: Number,
    default: 300
  }
})

const emit = defineEmits(['update:modelValue', 'search', 'clear', 'select'])

// Refs
const containerRef = ref(null)

// State
const isLoading = ref(false)
const showDropdown = ref(false)
const searchResults = ref([])
const highlightedIndex = ref(-1)

// Debounce timer
let debounceTimer = null

// Size classes
const sizeClasses = computed(() => {
  const sizes = {
    sm: 'h-8 text-xs',
    md: 'h-[36px] text-sm',
    lg: 'h-11 text-base'
  }
  return sizes[props.size]
})

// Format giá tiền
const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}

// Handle input với debounce
const handleInput = (event) => {
  const value = event.target.value
  emit('update:modelValue', value)
  highlightedIndex.value = -1
  
  // Clear timer cũ
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }

  // Nếu bật autocomplete và có searchFunction
  if (props.enableAutocomplete && props.searchFunction) {
    if (!value.trim()) {
      searchResults.value = []
      showDropdown.value = false
      isLoading.value = false
      return
    }

    isLoading.value = true
    showDropdown.value = true

    // Debounce search
    debounceTimer = setTimeout(async () => {
      try {
        const results = await props.searchFunction(value)
        searchResults.value = results || []
      } catch (error) {
        console.error('Search error:', error)
        searchResults.value = []
      } finally {
        isLoading.value = false
      }
    }, props.debounce)
  }
}

// Handle Enter key
const handleEnter = () => {
  if (highlightedIndex.value >= 0 && searchResults.value[highlightedIndex.value]) {
    selectItem(searchResults.value[highlightedIndex.value])
  } else {
    emit('search', props.modelValue)
    closeDropdown()
  }
}

// Handle focus
const handleFocus = () => {
  if (props.enableAutocomplete && searchResults.value.length > 0) {
    showDropdown.value = true
  }
}

// Navigate dropdown với keyboard
const navigateDown = () => {
  if (searchResults.value.length > 0) {
    highlightedIndex.value = (highlightedIndex.value + 1) % searchResults.value.length
  }
}

const navigateUp = () => {
  if (searchResults.value.length > 0) {
    highlightedIndex.value = highlightedIndex.value <= 0 
      ? searchResults.value.length - 1 
      : highlightedIndex.value - 1
  }
}

// Close dropdown
const closeDropdown = () => {
  showDropdown.value = false
  highlightedIndex.value = -1
}

// Select item
const selectItem = (item) => {
  console.log('selectItem called:', item) // Debug
  emit('select', item)
  searchResults.value = []
  showDropdown.value = false
  highlightedIndex.value = -1
}

// Clear input
const handleClear = () => {
  emit('update:modelValue', '')
  emit('clear')
  searchResults.value = []
  closeDropdown()
}

// Click outside để đóng dropdown
const handleClickOutside = (event) => {
  if (containerRef.value && !containerRef.value.contains(event.target)) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }
})
</script>

<style scoped>
/* Dropdown transition */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
