# Tích hợp Đăng nhập với Backend

## Tổng quan
Đã tích hợp thành công form đăng nhập với backend Laravel API. Khi người dùng nhập email và mật khẩu đúng, hệ thống sẽ:
1. Gửi thông tin đăng nhập đến backend
2. Xác thực quyền STUDENT
3. Lưu token và thông tin người dùng
4. Cập nhật giao diện hiển thị trạng thái đăng nhập

## Các thay đổi đã thực hiện

### 1. LoginModal.vue
**Thêm props mới:**
- `loading`: Boolean - hiển thị trạng thái đang xử lý
- `error`: String - hiển thị lỗi từ server

**Thay đổi hành vi:**
- Không tự động đóng modal sau khi submit
- Hiển thị thông báo lỗi từ backend
- Vô hiệu hóa nút submit khi đang xử lý
- Reset form khi đóng modal

### 2. Header.vue
**Import authService:**
```javascript
import { authService } from '../../services/courseService.js'
```

**State mới:**
```javascript
const loginLoading = ref(false)
const loginError = ref('')
```

**handleLoginSubmit - Tích hợp API:**
```javascript
const handleLoginSubmit = async (data) => {
  loginLoading.value = true
  loginError.value = ''
  
  try {
    // Gọi API backend
    const response = await authService.login(data.email, data.password)
    
    // Kiểm tra role STUDENT
    if (!response.user.roles.includes('STUDENT')) {
      loginError.value = 'Không phải học viên'
      return
    }
    
    // Lưu token và user vào localStorage
    localStorage.setItem('access_token', response.token)
    localStorage.setItem('user', JSON.stringify(response.user))
    
    // Cập nhật UI
    isLoggedIn.value = true
    currentUser.value = {
      name: response.user.name,
      email: response.user.email,
      avatar: 'https://i.pravatar.cc/150?img=12'
    }
    
    // Đóng modal
    showLoginModal.value = false
    
  } catch (error) {
    // Xử lý lỗi
    loginError.value = error.message || 'Đăng nhập thất bại'
  } finally {
    loginLoading.value = false
  }
}
```

**handleLogout - Tích hợp API:**
```javascript
const handleLogout = async () => {
  try {
    await authService.logout()
  } catch (error) {
    console.error('Lỗi logout:', error)
  } finally {
    // Xóa local state
    isLoggedIn.value = false
    showUserMenu.value = false
    currentUser.value = null
    
    // Xóa localStorage
    localStorage.removeItem('access_token')
    localStorage.removeItem('user')
  }
}
```

**Khôi phục phiên đăng nhập:**
```javascript
onMounted(() => {
  const token = localStorage.getItem('access_token')
  const savedUser = localStorage.getItem('user')
  
  if (token && savedUser) {
    const userData = JSON.parse(savedUser)
    isLoggedIn.value = true
    currentUser.value = {
      name: userData.name,
      email: userData.email,
      avatar: 'https://i.pravatar.cc/150?img=12'
    }
  }
})
```

## Cách hoạt động

### Luồng đăng nhập:
1. User nhập email & password vào LoginModal
2. Click "Đăng nhập"
3. Modal emit sự kiện `submit` với data
4. `handleLoginSubmit` nhận data và gọi `authService.login()`
5. Backend kiểm tra:
   - Email có tồn tại không
   - Mật khẩu có đúng không
   - User có role STUDENT không
6. Backend trả về:
   ```json
   {
     "message": "Đăng nhập thành công",
     "user": {
       "id": 1,
       "name": "Nguyễn Văn A",
       "email": "student@example.com",
       "roles": ["STUDENT"]
     },
     "token": "1|abc123xyz..."
   }
   ```
7. Frontend:
   - Lưu token vào `localStorage.access_token`
   - Lưu user vào `localStorage.user`
   - Đặt `isLoggedIn = true`
   - Cập nhật `currentUser` với thông tin từ backend
   - Đóng modal
   - Header tự động hiển thị avatar/menu thay vì nút đăng nhập/đăng ký

### Xử lý lỗi:
- **Email/password sai**: Backend trả 401, frontend hiển thị "Email hoặc mật khẩu sai"
- **Không phải STUDENT**: Backend trả 403, frontend hiển thị "Không phải học viên"
- **Lỗi mạng**: Frontend hiển thị "Lỗi kết nối máy chủ"

### Khôi phục phiên:
- Khi load trang, kiểm tra `localStorage`
- Nếu có token và user data, tự động đăng nhập lại
- Axios interceptor tự động thêm token vào mọi request

## API Backend

### Endpoint: POST /api/auth/login
**Request:**
```json
{
  "email": "student@example.com",
  "password": "123456"
}
```

**Response thành công (200):**
```json
{
  "message": "Đăng nhập thành công",
  "user": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "email": "student@example.com",
    "roles": ["STUDENT"]
  },
  "token": "1|abc123xyz..."
}
```

**Response lỗi:**
- 401: Email hoặc mật khẩu sai
- 403: Không phải học viên
- 422: Validation error

## Test thử nghiệm

### Để test:
1. Đảm bảo backend Laravel đang chạy: `php artisan serve`
2. Đảm bảo frontend đang chạy: `npm run dev`
3. Click "Đăng nhập" ở header
4. Nhập email và password của một user có role STUDENT trong database
5. Click "Đăng nhập"
6. Kiểm tra:
   - Modal đóng lại
   - Nút "Đăng nhập" & "Đăng ký" biến mất
   - Avatar và tên user xuất hiện
   - Console log hiển thị thông tin user
   - localStorage chứa token và user data

### Test logout:
1. Click vào avatar
2. Click "Đăng xuất"
3. Kiểm tra:
   - Avatar biến mất
   - Nút "Đăng nhập" & "Đăng ký" xuất hiện lại
   - localStorage.access_token bị xóa
   - localStorage.user bị xóa

## Lưu ý

1. **Bảo mật**: Token được lưu trong localStorage (có thể bị XSS). Cân nhắc dùng httpOnly cookie cho production.

2. **Token expiration**: Hiện tại chưa xử lý token hết hạn tự động. Axios interceptor sẽ redirect về /login khi gặp lỗi 401.

3. **Avatar**: Hiện dùng placeholder `https://i.pravatar.cc/150?img=12`. Backend cần trả về URL avatar thật.

4. **Validation**: Backend cần hash password bằng bcrypt. Hiện đang so sánh plain text.

5. **Remember me**: Checkbox "Ghi nhớ đăng nhập" đã được gửi lên nhưng backend chưa xử lý (có thể dùng để set token expiration khác nhau).
