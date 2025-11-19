<template>
  <div class="flex-1 flex flex-col overflow-hidden bg-white">
    <!-- Header của bài học -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 shadow-sm">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <!-- Tên khóa học -->
          <div>
            <h1 class="text-lg font-bold text-gray-800">{{ courseTitle }}</h1>
            <p class="text-sm text-gray-500">{{ lesson.title }}</p>
          </div>
        </div>

        <!-- Tiến độ -->
        <div class="flex items-center gap-4">
          <div class="text-sm text-gray-500">
            Bài {{ currentIndex + 1 }}/{{ totalLessons }}
          </div>
          <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
            <div 
              class="h-full bg-primary transition-all duration-300"
              :style="{ width: progress + '%' }"
            ></div>
          </div>
          <span class="text-sm text-primary font-semibold">{{ progress }}%</span>
        </div>
      </div>
    </div>

    <!-- Nội dung bài học (scrollable) -->
    <div class="flex-1 overflow-y-auto bg-gray-50">
      <div class="max-w-4xl mx-auto p-8">
        <!-- Row 1: Tiêu đề bài học -->
        <LessonHeader
          :title="lesson.title"
          :duration="lesson.duration"
          :category="lesson.category"
          :difficulty="lesson.difficulty"
          class="mb-6"
        />

        <!-- Row 2: Mô tả/Giới thiệu -->
        <div v-if="lesson.description" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            Giới thiệu
          </h3>
          <p class="text-gray-600 leading-relaxed">{{ lesson.description }}</p>
        </div>

        <!-- Row 3: Nội dung chính - Sections -->
        <div v-if="lesson.sections && lesson.sections.length > 0" class="space-y-6">
          <LessonSection
            v-for="(section, index) in lesson.sections"
            :key="index"
            :section="section"
          />
        </div>

        <!-- Row 4: Video (nếu có) -->
        <LessonVideo
          v-if="lesson.videoUrl"
          :video-url="lesson.videoUrl"
          :title="`Video: ${lesson.title}`"
          :duration="lesson.videoDuration"
          :file-size="lesson.videoSize"
          :show-info="true"
          class="mb-6"
        />

        <!-- Row 5: Ghi chú/Lưu ý - Notes -->
        <LessonNotes
          v-if="lesson.notes && lesson.notes.length > 0"
          :notes="lesson.notes"
          class="mb-6"
        />

        <!-- Row 6: Tài liệu tham khảo - References -->
        <LessonReferences
          v-if="lesson.references && lesson.references.length > 0"
          :references="lesson.references"
          class="mb-6"
        />
      </div>
    </div>

    <!-- Footer - Navigation buttons -->
    <div class="bg-white border-t border-gray-200 px-6 py-4 shadow-sm">
      <div class="flex items-center justify-between">
        <!-- Nút Bài trước -->
        <BaseButton 
          @click="$emit('previous')"
          :disabled="isFirstLesson"
          variant="secondary"
          size="md"
        >
          <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
          </template>
          Bài trước
        </BaseButton>

        <!-- Đánh dấu hoàn thành -->
        <BaseButton 
          @click="$emit('toggle-complete')"
          :variant="lesson.completed ? 'primary' : 'outline'"
          size="md"
        >
          <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
          </template>
          {{ lesson.completed ? 'Đã hoàn thành' : 'Hoàn thành' }}
        </BaseButton>

        <!-- Nút Bài tiếp theo hoặc Làm bài kiểm tra -->
        <BaseButton 
          v-if="isLastLesson"
          @click="$emit('go-to-quiz')"
          variant="success"
          size="md"
        >
          <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
          </template>
          Làm bài kiểm tra
        </BaseButton>
        <BaseButton 
          v-else
          @click="$emit('next')"
          variant="primary"
          size="md"
          icon-position="right"
        >
          Bài tiếp theo
          <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </template>
        </BaseButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import BaseButton from '../ui/BaseButton.vue'
import LessonHeader from './LessonHeader.vue'
import LessonSection from './LessonSection.vue'
import LessonVideo from './LessonVideo.vue'
import LessonNotes from './LessonNotes.vue'
import LessonReferences from './LessonReferences.vue'

// Props
const props = defineProps({
  // Thông tin bài học hiện tại
  lesson: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.hasOwnProperty('id') && 
             value.hasOwnProperty('title') &&
             value.hasOwnProperty('category') &&
             value.hasOwnProperty('duration')
    }
  },
  // Tên khóa học
  courseTitle: {
    type: String,
    required: true
  },
  // Index bài học hiện tại (bắt đầu từ 0)
  currentIndex: {
    type: Number,
    required: true
  },
  // Tổng số bài học
  totalLessons: {
    type: Number,
    required: true
  },
  // Tiến độ hoàn thành khóa học (%)
  progress: {
    type: Number,
    default: 0
  }
})

// Emits
const emit = defineEmits([
  'next',           // Chuyển sang bài tiếp theo
  'previous',       // Quay lại bài trước
  'toggle-complete', // Đánh dấu hoàn thành/chưa hoàn thành
  'go-to-quiz'      // Chuyển đến trang quiz
])

// Computed
const isFirstLesson = computed(() => props.currentIndex === 0)
const isLastLesson = computed(() => props.currentIndex === props.totalLessons - 1)
</script>

<style scoped>
/* Custom scrollbar cho theme sáng */
.overflow-y-auto::-webkit-scrollbar {
  width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f3f4f6;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Removed duplicate prose styles - now handled by child components */
</style>
