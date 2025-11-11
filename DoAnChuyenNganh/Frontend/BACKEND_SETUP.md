# Frontend - Kỹ năng lập trình

## 🔧 Cấu hình

### 1. Cài đặt dependencies
```bash
npm install
```

### 2. Cấu hình Environment Variables
Copy file `.env.example` thành `.env` và cập nhật:
```env
VITE_API_BASE_URL=http://localhost:8000/api
```

### 3. Chạy development server
```bash
npm run dev
```

## 🌐 Kết nối Backend MySQL

### API Endpoints
Backend API phải chạy tại: `http://localhost:8000/api`

### Cấu trúc API endpoints:

#### Authentication
- `POST /api/auth/register` - Đăng ký
- `POST /api/auth/login` - Đăng nhập
- `POST /api/auth/logout` - Đăng xuất
- `GET /api/auth/me` - Lấy thông tin user hiện tại

#### Courses
- `GET /api/courses` - Lấy danh sách khóa học (có phân trang)
- `GET /api/courses/:id` - Chi tiết khóa học
- `GET /api/courses/category/:category` - Lọc theo category
- `GET /api/courses/level/:level` - Lọc theo level
- `GET /api/courses/search?q=keyword` - Tìm kiếm
- `GET /api/courses/popular` - Khóa học phổ biến
- `GET /api/courses/latest` - Khóa học mới nhất
- `POST /api/courses/:id/enroll` - Đăng ký khóa học

#### Roadmaps
- `GET /api/roadmaps` - Danh sách lộ trình
- `GET /api/roadmaps/:id` - Chi tiết lộ trình
- `GET /api/roadmaps/:id/courses` - Khóa học trong lộ trình

#### Posts
- `GET /api/posts` - Danh sách bài viết
- `GET /api/posts/:id` - Chi tiết bài viết
- `GET /api/posts/category/:category` - Lọc theo category
- `GET /api/posts/search?q=keyword` - Tìm kiếm bài viết

#### User
- `GET /api/user/profile` - Thông tin profile
- `PUT /api/user/profile` - Cập nhật profile
- `POST /api/user/change-password` - Đổi mật khẩu
- `POST /api/user/avatar` - Upload avatar

## 📦 Services

### Sử dụng trong component:
```vue
<script setup>
import { ref, onMounted } from 'vue'
import { courseService } from '@/services/courseService'

const courses = ref([])
const loading = ref(false)

const fetchCourses = async () => {
  loading.value = true
  try {
    const response = await courseService.getAll()
    courses.value = response.data
  } catch (error) {
    console.error('Error:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCourses()
})
</script>
```

## 🔐 Authentication

Token được lưu trong `localStorage`:
- Key: `access_token`
- Tự động thêm vào header: `Authorization: Bearer {token}`

Khi token hết hạn (401), sẽ tự động redirect về trang login.

## 🎨 Tech Stack

- **Vue 3** - Framework
- **Vite** - Build tool
- **Tailwind CSS** - Styling
- **Axios** - HTTP client
- **Pinia** - State management (sẽ setup sau)
- **Vue Router** - Routing (sẽ setup sau)

## 📝 Lưu ý

- Backend phải chạy trước khi start frontend
- Đảm bảo CORS được cấu hình đúng ở backend
- API response format nên có dạng:
  ```json
  {
    "success": true,
    "data": {...},
    "message": "Success"
  }
  ```
