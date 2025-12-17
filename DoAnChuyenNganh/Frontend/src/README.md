# Frontend Source Code Structure

## 📁 Cấu trúc thư mục `src/`

### 📄 Files gốc
- **main.js** - Entry point, khởi tạo Vue app, Pinia, Router, Google Login
- **App.vue** - Root component, quản lý layout (Header, Sidebar, Footer)
- **style.css** - Global CSS styles, Tailwind imports

---

## 📂 Thư mục chính

### 🎨 `/components` - Các Vue components tái sử dụng

#### `/components/ui` - UI Components cơ bản
- **BaseButton.vue** - Button component với nhiều variants (primary, secondary, danger...)
- **BaseInput.vue** - Input component với validation
- **SearchInput.vue** - Input tìm kiếm với icon
- **BannerSlider.vue** - Slider banner quảng cáo
- **TablePagination.vue** - Component phân trang cho table
- **BackButton.vue** - Nút quay lại

#### `/components/course` - Components liên quan đến khóa học
- **LessonSidebar.vue** - Sidebar danh sách bài học + bài kiểm tra
- **LessonContentItem.vue** - Hiển thị nội dung bài học (text, video, code...)
- **LessonVideo.vue** - Player video bài học
- **LessonSection.vue** - Section wrapper cho bài học
- **LessonHeader.vue** - Header bài học
- **LessonNotes.vue** - Ghi chú bài học
- **LessonReferences.vue** - Tài liệu tham khảo

#### `/components/home` - Components trang chủ
- **CourseCard.vue** - Card hiển thị khóa học
- **PostCard.vue** - Card hiển thị bài viết
- **RoadmapCard.vue** - Card hiển thị lộ trình học
- **SectionHeader.vue** - Header cho các section

#### `/components/quiz` - Components bài kiểm tra
- Các components liên quan đến quiz system

#### `/components/modal` - Modal dialogs
- **LoginModal.vue** - Modal đăng nhập
- **RegisterModal.vue** - Modal đăng ký
- **ConfirmModal.vue** - Modal xác nhận
- **FormAddModal.vue** - Modal thêm form
- **QuizAddModal.vue** - Modal thêm bài kiểm tra

#### `/components/admin` - Components admin panel
- **CourseTable.vue** - Bảng quản lý khóa học
- **CourseTableRow.vue** - Row trong bảng khóa học
- **TableActions.vue** - Actions cho table
- **BadgeLabel.vue** - Badge hiển thị trạng thái
- `/CourseLesson/` - Components quản lý bài học
- `/statsCards/` - Cards thống kê

---

### 🎭 `/views` - Các trang (pages)

- **HomePage.vue** - Trang chủ, danh sách khóa học
- **CourseLearningPage.vue** - Trang học bài, hiển thị nội dung bài học
- **QuizPage.vue** - Trang làm bài kiểm tra
- **FinalExamPage.vue** - Trang làm bài kiểm tra cuối khóa

#### `/views/admin` - Trang admin
- Các trang quản lý admin (courses, users, quizzes...)

---

### 🔌 `/services` - API Services

- **api.js** - Axios instance, interceptors, base config
- **authService.js** - API authentication (login, register, logout)
- **courseService.js** - API khóa học (get courses, lessons, content)
- **quizService.js** - API bài kiểm tra (start, submit, get results)

**Cách sử dụng:**
```javascript
import { courseService } from '@/services/courseService'
const courses = await courseService.getCourses()
```

---

### 🗄️ `/stores` - Pinia Stores (State Management)

- **auth.js** - Store quản lý authentication (user, token, login/logout)
- **quiz.js** - Store quản lý quiz state (questions, answers, timer)

**Cách sử dụng:**
```javascript
import { useAuthStore } from '@/stores/auth'
const authStore = useAuthStore()
const user = authStore.user
```

---

### 🧩 `/composables` - Vue Composables (Reusable Logic)

- **useGoogleAuth.js** - Logic xử lý Google OAuth login
- `/quiz/` - Composables cho quiz system

**Cách sử dụng:**
```javascript
import { useGoogleAuth } from '@/composables/useGoogleAuth'
const { login, logout } = useGoogleAuth()
```

---

### 🗺️ `/router` - Vue Router Configuration

- **index.js** - Định nghĩa routes, navigation guards

**Routes chính:**
- `/` - HomePage
- `/khoa-hoc/:id/hoc` - CourseLearningPage
- `/quiz/:id` - QuizPage
- `/admin/*` - Admin pages

---

### 🎨 `/layouts` - Layout Components

- **DefaultLayout.vue** - Layout mặc định
- `/partials/Header.vue` - Header component
- `/partials/Sidebar.vue` - Sidebar component
- `/partials/Footer.vue` - Footer component

---

### 🖼️ `/assets` - Static Assets

- Images, icons, fonts...

---

## 🔄 Luồng hoạt động chính

### 1. Khởi động ứng dụng
```
main.js → App.vue → Router → Views
```

### 2. Authentication Flow
```
LoginModal → authService.login() → authStore.setUser() → localStorage
```

### 3. Course Learning Flow
```
HomePage → CourseCard click → CourseLearningPage
→ Load lessons → LessonSidebar → LessonContentItem
```

### 4. Quiz Flow
```
CourseLearningPage → Click "Làm bài kiểm tra"
→ Check số lượt (quizService.checkQuizBeforeStart)
→ QuizPage → Start quiz (quizService.startQuiz)
→ Answer questions → Submit (quizService.submitQuiz)
→ View results
```

---

## 📝 Quy tắc đặt tên

### Components
- **PascalCase**: `BaseButton.vue`, `CourseCard.vue`
- **Prefix theo chức năng**: `Base*` (UI), `Lesson*` (Course), `Quiz*` (Quiz)

### Services
- **camelCase**: `authService.js`, `courseService.js`
- **Export named**: `export const courseService = { ... }`

### Stores
- **camelCase**: `auth.js`, `quiz.js`
- **Use prefix**: `useAuthStore`, `useQuizStore`

### Composables
- **camelCase với prefix `use`**: `useGoogleAuth.js`

---

## 🎯 Best Practices

1. **Components nhỏ, tái sử dụng**: Tách logic phức tạp thành components nhỏ
2. **Props validation**: Luôn validate props với type và default value
3. **Emit events**: Sử dụng emit thay vì modify props trực tiếp
4. **Composables cho logic**: Tách logic tái sử dụng vào composables
5. **Services cho API**: Tất cả API calls phải qua services
6. **Stores cho global state**: Chỉ dùng stores khi cần share state giữa nhiều components
7. **Comments**: Thêm JSDoc comments cho functions và components quan trọng

---

## 🔧 Tools & Libraries

- **Vue 3** - Framework
- **Vite** - Build tool
- **Pinia** - State management
- **Vue Router** - Routing
- **Axios** - HTTP client
- **Tailwind CSS** - Styling
- **vue3-google-login** - Google OAuth

---

## 📚 Tài liệu tham khảo

- [Vue 3 Docs](https://vuejs.org/)
- [Pinia Docs](https://pinia.vuejs.org/)
- [Vue Router Docs](https://router.vuejs.org/)
- [Tailwind CSS Docs](https://tailwindcss.com/)
