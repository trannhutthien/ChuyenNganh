<template>
  <!-- Tài liệu tham khảo -->
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke-width="1.5" 
        stroke="currentColor" 
        class="w-5 h-5 text-primary"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" 
        />
      </svg>
      {{ title }}
    </h3>

    <!-- Danh sách tài liệu -->
    <ul class="space-y-2">
      <li 
        v-for="(reference, index) in references" 
        :key="index"
      >
        <a 
          :href="reference.url" 
          target="_blank" 
          rel="noopener noreferrer"
          class="text-primary hover:text-primary-600 flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors group"
        >
          <!-- External link icon -->
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke-width="1.5" 
            stroke="currentColor" 
            class="w-4 h-4 flex-shrink-0 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" 
            />
          </svg>

          <!-- Title -->
          <span class="flex-1 text-sm font-medium">{{ reference.title }}</span>

          <!-- Optional description -->
          <span 
            v-if="reference.description" 
            class="text-xs text-gray-500 hidden md:inline-block"
          >
            {{ reference.description }}
          </span>
        </a>
      </li>
    </ul>

    <!-- Empty state -->
    <div v-if="references.length === 0" class="text-center py-8">
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke-width="1.5" 
        stroke="currentColor" 
        class="w-12 h-12 text-gray-300 mx-auto mb-2"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" 
        />
      </svg>
      <p class="text-gray-500 text-sm">{{ emptyText }}</p>
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  // Danh sách tài liệu tham khảo
  references: {
    type: Array,
    default: () => [],
    validator: (value) => {
      return value.every(ref => 
        ref.hasOwnProperty('title') && 
        ref.hasOwnProperty('url')
      )
    }
  },
  // Tiêu đề
  title: {
    type: String,
    default: 'Tài liệu tham khảo'
  },
  // Text khi không có tài liệu
  emptyText: {
    type: String,
    default: 'Chưa có tài liệu tham khảo cho bài học này'
  }
})
</script>

<style scoped>
/* List styling */
ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

/* Link styling */
a {
  text-decoration: none;
}

a:hover {
  text-decoration: none;
}

/* Icon animation */
.group:hover svg {
  transition: transform 0.2s ease;
}

/* External link indicator */
a[target="_blank"]::after {
  content: none; /* Remove default external link indicator if any */
}

/* Responsive */
@media (max-width: 768px) {
  .hidden.md\:inline-block {
    display: none;
  }
}
</style>
