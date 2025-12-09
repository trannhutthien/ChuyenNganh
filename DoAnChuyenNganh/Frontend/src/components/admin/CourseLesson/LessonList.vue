<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Loading State -->
    <div v-if="loading" class="p-8 text-center">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary mx-auto"></div>
      <p class="mt-4 text-gray-500">{{ loadingText }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="lessons.length === 0" class="p-12 text-center">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-300">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
      </svg>
      <h3 class="mt-4 text-lg font-medium text-gray-900">{{ emptyTitle }}</h3>
      <p class="mt-2 text-gray-500">{{ emptySubtitle }}</p>
      <slot name="empty-action"></slot>
    </div>

    <!-- Lessons List -->
    <div v-else class="divide-y divide-gray-100">
      <LessonItem
        v-for="(lesson, index) in lessons"
        :key="lesson.id"
        :id="lesson.id"
        :title="lesson.title"
        :type="lesson.type"
        :order="lesson.order || index + 1"
        :duration="lesson.duration"
        :status="lesson.status"
        @view="$emit('view', lesson)"
        @edit="$emit('edit', lesson)"
        @delete="$emit('delete', lesson)"
      />
    </div>

    <!-- Pagination Slot -->
    <slot name="pagination"></slot>
  </div>
</template>

<script setup>
import LessonItem from './LessonItem.vue'

defineProps({
  lessons: {
    type: Array,
    required: true,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  loadingText: {
    type: String,
    default: 'Đang tải bài học...'
  },
  emptyTitle: {
    type: String,
    default: 'Chưa có bài học nào'
  },
  emptySubtitle: {
    type: String,
    default: 'Bắt đầu thêm bài học cho khóa học này'
  }
})

defineEmits(['view', 'edit', 'delete'])
</script>
