<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Loading State -->
    <div v-if="loading" class="p-8 text-center">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary mx-auto"></div>
      <p class="mt-4 text-gray-500">Đang tải ngân hàng câu hỏi...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="banks.length === 0" class="p-12 text-center">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-300">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
      </svg>
      <h3 class="mt-4 text-lg font-medium text-gray-900">Chưa có ngân hàng câu hỏi nào</h3>
      <p class="mt-2 text-gray-500">Tạo ngân hàng đầu tiên để bắt đầu thêm câu hỏi</p>
      <slot name="empty-action"></slot>
    </div>

    <!-- Question Banks List -->
    <div v-else class="divide-y divide-gray-100">
      <QuestionBankItem
        v-for="bank in banks"
        :key="bank.id"
        :bank="bank"
        @view="$emit('view', bank)"
        @edit="$emit('edit', bank)"
        @delete="$emit('delete', bank)"
      />
    </div>
  </div>
</template>

<script setup>
import QuestionBankItem from './QuestionBankItem.vue'

defineProps({
  banks: {
    type: Array,
    required: true,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

defineEmits(['view', 'edit', 'delete'])
</script>
