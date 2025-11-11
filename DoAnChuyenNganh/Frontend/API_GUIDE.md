# JSON Server - Mock API Documentation

## 📦 Đã cài đặt
- json-server: Mock REST API
- concurrently: Chạy nhiều script đồng thời

## 🚀 Cách chạy

### Chỉ chạy API Server:
```bash
npm run api
```
API sẽ chạy tại: `http://localhost:3000`

### Chạy cả Frontend + API:
```bash
npm run dev:all
```
- Frontend: `http://localhost:5173` (hoặc 5174)
- API: `http://localhost:3000`

## 📚 Endpoints

### Courses (Khóa học)
- GET `/courses` - Lấy tất cả khóa học
- GET `/courses/:id` - Lấy khóa học theo ID
- GET `/courses?category=Frontend` - Filter theo category
- GET `/courses?level=Beginner` - Filter theo level
- GET `/courses?q=javascript` - Tìm kiếm
- POST `/courses` - Tạo khóa học mới
- PUT `/courses/:id` - Cập nhật khóa học
- DELETE `/courses/:id` - Xóa khóa học

### Roadmaps (Lộ trình)
- GET `/roadmaps` - Lấy tất cả lộ trình
- GET `/roadmaps/:id` - Lấy lộ trình theo ID

### Posts (Bài viết)
- GET `/posts` - Lấy tất cả bài viết
- GET `/posts/:id` - Lấy bài viết theo ID
- GET `/posts?category=JavaScript` - Filter theo category
- GET `/posts?q=keyword` - Tìm kiếm

### Users (Người dùng)
- GET `/users` - Lấy tất cả users
- GET `/users/:id` - Lấy user theo ID
- POST `/users` - Tạo user mới (đăng ký)

## 🔧 Sử dụng Service trong Component

### Ví dụ 1: Lấy danh sách khóa học
```vue
<script setup>
import { ref, onMounted } from 'vue'
import { courseService } from '@/services/courseService'

const courses = ref([])
const loading = ref(false)
const error = ref(null)

const fetchCourses = async () => {
  loading.value = true
  try {
    courses.value = await courseService.getAll()
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCourses()
})
</script>
```

### Ví dụ 2: Tìm kiếm khóa học
```vue
<script setup>
import { ref } from 'vue'
import { courseService } from '@/services/courseService'

const searchQuery = ref('')
const searchResults = ref([])

const handleSearch = async () => {
  if (searchQuery.value.trim()) {
    searchResults.value = await courseService.search(searchQuery.value)
  }
}
</script>
```

### Ví dụ 3: Lấy khóa học theo category
```vue
<script setup>
import { courseService } from '@/services/courseService'

const frontendCourses = ref([])

const loadFrontendCourses = async () => {
  frontendCourses.value = await courseService.getByCategory('Frontend')
}
</script>
```

## 🎯 Features của JSON Server

1. **Pagination**: `GET /courses?_page=1&_limit=10`
2. **Sorting**: `GET /courses?_sort=price&_order=asc`
3. **Filter**: `GET /courses?category=Frontend&level=Beginner`
4. **Search**: `GET /courses?q=javascript`
5. **Relationships**: `GET /roadmaps/1?_embed=courses`

## 💾 Database
File `db.json` chứa dữ liệu mẫu:
- 8 khóa học (courses)
- 4 lộ trình (roadmaps)
- 3 bài viết (posts)
- 1 user mẫu

## 🔄 Khi nào chuyển sang Backend thật?
Khi có backend thật, chỉ cần:
1. Đổi `baseURL` trong `src/services/api.js`
2. Thêm authentication token nếu cần
3. Các service function vẫn hoạt động như cũ!

## 📝 Lưu ý
- JSON Server tự động lưu thay đổi vào `db.json`
- Restart server nếu sửa file `db.json` thủ công
- Dữ liệu sẽ reset nếu bạn restore lại file `db.json`
