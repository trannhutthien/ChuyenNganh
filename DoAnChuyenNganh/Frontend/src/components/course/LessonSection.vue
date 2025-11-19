<template>
  <!-- Section container -->
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <!-- Tiêu đề section -->
    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ section.title }}</h3>
    
    <!-- Nội dung section -->
    <div class="prose prose-gray max-w-none">
      <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ section.content }}</p>
    </div>

    <!-- Code example nếu có -->
    <div v-if="section.codeExample" class="mt-4">
      <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
        <pre class="text-sm text-gray-100"><code>{{ section.codeExample }}</code></pre>
      </div>
    </div>

    <!-- Image nếu có -->
    <div v-if="section.image" class="mt-4">
      <img 
        :src="section.image" 
        :alt="section.title" 
        class="rounded-lg border border-gray-200 w-full object-cover"
        loading="lazy"
      />
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  // Thông tin section
  section: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.hasOwnProperty('title') && 
             value.hasOwnProperty('content')
    }
  }
})
</script>

<style scoped>
/* Prose styling cho nội dung */
.prose {
  max-width: none;
}

.prose p {
  margin-bottom: 1rem;
  line-height: 1.75;
}

.prose code {
  background-color: #f3f4f6;
  padding: 0.125rem 0.25rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
  color: #1f2937;
}

/* Code block styling */
pre {
  margin: 0;
  font-family: 'Courier New', Courier, monospace;
  line-height: 1.6;
}

pre code {
  display: block;
  padding: 0;
  background: transparent;
  color: inherit;
  white-space: pre;
  overflow-x: auto;
}

/* Custom scrollbar cho code block */
pre::-webkit-scrollbar {
  height: 6px;
}

pre::-webkit-scrollbar-track {
  background: #374151;
  border-radius: 3px;
}

pre::-webkit-scrollbar-thumb {
  background: #6b7280;
  border-radius: 3px;
}

pre::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Image styling */
img {
  max-width: 100%;
  height: auto;
  display: block;
}
</style>
