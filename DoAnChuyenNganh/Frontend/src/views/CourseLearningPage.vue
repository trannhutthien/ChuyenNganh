<template>
  <!-- Container có chiều cao trừ đi header (65px) -->
  <div class="flex bg-white" style="height: calc(100vh - 65px);">
    <!-- Cột BÊN TRÁI - Nội dung bài học -->
    <LessonContent
      :lesson="currentLesson"
      :course-title="courseTitle"
      :current-index="currentLessonIndex"
      :total-lessons="lessons.length"
      :progress="progress"
      @next="nextLesson"
      @previous="previousLesson"
      @toggle-complete="markAsCompleted"
      @go-to-quiz="goToQuiz"
    />

    <!-- Cột BÊN PHẢI - Danh sách bài học -->
    <LessonSidebar
      :lessons="lessons"
      :current-index="currentLessonIndex"
      @select-lesson="selectLesson"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import LessonContent from '../components/course/LessonContent.vue'
import LessonSidebar from '../components/course/LessonSidebar.vue'

const router = useRouter()

// ========== STATE CHÍNH ==========
const courseTitle = ref('HTML & CSS Cơ bản')
const currentLessonIndex = ref(0)

// ========== DATA - Sẽ load từ API ==========
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

// ========== COMPUTED ==========
const currentLesson = computed(() => lessons.value[currentLessonIndex.value])

const completedCount = computed(() => {
  return lessons.value.filter(lesson => lesson.completed).length
})

const progress = computed(() => {
  return Math.round((completedCount.value / lessons.value.length) * 100)
})

// ========== METHODS - Navigation & Actions ==========
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

const goToQuiz = () => {
  const quizId = 1 // TODO: Lấy từ course data
  router.push(`/quiz/${quizId}`)
}

// ========== API CALLS (TODO) ==========
// const loadLessons = async (courseId) => {
//   try {
//     const response = await courseService.getLessons(courseId)
//     lessons.value = response.data
//   } catch (error) {
//     console.error('Error loading lessons:', error)
//   }
// }
//
// onMounted(() => {
//   const courseId = route.params.courseId
//   loadLessons(courseId)
// })
</script>

<style scoped>
/* Minimal styles - Components handle their own styling */
</style>
