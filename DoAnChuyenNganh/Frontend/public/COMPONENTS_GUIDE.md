# Components Guide - Hướng dẫn sử dụng Components

## 📋 Tổng quan

Thư mục `components/` chứa tất cả các Vue components tái sử dụng trong ứng dụng, được tổ chức theo chức năng và module.

---

## 🎨 `/ui` - UI Components cơ bản

### BaseButton.vue
**Công dụng:** Button component với nhiều variants và sizes  
**Props:** variant, size, loading, disabled, fullWidth  
**Ứng dụng:** Toàn bộ ứng dụng thay cho `<button>` thông thường

### BaseInput.vue
**Công dụng:** Input field với validation  
**Props:** type, label, placeholder, error, required  
**Ứng dụng:** Tất cả forms (login, register, admin forms)

### SearchInput.vue
**Công dụng:** Input tìm kiếm với icon và clear button  
**Props:** placeholder, modelValue  
**Ứng dụng:** HomePage, Admin tables, QuestionBank

### TablePagination.vue
**Công dụng:** Component phân trang cho tables  
**Props:** currentPage, totalPages, total, perPage  
**Ứng dụng:** Tất cả danh sách có phân trang

### BannerSlider.vue
**Công dụng:** Slider banner với auto-play  
**Props:** slides array  
**Ứng dụng:** HomePage banner quảng cáo

### BackButton.vue
**Công dụng:** Nút quay lại trang trước  
**Props:** text, to  
**Ứng dụng:** QuizPage, Admin detail pages

---

## 📚 `/course` - Components khóa học

### LessonSidebar.vue
**Công dụng:** Sidebar danh sách bài học + bài kiểm tra  
**Props:** lessons, quizzes, currentIndex, hasBaiKiemTra  
**Emits:** select-lesson, start-final-exam  
**Ứng dụng:** CourseLearningPage

### LessonContentItem.vue
**Công dụng:** Render nội dung bài học theo type  
**Props:** item (type, content, data)  
**Types:** heading, paragraph, list, code, image, video, quote  
**Ứng dụng:** CourseLearningPage - loop qua lessonContents

### LessonVideo.vue
**Công dụng:** Video player cho bài học  
**Props:** videoUrl, title  
**Ứng dụng:** LessonContentItem khi type = 'video'

### LessonHeader.vue
**Công dụng:** Header thông tin bài học  
**Props:** title, duration, views, updatedAt  
**Ứng dụng:** CourseLearningPage

### LessonNotes.vue
**Công dụng:** Box highlight ghi chú quan trọng  
**Props:** title, content  
**Ứng dụng:** LessonContentItem khi type = 'note'

### LessonSection.vue
**Công dụng:** Wrapper section cho bài học  
**Props:** title  
**Slots:** default  
**Ứng dụng:** Wrap các phần nội dung

### LessonContent.vue
**Công dụng:** Container chính cho nội dung bài học  
**Slots:** header, content, footer  
**Ứng dụng:** CourseLearningPage layout

---

## 🏠 `/home` - Components trang chủ

### CourseCard.vue
**Công dụng:** Card hiển thị khóa học  
**Props:** course object  
**Emits:** click  
**Ứng dụng:** HomePage danh sách khóa học

### PostCard.vue
**Công dụng:** Card hiển thị bài viết  
**Props:** post object  
**Emits:** click  
**Ứng dụng:** HomePage section bài viết

### RoadmapCard.vue
**Công dụng:** Card hiển thị lộ trình học  
**Props:** roadmap object  
**Emits:** click  
**Ứng dụng:** HomePage section lộ trình

### SectionHeader.vue
**Công dụng:** Header cho sections  
**Props:** title, badge, description, linkText, linkTo  
**Ứng dụng:** HomePage headers

---

## 🎯 `/quiz` - Components bài kiểm tra

### QuizStartScreen.vue
**Công dụng:** Màn hình bắt đầu quiz  
**Props:** quiz info, attempts info  
**Emits:** start-quiz  
**Ứng dụng:** QuizPage trước khi start

### QuizHeader.vue
**Công dụng:** Header trang quiz  
**Props:** quiz, timeRemaining  
**Slots:** controls  
**Ứng dụng:** QuizPage header

### QuizTimer.vue
**Công dụng:** Đếm ngược thời gian  
**Props:** timeRemaining  
**Emits:** time-up  
**Ứng dụng:** QuizHeader controls slot

### QuestionCard.vue
**Công dụng:** Card hiển thị câu hỏi  
**Props:** question, selectedAnswers  
**Emits:** answer-selected  
**Ứng dụng:** QuizPage loop qua questions

### QuestionNavigator.vue
**Công dụng:** Grid navigation câu hỏi  
**Props:** questions, currentIndex, answers  
**Emits:** navigate  
**Ứng dụng:** QuizPage sidebar

