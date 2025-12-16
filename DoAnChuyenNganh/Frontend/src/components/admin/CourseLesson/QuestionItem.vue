<template>
  <div class="p-6 hover:bg-gray-50 transition-colors">
    <div class="flex items-start gap-4">
      <!-- Question Number -->
      <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-semibold">
        {{ index + 1 }}
      </div>

      <!-- Question Content -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-2">
          <span :class="['px-2.5 py-0.5 text-xs font-medium rounded-full', typeClass]">
            {{ typeText }}
          </span>
          <span :class="['px-2.5 py-0.5 text-xs font-medium rounded-full', difficultyClass]">
            {{ difficultyText }}
          </span>
          <span v-if="question.tags" class="text-xs text-gray-500">
            🏷️ {{ question.tags }}
          </span>
        </div>

        <div class="text-gray-800 font-medium mb-2 line-clamp-2">
          {{ question.question }}
        </div>

        <!-- Choices Preview -->
        <div v-if="question.choices && question.choices.length > 0" class="space-y-1 mb-3">
          <div 
            v-for="(choice, idx) in question.choices.slice(0, 3)" 
            :key="idx"
            class="flex items-center gap-2 text-sm"
          >
            <span :class="[
              'w-5 h-5 rounded flex items-center justify-center text-xs',
              choice.dungHaySai ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
            ]">
              {{ String.fromCharCode(65 + idx) }}
            </span>
            <span class="text-gray-600 truncate">{{ choice.noiDung }}</span>
          </div>
          <div v-if="question.choices.length > 3" class="text-xs text-gray-400 pl-7">
            +{{ question.choices.length - 3 }} lựa chọn khác
          </div>
        </div>

        <!-- Explanation Preview -->
        <div v-if="question.explanation" class="text-sm text-gray-500 italic line-clamp-1">
          💡 {{ question.explanation }}
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-2">
        <BaseButton
          @click="$emit('preview', question)"
          variant="ghost"
          size="sm"
          title="Xem trước"
        >
          <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
          </template>
        </BaseButton>
        
        <BaseButton
          @click="$emit('edit', question)"
          variant="ghost"
          size="sm"
          title="Sửa"
        >
          <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
          </template>
        </BaseButton>
        
        <BaseButton
          @click="$emit('delete', question)"
          variant="ghost"
          size="sm"
          class="text-red-600 hover:text-red-700 hover:bg-red-50"
          title="Xóa"
        >
          <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
          </template>
        </BaseButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import BaseButton from '../../ui/BaseButton.vue'

const props = defineProps({
  question: {
    type: Object,
    required: true
  },
  index: {
    type: Number,
    required: true
  }
})

defineEmits(['edit', 'delete', 'preview'])

const typeClass = computed(() => {
  const types = {
    'MOT_DAP_AN': 'bg-blue-100 text-blue-700',
    'NHIEU_DAP_AN': 'bg-purple-100 text-purple-700',
    'DUNG_SAI': 'bg-green-100 text-green-700',
    'DIEN_KHUYET': 'bg-orange-100 text-orange-700'
  }
  return types[props.question.type] || 'bg-gray-100 text-gray-700'
})

const typeText = computed(() => {
  const types = {
    'MOT_DAP_AN': 'Một đáp án',
    'NHIEU_DAP_AN': 'Nhiều đáp án',
    'DUNG_SAI': 'Đúng/Sai',
    'DIEN_KHUYET': 'Điền khuyết'
  }
  return types[props.question.type] || 'Khác'
})

const difficultyClass = computed(() => {
  const difficulty = props.question.difficulty || 1
  const classes = {
    1: 'bg-green-100 text-green-700',
    2: 'bg-yellow-100 text-yellow-700',
    3: 'bg-orange-100 text-orange-700',
    4: 'bg-red-100 text-red-700',
    5: 'bg-red-200 text-red-800'
  }
  return classes[difficulty] || classes[1]
})

const difficultyText = computed(() => {
  const difficulty = props.question.difficulty || 1
  const texts = {
    1: '⭐ Dễ',
    2: '⭐⭐ TB',
    3: '⭐⭐⭐ Khó',
    4: '⭐⭐⭐⭐ Rất khó',
    5: '⭐⭐⭐⭐⭐ Cực khó'
  }
  return texts[difficulty] || texts[1]
})
</script>
