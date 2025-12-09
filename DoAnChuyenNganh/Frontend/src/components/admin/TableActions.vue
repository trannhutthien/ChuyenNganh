<template>
  <div class="flex items-center justify-center gap-2">
    <!-- View Button -->
    <button 
      v-if="showView"
      @click="$emit('view', row)"
      class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
      :title="viewTitle"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
      </svg>
    </button>

    <!-- Edit Button -->
    <button 
      v-if="showEdit"
      @click="$emit('edit', row)"
      class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
      :title="editTitle"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
      </svg>
    </button>

    <!-- Delete Button -->
    <button 
      v-if="showDelete"
      @click="showDeleteConfirm = true"
      class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
      :title="deleteTitle"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
      </svg>
    </button>

    <!-- Extra slot for custom actions -->
    <slot name="extra" :row="row"></slot>

    <!-- Delete Confirm Modal -->
    <ConfirmModal
      v-model:show="showDeleteConfirm"
      :title="deleteConfirmTitle"
      :message="deleteConfirmMessage"
      :confirm-text="deleteConfirmText"
      confirm-variant="danger"
      @confirm="handleDeleteConfirm"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import ConfirmModal from '../modal/ConfirmModal.vue'

const props = defineProps({
  row: {
    type: Object,
    required: true
  },
  // Control which buttons to show
  showView: {
    type: Boolean,
    default: true
  },
  showEdit: {
    type: Boolean,
    default: true
  },
  showDelete: {
    type: Boolean,
    default: true
  },
  // Custom titles
  viewTitle: {
    type: String,
    default: 'Xem chi tiết'
  },
  editTitle: {
    type: String,
    default: 'Chỉnh sửa'
  },
  deleteTitle: {
    type: String,
    default: 'Xóa'
  },
  // Delete confirm modal options
  deleteConfirmTitle: {
    type: String,
    default: 'Xác nhận xóa'
  },
  deleteConfirmMessage: {
    type: String,
    default: 'Bạn có chắc chắn muốn xóa? Hành động này không thể hoàn tác.'
  },
  deleteConfirmText: {
    type: String,
    default: 'Xóa'
  },
  // Item name for dynamic message
  itemName: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['view', 'edit', 'delete'])

// Delete confirm state
const showDeleteConfirm = ref(false)

// Handle delete confirm
const handleDeleteConfirm = () => {
  emit('delete', props.row)
  showDeleteConfirm.value = false
}
</script>
