<template>
  <!-- Container có chiều cao trừ đi header (65px) -->
  <div class="flex bg-white" style="height: calc(100vh - 65px);">
    <!-- Cột BÊN TRÁI - Nội dung bài học (RỘNG) -->
    <div class="flex-1 flex flex-col overflow-hidden bg-white">
      <!-- Header của bài học -->
      <div class="bg-white border-b border-gray-200 px-6 py-4 shadow-sm">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <!-- Tên khóa học -->
            <div>
              <h1 class="text-lg font-bold text-gray-800">{{ courseTitle }}</h1>
              <p class="text-sm text-gray-500">{{ currentLesson.title }}</p>
            </div>
          </div>

          <!-- Tiến độ -->
          <div class="flex items-center gap-4">
            <div class="text-sm text-gray-500">
              Bài {{ currentLessonIndex + 1 }}/{{ lessons.length }}
            </div>
            <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
              <div 
                class="h-full bg-primary transition-all duration-300"
                :style="{ width: progress + '%' }"
              ></div>
            </div>
            <span class="text-sm text-primary font-semibold">{{ progress }}%</span>
          </div>
        </div>
      </div>

      <!-- Nội dung bài học (scrollable) - Nhiều rows -->
      <div class="flex-1 overflow-y-auto bg-gray-50">
        <div class="max-w-4xl mx-auto p-8">
          <!-- Row 1: Tiêu đề bài học -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ currentLesson.title }}</h2>
            <div class="flex items-center gap-4 text-sm text-gray-500">
              <span class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ currentLesson.duration }}
              </span>
              <span class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>
                {{ currentLesson.category }}
              </span>
            </div>
          </div>

          <!-- Row 2: Mô tả/Giới thiệu -->
          <div v-if="currentLesson.description" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
              </svg>
              Giới thiệu
            </h3>
            <p class="text-gray-600 leading-relaxed">{{ currentLesson.description }}</p>
          </div>

          <!-- Row 3: Nội dung chính -->
          <div v-if="currentLesson.sections && currentLesson.sections.length > 0" class="space-y-6">
            <div 
              v-for="(section, index) in currentLesson.sections" 
              :key="index"
              class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
            >
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
                <img :src="section.image" :alt="section.title" class="rounded-lg border border-gray-200 w-full" />
              </div>
            </div>
          </div>

          <!-- Row 4: Video (nếu có) -->
          <div v-if="currentLesson.videoUrl" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
              </svg>
              Video bài học
            </h3>
            <div class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center">
              <!-- Placeholder - Thay bằng video player thật -->
              <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-400 mx-auto mb-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112Z" />
                </svg>
                <p class="text-gray-500 text-sm">Video: {{ currentLesson.videoUrl }}</p>
              </div>
            </div>
          </div>

          <!-- Row 5: Ghi chú/Lưu ý -->
          <div v-if="currentLesson.notes && currentLesson.notes.length > 0" class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-amber-800 mb-3 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
              </svg>
              Lưu ý
            </h3>
            <ul class="space-y-2">
              <li v-for="(note, index) in currentLesson.notes" :key="index" class="text-amber-800 flex items-start gap-2">
                <span class="text-amber-500 font-bold">•</span>
                <span>{{ note }}</span>
              </li>
            </ul>
          </div>

          <!-- Row 6: Tài liệu tham khảo -->
          <div v-if="currentLesson.references && currentLesson.references.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
              </svg>
              Tài liệu tham khảo
            </h3>
            <ul class="space-y-2">
              <li v-for="(ref, index) in currentLesson.references" :key="index">
                <a :href="ref.url" target="_blank" class="text-primary hover:text-primary-600 flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                  </svg>
                  {{ ref.title }}
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Footer - Navigation buttons -->
      <div class="bg-white border-t border-gray-200 px-6 py-4 shadow-sm">
        <div class="flex items-center justify-between">
          <!-- Nút Bài trước -->
          <button 
            @click="previousLesson"
            :disabled="currentLessonIndex === 0"
            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
            Bài trước
          </button>

          <!-- Đánh dấu hoàn thành -->
          <button 
            @click="markAsCompleted"
            class="px-6 py-3 border-2 border-primary text-primary rounded-lg font-semibold hover:bg-primary hover:text-white transition-colors flex items-center gap-2"
            :class="{ 'bg-primary text-white': currentLesson.completed }"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ currentLesson.completed ? 'Đã hoàn thành' : 'Hoàn thành' }}
          </button>

          <!-- Nút Bài tiếp theo -->
          <button 
            @click="nextLesson"
            :disabled="currentLessonIndex === lessons.length - 1"
            class="px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            Bài tiếp theo
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Cột BÊN PHẢI - Danh sách bài học (HẸP hơn) -->
    <div class="w-64 bg-gray-800 border-l border-gray-700 flex flex-col overflow-hidden">
      <!-- Header danh sách bài học -->
      <div class="px-4 py-3 border-b border-gray-700">
        <h2 class="text-sm font-bold text-white">Nội dung khóa học</h2>
      </div>

      <!-- Danh sách bài học (scrollable) -->
      <div class="flex-1 overflow-y-auto">
        <div class="p-2 space-y-1">
          <!-- Lesson item - Đơn giản hóa -->
          <div 
            v-for="(lesson, index) in lessons" 
            :key="lesson.id"
            @click="selectLesson(index)"
            class="rounded-lg px-3 py-2 cursor-pointer transition-all flex items-center gap-2"
            :class="[
              currentLessonIndex === index 
                ? 'bg-primary/20 text-primary font-semibold' 
                : 'text-gray-300 hover:bg-gray-700',
              lesson.completed ? 'opacity-75' : ''
            ]"
          >
            <!-- Icon đơn giản -->
            <div class="flex-shrink-0 w-5 h-5 rounded flex items-center justify-center text-xs font-bold"
                 :class="lesson.completed ? 'bg-green-500 text-white' : 'bg-gray-600 text-gray-300'">
              <span v-if="lesson.completed">✓</span>
              <span v-else>{{ index + 1 }}</span>
            </div>

            <!-- Tên bài học -->
            <span class="text-sm flex-1 truncate">{{ lesson.title }}</span>

            <!-- Playing indicator -->
            <div v-if="currentLessonIndex === index" class="flex-shrink-0">
              <div class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Course info