### QuizProgress.vue
**Công dụng:** Progress bar quiz  
**Props:** current, total  
**Ứng dụng:** QuizHeader

---

## 🔐 `/modal` - Modal dialogs

### LoginModal.vue
**Công dụng:** Modal đăng nhập  
**Props:** isOpen  
**Emits:** close, login-success  
**Ứng dụng:** Header "Đăng nhập"

### RegisterModal.vue
**Công dụng:** Modal đăng ký  
**Props:** isOpen  
**Emits:** close, register-success  
**Ứng dụng:** Header "Đăng ký"

### ConfirmModal.vue
**Công dụng:** Modal xác nhận action  
**Props:** isOpen, title, message, type  
**Emits:** confirm, cancel  
**Ứng dụng:** Xác nhận xóa, nộp bài

### QuizAddModal.vue
**Công dụng:** Modal tạo bài kiểm tra  
**Props:** modelValue  
**Emits:** save  
**Ứng dụng:** Admin QuizManagement

### FormAddModal.vue
**Công dụng:** Modal generic thêm items  
**Props:** modelValue, fields, title  
**Emits:** save  
**Ứng dụng:** Admin pages

---

## 👨‍💼 `/admin` - Components admin panel

### CourseTable.vue
**Công dụng:** Bảng danh sách khóa học  
**Props:** courses array  
**Emits:** edit, delete, view  
**Ứng dụng:** Admin CourseManagement

### CourseTableRow.vue
**Công dụng:** Row trong CourseTable  
**Props:** course, columns  
**Emits:** edit, delete, view  
**Ứng dụng:** CourseTable loop

### TableActions.vue
**Công dụng:** Action buttons cho table  
**Props:** showView, showEdit, showDelete  
**Emits:** view, edit, delete  
**Ứng dụng:** Tất cả admin tables

### BadgeLabel.vue
**Công dụng:** Badge hiển thị status  
**Props:** type, text  
**Types:** success, warning, danger, info  
**Ứng dụng:** Tables, status indicators

### StatsCard.vue
**Công dụng:** Card thống kê số liệu  
**Props:** icon, label, value, trend, color  
**Ứng dụng:** Admin Dashboard

---

## 📝 `/admin/CourseLesson` - Quản lý bài học

### LessonList.vue
**Công dụng:** Danh sách bài học  
**Props:** courseId, lessons  
**Emits:** edit, delete, add  
**Ứng dụng:** Admin CourseDetail

### LessonItem.vue
**Công dụng:** Item trong LessonList  
**Props:** lesson  
**Emits:** edit, delete  
**Ứng dụng:** LessonList loop

### QuestionBankList.vue
**Công dụng:** Danh sách ngân hàng câu hỏi  
**Props:** questionBanks  
**Emits:** view, edit, delete  
**Ứng dụng:** Admin QuestionBank

### QuestionItem.vue
**Công dụng:** Hiển thị một câu hỏi  
**Props:** question, index  
**Emits:** edit, delete  
**Ứng dụng:** QuestionPage

### QuestionFormModal.vue
**Công dụng:** Form thêm/sửa câu hỏi  
**Props:** modelValue, question, questionBankId  
**Emits:** save, cancel  
**Ứng dụng:** QuestionPage

---

## 🎯 Best Practices

### 1. Props Validation
```vue
defineProps({
  title: {
    type: String,
    required: true
  },
  count: {
    type: Number,
    default: 0
  }
})
```

### 2. Emit Events
```vue
const emit = defineEmits(['update', 'delete'])
emit('update', data)
```

### 3. Slots
```vue
<template>
  <div>
    <slot name="header" />
    <slot /> <!-- default slot -->
    <slot name="footer" />
  </div>
</template>
```

### 4. Composables
```vue
import { useQuizStore } from '@/stores/quiz'
const quizStore = useQuizStore()
```

---

## 📦 Component Naming Convention

- **PascalCase**: `BaseButton.vue`, `CourseCard.vue`
- **Prefix theo module**: 
  - `Base*` - UI components cơ bản
  - `Lesson*` - Components bài học
  - `Quiz*` - Components quiz
  - `Question*` - Components câu hỏi

---

## 🔄 Component Lifecycle

```vue
<script setup>
import { onMounted, onUnmounted } from 'vue'

onMounted(() => {
  // Component mounted
})

onUnmounted(() => {
  // Cleanup
})
</script>
```

---

## 📚 Tài liệu tham khảo

- [Vue 3 Components](https://vuejs.org/guide/essentials/component-basics.html)
- [Props](https://vuejs.org/guide/components/props.html)
- [Events](https://vuejs.org/guide/components/events.html)
- [Slots](https://vuejs.org/guide/components/slots.html)
