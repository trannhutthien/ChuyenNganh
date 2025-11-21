<template>
  <div class="space-y-4">
    <!-- Instruction -->
    <div class="flex items-start gap-2 text-sm text-gray-600 bg-blue-50 px-4 py-3 rounded-lg border border-blue-200">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 flex-shrink-0 mt-0.5 text-blue-600">
        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
      </svg>
      <span class="font-medium">{{ instruction }}</span>
    </div>

    <!-- Matching Grid -->
    <div class="grid md:grid-cols-2 gap-6">
      <!-- Left Items -->
      <div class="space-y-3">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">Câu hỏi</h4>
        <div
          v-for="(item, index) in question.leftItems"
          :key="index"
          class="p-4 bg-gradient-to-r from-primary/10 to-primary/5 border-l-4 border-primary rounded-lg"
        >
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center font-bold text-sm">
              {{ index + 1 }}
            </div>
            <div class="flex-1 text-gray-800 leading-relaxed pt-1">
              {{ item }}
            </div>
          </div>
        </div>
      </div>

      <!-- Right Items with Selects -->
      <div class="space-y-3">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">Đáp án</h4>
        <div
          v-for="(item, index) in question.leftItems"
          :key="index"
          class="p-4 bg-white border-2 rounded-lg transition-all duration-200"
          :class="getMatchClass(index)"
        >
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-8 h-8 bg-gray-200 text-gray-700 rounded-full flex items-center justify-center font-bold text-sm">
              {{ index + 1 }}
            </div>
            <div class="flex-1">
              <select
                :value="modelValue[index]"
                @change="handleChange(index, $event.target.value)"
                :disabled="disabled"
                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary disabled:bg-gray-100 disabled:cursor-not-allowed transition-all"
                :class="{ 'border-primary bg-primary/5': modelValue[index] !== undefined && modelValue[index] !== '' }"
              >
                <option value="">-- Chọn đáp án --</option>
                <option
                  v-for="(rightItem, rIndex) in question.rightItems"
                  :key="rIndex"
                  :value="rIndex"
                >
                  {{ rightItem }}
                </option>
              </select>

              <!-- Selected Answer Preview -->
              <div
                v-if="modelValue[index] !== undefined && modelValue[index] !== '' && !showCorrect"
                class="mt-2 text-sm text-primary font-medium"
              >
                ✓ {{ question.rightItems[modelValue[index]] }}
              </div>

              <!-- Review Mode - Show Correct/Incorrect -->
              <div v-if="showCorrect" class="mt-2">
                <div
                  v-if="isMatchCorrect(index)"
                  class="flex items-center gap-2 text-sm text-green-700 font-medium"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                  </svg>
                  Chính xác!
                </div>
                <div v-else class="space-y-1">
                  <div class="flex items-center gap-2 text-sm text-red-700 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                      <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                    </svg>
                    Không chính xác
                  </div>
                  <div class="text-sm text-gray-700 ml-7">
                    <span class="font-medium">Đáp án đúng:</span> {{ question.rightItems[correctAnswers[index]] }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Progress Indicator -->
    <div v-if="!disabled && !showCorrect" class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700">Tiến độ</span>
        <span class="text-sm font-semibold text-primary">{{ matchedCount }}/{{ question.leftItems.length }}</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
        <div
          class="bg-gradient-to-r from-primary to-primary-dark h-full transition-all duration-300"
          :style="{ width: progressPercentage + '%' }"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Dữ liệu câu hỏi
  question: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.leftItems && Array.isArray(value.leftItems) &&
             value.rightItems && Array.isArray(value.rightItems)
    }
  },

  // Giá trị đã chọn (object mapping left index to right index)
  modelValue: {
    type: Object,
    default: () => ({})
  },

  // Disabled state
  disabled: {
    type: Boolean,
    default: false
  },

  // Hiển thị đáp án đúng (review mode)
  showCorrect: {
    type: Boolean,
    default: false
  },

  // Đáp án đúng (object mapping left index to right index)
  correctAnswers: {
    type: Object,
    default: () => ({})
  },

  // Instruction text
  instruction: {
    type: String,
    default: 'Ghép mỗi câu hỏi bên trái với đáp án phù hợp bên phải'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

// Số cặp đã ghép
const matchedCount = computed(() => {
  return Object.values(props.modelValue).filter(v => v !== undefined && v !== '').length
})

// Phần trăm hoàn thành
const progressPercentage = computed(() => {
  if (!props.question.leftItems.length) return 0
  return (matchedCount.value / props.question.leftItems.length) * 100
})

// Kiểm tra một cặp ghép có đúng không
const isMatchCorrect = (leftIndex) => {
  if (!props.showCorrect) return false
  return props.modelValue[leftIndex] === props.correctAnswers[leftIndex]
}

// Get class cho mỗi match item
const getMatchClass = (index) => {
  const classes = []

  if (props.showCorrect) {
    if (isMatchCorrect(index)) {
      classes.push('border-green-500 bg-green-50')
    } else {
      classes.push('border-red-500 bg-red-50')
    }
  } else if (props.modelValue[index] !== undefined && props.modelValue[index] !== '') {
    classes.push('border-primary bg-primary/5')
  } else {
    classes.push('border-gray-200 hover:border-gray-300')
  }

  return classes.join(' ')
}

// Handle change
const handleChange = (leftIndex, rightIndex) => {
  if (props.disabled) return

  const newValue = { ...props.modelValue }
  
  if (rightIndex === '') {
    delete newValue[leftIndex]
  } else {
    newValue[leftIndex] = parseInt(rightIndex)
  }

  emit('update:modelValue', newValue)
  emit('change', newValue)
}
</script>
