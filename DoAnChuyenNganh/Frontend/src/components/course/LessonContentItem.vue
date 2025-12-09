<template>
  <!-- Render nội dung bài học dựa trên loại -->
  <div class="lesson-content-item">
    <!-- HEADING - Tiêu đề lớn -->
    <h2 
      v-if="item.type === 'heading'" 
      class="text-2xl font-bold text-gray-800 mb-4 mt-6 first:mt-0"
    >
      {{ item.content }}
    </h2>

    <!-- SUBHEADING - Tiêu đề phụ -->
    <h3 
      v-else-if="item.type === 'subheading'" 
      class="text-xl font-semibold text-gray-700 mb-3 mt-5"
    >
      {{ item.content }}
    </h3>

    <!-- PARAGRAPH - Đoạn văn -->
    <p 
      v-else-if="item.type === 'paragraph'" 
      class="text-gray-600 leading-relaxed mb-4 whitespace-pre-line"
    >
      {{ item.content }}
    </p>

    <!-- IMAGE - Hình ảnh -->
    <div 
      v-else-if="item.type === 'image'" 
      class="my-6"
    >
      <img 
        :src="getImageUrl(item.content)" 
        :alt="'Hình ảnh bài học'"
        class="w-full rounded-lg border border-gray-200 shadow-sm"
        loading="lazy"
        @error="handleImageError"
      />
    </div>

    <!-- VIDEO - Video embed -->
    <div 
      v-else-if="item.type === 'video'" 
      class="my-6"
    >
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
    <blockquote 
      v-else-if="item.type === 'quote'" 
      class="border-l-4 border-primary bg-primary/5 pl-4 py-3 my-4 italic text-gray-700"
    >
      <p class="mb-0">{{ item.content }}</p>
    </blockquote>

    <!-- LIST - Danh sách -->
    <ul 
      v-else-if="item.type === 'list'" 
      class="list-disc list-inside space-y-2 text-gray-600 mb-4 ml-4"
    >
      <li 
        v-for="(listItem, index) in parseListContent(item.content)" 
        :key="index"
        class="leading-relaxed"
      >
        {{ listItem }}
      </li>
    </ul>

    <!-- CODE - Code block (nếu cần thêm sau) -->
    <div 
      v-else-if="item.type === 'code'" 
      class="my-4"
    >
      <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
        <pre class="text-sm text-gray-100"><code>{{ item.content }}</code></pre>
      </div>
    </div>

    <!-- DEFAULT - Fallback cho loại không xác định -->
    <div v-else class="text-gray-600 mb-4">
      {{ item.content }}
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
      return value.hasOwnProperty('type') && value.hasOwnProperty('content')
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
