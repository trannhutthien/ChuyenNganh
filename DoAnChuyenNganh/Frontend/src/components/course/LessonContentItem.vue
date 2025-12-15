<template>
  <!-- Render nội dung bài học dựa trên loại -->
  <div class="lesson-content-item">
    <!-- HEADING - Tiêu đề chính -->
    <div v-if="item.type === 'heading'" class="mb-6 mt-6 first:mt-0">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">
        {{ item.title }}
      </h1>
      <p v-if="item.content" class="text-gray-600 leading-relaxed">
        {{ item.content }}
      </p>
    </div>

    <!-- SUBHEADING - Tiêu đề phụ -->
    <div v-else-if="item.type === 'subheading'" class="mb-5 mt-5">
      <h2 class="text-2xl font-semibold text-gray-800 mb-2">
        {{ item.title }}
      </h2>
      <p v-if="item.content" class="text-gray-600 leading-relaxed">
        {{ item.content }}
      </p>
    </div>

    <!-- PARAGRAPH - Đoạn văn -->
    <div v-else-if="item.type === 'paragraph'" class="mb-4">
      <h3 v-if="item.title" class="text-lg font-medium text-gray-800 mb-2">
        {{ item.title }}
      </h3>
      <p class="text-gray-600 leading-relaxed whitespace-pre-line">
        {{ item.content }}
      </p>
    </div>

    <!-- IMAGE - Hình ảnh -->
    <div v-else-if="item.type === 'image'" class="my-6">
      <h3 v-if="item.title" class="text-lg font-medium text-gray-800 mb-3">
        {{ item.title }}
      </h3>
      <img 
        :src="getImageUrl(item.content)" 
        :alt="item.title || 'Hình ảnh bài học'"
        class="w-full rounded-lg border border-gray-200 shadow-sm"
        loading="lazy"
        @error="handleImageError"
      />
    </div>

    <!-- VIDEO - Video embed -->
    <div v-else-if="item.type === 'video'" class="my-6">
      <h3 v-if="item.title" class="text-lg font-medium text-gray-800 mb-3">
        {{ item.title }}
      </h3>
      <div class="aspect-video rounded-lg overflow-hidden border border-gray-200 shadow-sm">
        <iframe
          :src="getVideoEmbedUrl(item.content)"
          class="w-full h-full"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
        ></iframe>
      </div>
    </div>

    <!-- QUOTE - Trích dẫn -->
    <div v-else-if="item.type === 'quote'" class="my-4">
      <h3 v-if="item.title" class="text-lg font-medium text-gray-800 mb-2">
        {{ item.title }}
      </h3>
      <blockquote class="border-l-4 border-primary bg-primary/5 pl-4 py-3 italic text-gray-700">
        <p class="mb-0">{{ item.content }}</p>
      </blockquote>
    </div>

    <!-- LIST - Danh sách -->
    <div v-else-if="item.type === 'list'" class="mb-4">
      <h3 v-if="item.title" class="text-lg font-medium text-gray-800 mb-2">
        {{ item.title }}
      </h3>
      <ul class="list-disc list-inside space-y-2 text-gray-600 ml-4">
        <li 
          v-for="(listItem, index) in parseListContent(item.content)" 
          :key="index"
          class="leading-relaxed"
        >
          {{ listItem }}
        </li>
      </ul>
    </div>

    <!-- CODE - Code block (nếu cần thêm sau) -->
    <div v-else-if="item.type === 'code'" class="my-4">
      <h3 v-if="item.title" class="text-lg font-medium text-gray-800 mb-2">
        {{ item.title }}
      </h3>
      <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
        <pre class="text-sm text-gray-100"><code>{{ item.content }}</code></pre>
      </div>
    </div>

    <!-- DEFAULT - Fallback cho loại không xác định -->
    <div v-else class="mb-4">
      <h3 v-if="item.title" class="text-lg font-medium text-gray-800 mb-2">
        {{ item.title }}
      </h3>
      <p class="text-gray-600">{{ item.content }}</p>
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  item: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.hasOwnProperty('type')
    }
  }
})

// Helper: Lấy URL hình ảnh
const getImageUrl = (url) => {
  if (!url) return ''
  // Nếu đã là URL đầy đủ
  if (url.startsWith('http')) {
    return url
  }
  // Nếu là đường dẫn relative, thêm base URL
  return `http://localhost:8000/storage${url}`
}

// Helper: Xử lý lỗi load ảnh
const handleImageError = (event) => {
  // Ẩn hình ảnh thay vì hiển thị placeholder lỗi
  event.target.style.display = 'none'
  // Hoặc dùng hình ảnh local/data URI
  // event.target.src = '/images/no-image.png'
}

// Helper: Lấy URL embed video (YouTube, Vimeo)
const getVideoEmbedUrl = (url) => {
  if (!url) return ''
  
  // Nếu đã là embed URL
  if (url.includes('/embed/')) {
    return url
  }
  
  // YouTube - chuyển đổi watch URL thành embed URL
  if (url.includes('youtube.com/watch')) {
    const videoId = new URL(url).searchParams.get('v')
    return `https://www.youtube.com/embed/${videoId}`
  }
  
  // YouTube short URL
  if (url.includes('youtu.be/')) {
    const videoId = url.split('youtu.be/')[1].split('?')[0]
    return `https://www.youtube.com/embed/${videoId}`
  }
  
  // Vimeo
  if (url.includes('vimeo.com/')) {
    const videoId = url.split('vimeo.com/')[1].split('?')[0]
    return `https://player.vimeo.com/video/${videoId}`
  }
  
  return url
}

// Helper: Parse nội dung list (phân tách bằng newline hoặc dấu -)
const parseListContent = (content) => {
  if (!content) return []
  
  // Tách theo dòng mới
  const lines = content.split('\n').filter(line => line.trim())
  
  // Loại bỏ dấu - hoặc * ở đầu nếu có
  return lines.map(line => line.replace(/^[\-\*]\s*/, '').trim())
}
</script>

<style scoped>
/* Styling cho code block */
pre {
  margin: 0;
  font-family: 'Fira Code', 'Courier New', Courier, monospace;
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

/* Responsive image */
img {
  max-width: 100%;
  height: auto;
}
</style>
