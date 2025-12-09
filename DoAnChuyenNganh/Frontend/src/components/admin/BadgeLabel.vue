<template>
  <span 
    class="px-3 py-1 rounded-full text-sm font-medium inline-block"
    :class="badgeClass"
  >
    {{ badgeText }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Giá trị key (vd: 'active', 'frontend', 'pending', ...)
  value: {
    type: [String, Number],
    required: true
  },
  // Loại badge: 'status', 'category', hoặc 'level'
  type: {
    type: String,
    default: 'status',
    validator: (val) => ['status', 'category', 'level'].includes(val)
  },
  // Text hiển thị tùy chỉnh (override default text)
  text: {
    type: String,
    default: ''
  },
  // Custom config (optional) - override toàn bộ config mặc định
  config: {
    type: Object,
    default: null
  }
})

// ============ STATUS CONFIG ============
const statusConfig = {
  // Số (từ database)
  '1': {
    class: 'bg-green-100 text-green-700',
    text: 'Hoạt động'
  },
  '2': {
    class: 'bg-yellow-100 text-yellow-700',
    text: 'Chờ duyệt'
  },
  '0': {
    class: 'bg-gray-100 text-gray-700',
    text: 'Nháp'
  },
  // String
  active: {
    class: 'bg-green-100 text-green-700',
    text: 'Hoạt động'
  },
  pending: {
    class: 'bg-yellow-100 text-yellow-700',
    text: 'Chờ duyệt'
  },
  draft: {
    class: 'bg-gray-100 text-gray-700',
    text: 'Nháp'
  },
  inactive: {
    class: 'bg-red-100 text-red-700',
    text: 'Ngừng hoạt động'
  },
  completed: {
    class: 'bg-blue-100 text-blue-700',
    text: 'Hoàn thành'
  },
  cancelled: {
    class: 'bg-red-100 text-red-700',
    text: 'Đã hủy'
  },
  published: {
    class: 'bg-green-100 text-green-700',
    text: 'Đã xuất bản'
  },
  archived: {
    class: 'bg-gray-100 text-gray-700',
    text: 'Đã lưu trữ'
  }
}

// ============ CATEGORY CONFIG ============
const categoryConfig = {
  frontend: {
    class: 'bg-blue-100 text-blue-700',
    text: 'Frontend'
  },
  backend: {
    class: 'bg-green-100 text-green-700',
    text: 'Backend'
  },
  mobile: {
    class: 'bg-purple-100 text-purple-700',
    text: 'Mobile'
  },
  devops: {
    class: 'bg-orange-100 text-orange-700',
    text: 'DevOps'
  },
  database: {
    class: 'bg-yellow-100 text-yellow-700',
    text: 'Database'
  },
  design: {
    class: 'bg-pink-100 text-pink-700',
    text: 'Design'
  },
  ai: {
    class: 'bg-indigo-100 text-indigo-700',
    text: 'AI/ML'
  },
  security: {
    class: 'bg-red-100 text-red-700',
    text: 'Security'
  },
  testing: {
    class: 'bg-teal-100 text-teal-700',
    text: 'Testing'
  },
  other: {
    class: 'bg-gray-100 text-gray-700',
    text: 'Khác'
  }
}

// ============ LEVEL CONFIG ============
const levelConfig = {
  // Số (từ database)
  '1': {
    class: 'bg-green-100 text-green-700',
    text: 'Cơ bản'
  },
  '2': {
    class: 'bg-yellow-100 text-yellow-700',
    text: 'Trung bình'
  },
  '3': {
    class: 'bg-red-100 text-red-700',
    text: 'Nâng cao'
  },
  // Tiếng Việt
  'cơ bản': {
    class: 'bg-green-100 text-green-700',
    text: 'Cơ bản'
  },
  'trung bình': {
    class: 'bg-yellow-100 text-yellow-700',
    text: 'Trung bình'
  },
  'nâng cao': {
    class: 'bg-red-100 text-red-700',
    text: 'Nâng cao'
  },
  // Tiếng Anh
  beginner: {
    class: 'bg-green-100 text-green-700',
    text: 'Cơ bản'
  },
  intermediate: {
    class: 'bg-yellow-100 text-yellow-700',
    text: 'Trung bình'
  },
  advanced: {
    class: 'bg-red-100 text-red-700',
    text: 'Nâng cao'
  }
}

// Chọn config dựa trên type
const currentConfig = computed(() => {
  if (props.config) return props.config
  if (props.type === 'category') return categoryConfig
  if (props.type === 'level') return levelConfig
  return statusConfig
})

// Normalize value to string
const normalizedValue = computed(() => {
  if (props.value === null || props.value === undefined) return ''
  return String(props.value).toLowerCase()
})

// Get badge class
const badgeClass = computed(() => {
  const key = normalizedValue.value
  const info = currentConfig.value[key]
  
  // Default class nếu không tìm thấy
  const defaultClass = props.type === 'category' 
    ? 'bg-blue-100 text-blue-700' 
    : 'bg-gray-100 text-gray-700'
  
  return info?.class || defaultClass
})

// Get badge text
const badgeText = computed(() => {
  // Ưu tiên prop text nếu có
  if (props.text) return props.text
  
  const key = normalizedValue.value
  const info = currentConfig.value[key]
  
  // Trả về text từ config hoặc giá trị gốc
  return info?.text || props.value
})
</script>