const courseTitle = ref('HTML & CSS Cơ bản')

// Current lesson index
const currentLessonIndex = ref(0)

// Danh sách bài học - Dữ liệu mẫu (sẽ lấy từ API/Database)
const lessons = ref([
  {
    id: 1,
    title: 'HTML Links',
    category: 'HTML',
    duration: '15 phút',
    completed: true,
    description: 'Tìm hiểu cách tạo và sử dụng liên kết (links) trong HTML để điều hướng giữa các trang web.',
    videoUrl: null, // URL video nếu có
    sections: [
      {
        title: 'Thẻ <a> trong HTML',
        content: 'Thẻ <a> (anchor) được sử dụng để tạo liên kết trong HTML. Thuộc tính href xác định URL đích của liên kết.\n\nCú pháp cơ bản:\n<a href="url">Nội dung liên kết</a>',
        codeExample: '<a href="https://www.example.com">Nhấn vào đây</a>',
        image: null
      },
      {
        title: 'Các loại liên kết',
        content: '1. Liên kết tuyệt đối: Đường dẫn đầy đủ đến trang web khác\n2. Liên kết tương đối: Đường dẫn đến file trong cùng website\n3. Liên kết neo (anchor): Di chuyển đến vị trí cụ thể trong trang',
        codeExample: '<!-- Liên kết tuyệt đối -->\n<a href="https://google.com">Google</a>\n\n<!-- Liên kết tương đối -->\n<a href="/about.html">Về chúng tôi</a>\n\n<!-- Liên kết neo -->\n<a href="#section1">Đến phần 1</a>',
        image: null
      },
      {
        title: 'Thuộc tính target',
        content: 'Thuộc tính target xác định cách mở liên kết:\n- _blank: Mở trong tab mới\n- _self: Mở trong cùng tab (mặc định)\n- _parent: Mở trong frame cha\n- _top: Mở trong toàn bộ cửa sổ',
        codeExample: '<a href="https://example.com" target="_blank">Mở tab mới</a>',
        image: null
      }
    ],
    notes: [
      'Luôn sử dụng văn bản mô tả rõ ràng cho liên kết',
      'Tránh dùng "click here" hoặc "đọc thêm" không có ngữ cảnh',
      'Sử dụng target="_blank" cẩn thận vì có thể gây khó chịu cho người dùng'
    ],
    references: [
      { title: 'MDN - HTML <a> Element', url: 'https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a' },
      { title: 'W3Schools - HTML Links', url: 'https://www.w3schools.com/html/html_links.asp' }
    ]
  },
  {
    id: 2,
    title: 'HTML Images',
    category: 'HTML',
    duration: '18 phút',
    completed: false,
    description: 'Học cách chèn và tối ưu hóa hình ảnh trong HTML.',
    videoUrl: 'video-placeholder.mp4',
    sections: [
      {
        title: 'Thẻ <img> trong HTML',
        content: 'Thẻ <img> được sử dụng để chèn hình ảnh vào trang web. Đây là thẻ tự đóng (self-closing tag).',
        codeExample: '<img src="image.jpg" alt="Mô tả hình ảnh">',
        image: null
      },
      {
        title: 'Thuộc tính quan trọng',
        content: '1. src: Đường dẫn đến file hình ảnh (bắt buộc)\n2. alt: Văn bản thay thế khi ảnh không tải được (bắt buộc)\n3. width, height: Kích thước hình ảnh\n4. title: Tooltip khi hover chuột',
        codeExample: '<img src="photo.jpg" alt="Phong cảnh" width="500" height="300" title="Ảnh phong cảnh đẹp">',
        image: null
      }
    ],
    notes: [
      'Luôn thêm thuộc tính alt cho accessibility',
      'Tối ưu kích thước ảnh trước khi upload',
      'Sử dụng định dạng ảnh phù hợp (JPEG, PNG, WebP)'
    ],
    references: [
      { title: 'MDN - HTML <img> Element', url: 'https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img' }
    ]
  },
  {
    id: 3,
    title: 'HTML Tables',
    category: 'HTML',
    duration: '20 phút',
    completed: false,
    description: 'Tạo và định dạng bảng dữ liệu trong HTML.',
    videoUrl: null,
    sections: [
      {
        title: 'Cấu trúc bảng HTML',
        content: 'Bảng HTML bao gồm các thẻ chính:\n- <table>: Thẻ bao ngoài\n- <tr>: Table row (hàng)\n- <td>: Table data (ô dữ liệu)\n- <th>: Table header (tiêu đề)',
        codeExample: '<table>\n  <tr>\n    <th>Tên</th>\n    <th>Tuổi</th>\n  </tr>\n  <tr>\n    <td>An</td>\n    <td>25</td>\n  </tr>\n</table>',
        image: null
      }
    ],
    notes: [
      'Sử dụng bảng cho dữ liệu dạng bảng, không dùng để layout',
      'Thêm border và padding để dễ đọc'
    ],
    references: []
  },
  {
    id: 4,
    title: 'HTML Forms',
    category: 'HTML',
    duration: '25 phút',
    completed: false,
    description: 'Tạo biểu mẫu nhập liệu với HTML.',
    videoUrl: 'video-form.mp4',
    sections: [
      {
        title: 'Thẻ <form>',
        content: 'Thẻ <form> dùng để tạo biểu mẫu thu thập thông tin từ người dùng.',
        codeExample: '<form action="/submit" method="POST">\n  <input type="text" name="username">\n  <button type="submit">Gửi</button>\n</form>',
        image: null
      }
    ],
    notes: [],
    references: []
  },
  {
    id: 5,
    title: 'CSS Selectors',
    category: 'CSS',
    duration: '22 phút',
    completed: false,
    description: 'Các cách chọn phần tử HTML để áp dụng CSS.',
    videoUrl: null,
    sections: [
      {
        title: 'Selector cơ bản',
        content: 'CSS selector dùng để chọn phần tử HTML cần style:\n- Element selector: p, div, h1\n- Class selector: .class-name\n- ID selector: #id-name',
        codeExample: '/* Element */\np { color: red; }\n\n/* Class */\n.button { background: blue; }\n\n/* ID */\n#header { font-size: 24px; }',
        image: null
      }
    ],
    notes: [],
    references: []
  }
])

// Computed properties
const currentLesson = computed(() => lessons.value[currentLessonIndex.value])

const completedCount = computed(() => {
  return lessons.value.filter(lesson => lesson.completed).length
})

const progress = computed(() => {
  return Math.round((completedCount.value / lessons.value.length) * 100)
})

// Methods
const selectLesson = (index) => {
  currentLessonIndex.value = index
}

const nextLesson = () => {
  if (currentLessonIndex.value < lessons.value.length - 1) {
    currentLessonIndex.value++
  }
}

const previousLesson = () => {
  if (currentLessonIndex.value > 0) {
    currentLessonIndex.value--
  }
}

const markAsCompleted = () => {
  lessons.value[currentLessonIndex.value].completed = !lessons.value[currentLessonIndex.value].completed
}

const goBack = () => {
  router.push('/')
}
</script>

<style scoped>
/* Custom scrollbar cho theme sáng */
.overflow-y-auto::-webkit-scrollbar {
  width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f3f4f6;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

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
}
</style>
