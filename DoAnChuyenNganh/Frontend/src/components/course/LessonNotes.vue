<template>
  <!-- Ghi chú/Lưu ý -->
  <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-6">
    <h3 class="text-lg font-semibold text-amber-800 mb-3 flex items-center gap-2">
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
          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" 
        />
      </svg>
      {{ title }}
    </h3>

    <!-- Danh sách ghi chú -->
    <ul class="space-y-2">
      <li 
        v-for="(note, index) in notes" 
        :key="index" 
        class="text-amber-800 flex items-start gap-2"
      >
        <span class="text-amber-500 font-bold mt-0.5">•</span>
        <span class="flex-1">{{ note }}</span>
      </li>
    </ul>

    <!-- Empty state -->
    <div v-if="notes.length === 0" class="text-center py-4">
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke-width="1.5" 
        stroke="currentColor" 
        class="w-12 h-12 text-amber-300 mx-auto mb-2"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" 
        />
      </svg>
      <p class="text-amber-600 text-sm">{{ emptyText }}</p>
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  // Danh sách ghi chú
  notes: {
    type: Array,
    default: () => [],
    validator: (value) => {
      return value.every(note => typeof note === 'string')
    }
  },
  // Tiêu đề
  title: {
    type: String,
    default: 'Lưu ý'
  },
  // Text khi không có ghi chú
  emptyText: {
    type: String,
    default: 'Chưa có ghi chú cho bài học này'
  }
})
</script>

<style scoped>
/* Warning box styling */
.bg-amber-50 {
  background-color: #fffbeb;
}

.border-amber-500 {
  border-color: #f59e0b;
}

.text-amber-800 {
  color: #92400e;
}

.text-amber-500 {
  color: #f59e0b;
}

.text-amber-600 {
  color: #d97706;
}

.text-amber-300 {
  color: #fcd34d;
}

/* List styling */
ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

li {
  line-height: 1.6;
}

/* Icon alignment */
svg {
  flex-shrink: 0;
}
</style>
