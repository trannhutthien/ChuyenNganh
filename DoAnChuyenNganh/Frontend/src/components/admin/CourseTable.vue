<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <!-- Table Header -->
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th 
              v-for="column in columns" 
              :key="column.key"
              :class="[
                'py-4 px-6 font-semibold text-gray-700',
                column.align === 'center' ? 'text-center' : 
                column.align === 'right' ? 'text-right' : 'text-left',
                column.headerClass || ''
              ]"
              :style="column.width ? { width: column.width } : {}"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="divide-y divide-gray-100">
          <tr 
            v-for="(row, index) in data" 
            :key="row[rowKey] || index"
            class="hover:bg-gray-50 transition-colors"
          >
            <td 
              v-for="column in columns" 
              :key="column.key"
              :class="[
                'py-4 px-6',
                column.align === 'center' ? 'text-center' : 
                column.align === 'right' ? 'text-right' : 'text-left',
                column.cellClass || ''
              ]"
            >
              <!-- Sử dụng CourseTableRow component -->
              <CourseTableRow
                :row="row"
                :column-key="column.key"
                @view="$emit('view', $event)"
                @edit="$emit('edit', $event)"
                @delete="$emit('delete', $event)"
              />
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="data.length === 0">
            <td :colspan="columns.length" class="py-12 text-center">
              <slot name="empty">
                <div class="flex flex-col items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-300 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                  </svg>
                  <p class="text-gray-500 text-lg">{{ emptyText }}</p>
                  <p v-if="emptySubText" class="text-gray-400 text-sm mt-1">{{ emptySubText }}</p>
                </div>
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Slot -->
    <slot name="pagination"></slot>
  </div>
</template>

<script setup>
import CourseTableRow from './CourseTableRow.vue'

defineProps({
  // Cấu hình các cột
  columns: {
    type: Array,
    required: true,
    // Mỗi column: { key: 'id', label: 'ID', align: 'left|center|right', width: '100px', headerClass: '', cellClass: '' }
  },
  // Dữ liệu các hàng
  data: {
    type: Array,
    required: true
  },
  // Key để identify mỗi row (vd: 'id')
  rowKey: {
    type: String,
    default: 'id'
  },
  // Empty state text
  emptyText: {
    type: String,
    default: 'Không có dữ liệu'
  },
  emptySubText: {
    type: String,
    default: ''
  }
})

defineEmits(['view', 'edit', 'delete'])
</script>
