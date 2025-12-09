<template>
  <div 
    class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors"
  >
    <!-- Order Number -->
    <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
      {{ order }}
    </div>

    <!-- Lesson Type Icon -->
    <div :class="['w-10 h-10 rounded-lg flex items-center justify-center', typeIconClass]">
      <!-- Video Icon -->
      <svg v-if="type === 'video'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
      </svg>
      <!-- Text Icon -->
      <svg v-else-if="type === 'text'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
      </svg>
      <!-- Quiz Icon -->
      <svg v-else-if="type === 'quiz'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
      </svg>
      <!-- Assignment Icon -->
      <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
      </svg>
    </div>

    <!-- Lesson Info -->
    <div class="flex-1 min-w-0">
      <h3 class="font-medium text-gray-900 truncate">{{ title }}</h3>
      <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
        <span class="flex items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
          {{ duration }} phút
        </span>
        <span :class="['px-2 py-0.5 rounded-full text-xs font-medium', typeBadgeClass]">
          {{ typeLabel }}
        </span>
      </div>
    </div>

    <!-- Status Badge -->
    <BadgeLabel :value="status" type="status" />

    <!-- Actions using TableActions component -->
    <TableActions
      :row="lessonData"
      view-title="Xem nội dung"
      delete-confirm-title="Xóa bài học"
      :delete-confirm-message="`Bạn có chắc chắn muốn xóa bài học '${title}'? Hành động này không thể hoàn tác.`"
      @view="$emit('view')"
      @edit="$emit('edit')"
      @delete="$emit('delete')"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import TableActions from '../TableActions.vue'
import BadgeLabel from '../BadgeLabel.vue'

const props = defineProps({
  id: {
    type: [Number, String],
    required: true
  },
  title: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'video',
    validator: (value) => ['video', 'text', 'quiz', 'assignment'].includes(value)
  },
  order: {
    type: Number,
    default: 1
  },
  duration: {
    type: Number,
    default: 0
  },
  status: {
    type: [Number, String],
    default: 1
  }
})

defineEmits(['view', 'edit', 'delete'])

// Tạo object lesson data cho TableActions
const lessonData = computed(() => ({
  id: props.id,
  title: props.title,
  type: props.type,
  order: props.order,
  duration: props.duration,
  status: props.status
}))

// Type helpers
const typeLabel = computed(() => {
  const types = {
    video: 'Video',
    text: 'Văn bản',
    quiz: 'Bài kiểm tra',
    assignment: 'Bài tập'
  }
  return types[props.type] || 'Video'
})

const typeIconClass = computed(() => {
  const classes = {
    video: 'bg-blue-100 text-blue-600',
    text: 'bg-green-100 text-green-600',
    quiz: 'bg-purple-100 text-purple-600',
    assignment: 'bg-orange-100 text-orange-600'
  }
  return classes[props.type] || 'bg-blue-100 text-blue-600'
})

const typeBadgeClass = computed(() => {
  const classes = {
    video: 'bg-blue-100 text-blue-700',
    text: 'bg-green-100 text-green-700',
    quiz: 'bg-purple-100 text-purple-700',
    assignment: 'bg-orange-100 text-orange-700'
  }
  return classes[props.type] || 'bg-blue-100 text-blue-700'
})
</script>
