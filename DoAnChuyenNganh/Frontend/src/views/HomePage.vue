<template>
  <div class="w-full">
    <!-- Row 1: Banner Slider - Quảng cáo khóa học -->
    <section class="w-full px-6 py-8 bg-gray-50">
      <div class="max-w-7xl mx-auto">
        <BannerSlider />
      </div>
    </section>

    <!-- Row 2: Khóa học Pro (Có phí) -->
    <section class="w-full px-6 py-12 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <h2 class="text-3xl font-bold text-gray-800">Khóa học Pro</h2>
              <span class="px-3 py-1 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-sm font-semibold rounded-full">
                ⭐ Premium
              </span>
            </div>
            <p class="text-gray-600">Khóa học chuyên sâu, chất lượng cao</p>
          </div>
          <a href="#" class="text-primary hover:text-primary-600 font-semibold flex items-center gap-2">
            Xem tất cả
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </a>
        </div>

        <!-- Grid khóa học Pro -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div 
            v-for="course in proCourses" 
            :key="course.id"
            class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden hover:shadow-xl hover:border-primary transition-all cursor-pointer group"
          >
            <!-- Thumbnail -->
            <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center relative overflow-hidden">
              <span class="text-white text-4xl font-bold group-hover:scale-110 transition-transform">{{ course.icon }}</span>
              <!-- Badge Pro -->
              <div class="absolute top-3 right-3 px-2 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-md">
                PRO
              </div>
            </div>
            
            <div class="p-4">
              <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">{{ course.title }}</h3>
              <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ course.description }}</p>
              
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs text-gray-500 flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                  </svg>
                  {{ course.students }} học viên
                </span>
                <!-- Rating stars -->
                <div class="flex items-center gap-1">
                  <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-400">
                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
              
              <!-- Price Section - Logic render giá -->
              <div class="pt-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                  <!-- Kiểm tra nếu có giá gốc (originalPrice) thì hiển thị giá đã giảm -->
                  <div v-if="course.originalPrice" class="flex flex-col">
                    <span class="text-xs text-gray-400 line-through">{{ formatPrice(course.originalPrice) }}</span>
                    <span class="text-lg font-bold text-primary">{{ formatPrice(course.price) }}</span>
                  </div>
                  <!-- Nếu không có giá gốc, chỉ hiển thị giá hiện tại -->
                  <div v-else>
                    <span class="text-lg font-bold text-primary">{{ formatPrice(course.price) }}</span>
                  </div>
                  
                  <!-- Discount badge nếu có giảm giá -->
                  <span v-if="course.originalPrice" class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">
                    -{{ calculateDiscount(course.originalPrice, course.price) }}%
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Row 3: Khóa học Cơ bản (Miễn phí) -->
    <section class="w-full px-6 py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <h2 class="text-3xl font-bold text-gray-800">Khóa học Cơ bản</h2>
              <span class="px-3 py-1 bg-green-500 text-white text-sm font-semibold rounded-full">
                🎁 Miễn phí
              </span>
            </div>
            <p class="text-gray-600">Học lập trình hoàn toàn miễn phí</p>
          </div>
          <a href="#" class="text-primary hover:text-primary-600 font-semibold flex items-center gap-2">
            Xem tất cả
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </a>
        </div>

        <!-- Grid khóa học miễn phí -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div 
            v-for="course in freeCourses" 
            :key="course.id"
            class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow cursor-pointer group"
          >
            <!-- Thumbnail -->
            <div class="h-48 bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center relative overflow-hidden">
              <span class="text-white text-4xl font-bold group-hover:scale-110 transition-transform">{{ course.icon }}</span>
              <!-- Badge Free -->
              <div class="absolute top-3 right-3 px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-md">
                FREE
              </div>
            </div>
            
            <div class="p-4">
              <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">{{ course.title }}</h3>
              <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ course.description }}</p>
              
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs text-gray-500 flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                  </svg>
                  {{ course.students }} học viên
                </span>
                <div class="flex items-center gap-1">
                  <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-400">
                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
              
              <!-- Price Section -->
              <div class="pt-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                  <!-- Logic: Nếu price = 0 hoặc null thì hiển thị "Miễn phí", ngược lại hiển thị giá -->
                  <span v-if="!course.price || course.price === 0" class="text-lg font-bold text-green-600">
                    Miễn phí
                  </span>
                  <div v-else>
                    <!-- Nếu có giá gốc (chuyển từ free sang pro) -->
                    <div v-if="course.originalPrice" class="flex flex-col">
                      <span class="text-xs text-gray-400 line-through">{{ formatPrice(course.originalPrice) }}</span>
                      <span class="text-lg font-bold text-primary">{{ formatPrice(course.price) }}</span>
                    </div>
                    <!-- Nếu chỉ có giá mới -->
                    <span v-else class="text-lg font-bold text-primary">{{ formatPrice(course.price) }}</span>
                  </div>
                  
                  <BaseButton 
                    @click="startLearning(course.id)"
                    variant="primary"
                    size="sm"
                  >
                    Học ngay
                  </BaseButton>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Row 3: Lộ trình học tập -->
    <section class="w-full px-6 py-12 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Lộ trình học tập</h2>
            <p class="text-gray-600">Định hướng phát triển sự nghiệp</p>
          </div>
          <a href="#" class="text-primary hover:text-primary-600 font-semibold flex items-center gap-2">
            Xem tất cả
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </a>
        </div>

        <!-- Grid lộ trình -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div 
            v-for="roadmap in roadmaps" 
            :key="roadmap.id"
            class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-primary transition-colors cursor-pointer"
          >
            <div class="w-16 h-16 bg-gradient-to-br from-primary to-purple-500 rounded-xl flex items-center justify-center mb-4">
              <span class="text-white text-3xl">{{ roadmap.icon }}</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ roadmap.title }}</h3>
            <p class="text-gray-600 mb-4">{{ roadmap.description }}</p>
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
              </svg>
              {{ roadmap.courses }} khóa học
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Row 4: Bài viết mới nhất -->
    <section class="w-full px-6 py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Bài viết mới nhất</h2>
            <p class="text-gray-600">Chia sẻ kiến thức và kinh nghiệm</p>
          </div>
          <a href="#" class="text-primary hover:text-primary-600 font-semibold flex items-center gap-2">
            Xem tất cả
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </a>
        </div>

        <!-- Grid bài viết -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="post in posts" 
            :key="post.id"
            class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
          >
            <div class="h-48 bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
              <span class="text-white text-5xl">📝</span>
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 bg-primary/10 text-primary text-xs rounded-full font-semibold">
                  {{ post.category }}
                </span>
                <span class="text-xs text-gray-500">{{ post.date }}</span>
              </div>
              <h3 class="font-bold text-gray-800 mb-2 line-clamp-2">{{ post.title }}</h3>
              <p class="text-sm text-gray-600 line-clamp-3 mb-4">{{ post.excerpt }}</p>
              <div class="flex items-center gap-2 text-sm text-gray-500">
                <img :src="post.author.avatar" :alt="post.author.name" class="w-6 h-6 rounded-full">
                <span>{{ post.author.name }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BannerSlider from '../components/ui/BannerSlider.vue'
import BaseButton from '../components/ui/BaseButton.vue'

const router = useRouter()

// ========== KHÓA HỌC PRO (CÓ PHÍ) ==========
const proCourses = ref([
  {
    id: 1,
    icon: '⚡',
    title: 'Vue.js 3 Composition API - Master Class',
    description: 'Khóa học Vue.js 3 chuyên sâu từ cơ bản đến nâng cao, dự án thực tế',
    students: '2,456',
    price: 799000,           // Giá hiện tại
    originalPrice: 1499000   // Giá gốc (để hiển thị giảm giá)
  },
  {
    id: 2,
    icon: '🚀',
    title: 'Node.js & Express - Backend Master',
    description: 'Xây dựng RESTful API chuyên nghiệp với Node.js, MongoDB',
    students: '1,823',
    price: 999000,
    originalPrice: 1799000
  },
  {
    id: 3,
    icon: '🎯',
    title: 'React Native - Mobile App Development',
    description: 'Phát triển ứng dụng di động đa nền tảng với React Native',
    students: '1,567',
    price: 1299000,
    originalPrice: null     // Không có giá gốc = không giảm giá
  },
  {
    id: 4,
    icon: '💎',
    title: 'Full-stack MEVN - Dự án thực tế',
    description: 'MongoDB, Express, Vue.js, Node.js - Xây dựng ứng dụng hoàn chỉnh',
    students: '945',
    price: 1599000,
    originalPrice: 2499000
  }
])

// ========== KHÓA HỌC CƠ BẢN (MIỄN PHÍ) ==========
const freeCourses = ref([
  {
    id: 5,
    icon: '📘',
    title: 'HTML & CSS Cơ bản',
    description: 'Bắt đầu với HTML5 và CSS3, xây dựng trang web đầu tiên',
    students: '5,234',
    price: 0,              // price = 0 nghĩa là miễn phí
    originalPrice: null
  },
  {
    id: 6,
    icon: '�',
    title: 'JavaScript Căn bản',
    description: 'Nền tảng JavaScript từ đầu, biến, hàm, vòng lặp, DOM',
    students: '4,567',
    price: null,           // price = null cũng nghĩa là miễn phí
    originalPrice: null
  },
  {
    id: 7,
    icon: '🐍',
    title: 'Python Cho Người Mới Bắt Đầu',
    description: 'Học Python từ con số 0, cú pháp cơ bản và thực hành',
    students: '3,891',
    price: 0,
    originalPrice: null
  },
  {
    id: 8,
    icon: '🎨',
    title: 'Thiết kế UI/UX với Figma',
    description: 'Tạo giao diện đẹp mắt với công cụ Figma miễn phí',
    students: '2,345',
    price: 0,
    originalPrice: null
  }
])

// ========== VÍ DỤ: Chuyển khóa học từ FREE sang PRO ==========
// Nếu muốn chuyển khóa "HTML & CSS Cơ bản" từ miễn phí sang pro với giá 299,000đ:
// 
// const upgradedCourse = {
//   ...freeCourses.value[0],  // Lấy thông tin khóa học hiện tại
//   price: 299000,             // Đặt giá mới
//   originalPrice: 599000      // (Optional) Đặt giá gốc nếu muốn hiển thị giảm giá
// }
// proCourses.value.push(upgradedCourse)  // Thêm vào danh sách Pro
// freeCourses.value.splice(0, 1)         // Xóa khỏi danh sách Free

// ========== FUNCTIONS ==========
/**
 * Bắt đầu học khóa học
 * @param {number} courseId - ID của khóa học
 */
const startLearning = (courseId) => {
  // Chuyển hướng đến trang học với courseId
  router.push({ name: 'CourseLearning', params: { courseId } })
}

/**
 * Format giá tiền theo định dạng Việt Nam
 * @param {number} price - Giá tiền
 * @returns {string} - Giá đã format (VD: "799,000đ")
 */
const formatPrice = (price) => {
  if (!price || price === 0) return 'Miễn phí'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}

/**
 * Tính phần trăm giảm giá
 * @param {number} originalPrice - Giá gốc
 * @param {number} currentPrice - Giá hiện tại
 * @returns {number} - Phần trăm giảm
 */
const calculateDiscount = (originalPrice, currentPrice) => {
  if (!originalPrice || !currentPrice) return 0
  return Math.round(((originalPrice - currentPrice) / originalPrice) * 100)
}

// Dữ liệu mẫu - Sẽ thay thế bằng API call

const roadmaps = ref([
  {
    id: 1,
    icon: '🎨',
    title: 'Frontend Developer',
    description: 'Lộ trình trở thành Frontend Developer chuyên nghiệp',
    courses: 12
  },
  {
    id: 2,
    icon: '⚙️',
    title: 'Backend Developer',
    description: 'Xây dựng hệ thống backend mạnh mẽ và bảo mật',
    courses: 15
  },
  {
    id: 3,
    icon: '📱',
    title: 'Mobile Developer',
    description: 'Phát triển ứng dụng di động iOS và Android',
    courses: 10
  }
])

const posts = ref([
  {
    id: 1,
    title: '10 Mẹo tối ưu hiệu suất Vue.js',
    excerpt: 'Những kỹ thuật giúp ứng dụng Vue.js của bạn chạy nhanh hơn và hiệu quả hơn',
    category: 'Vue.js',
    date: '5 ngày trước',
    author: {
      name: 'Nguyễn Văn A',
      avatar: 'https://i.pravatar.cc/150?img=1'
    }
  },
  {
    id: 2,
    title: 'Cách sử dụng Async/Await trong JavaScript',
    excerpt: 'Hiểu rõ về bất đồng bộ trong JavaScript và cách sử dụng async/await',
    category: 'JavaScript',
    date: '1 tuần trước',
    author: {
      name: 'Trần Thị B',
      avatar: 'https://i.pravatar.cc/150?img=2'
    }
  },
  {
    id: 3,
    title: 'REST API vs GraphQL: Nên chọn gì?',
    excerpt: 'So sánh chi tiết giữa REST API và GraphQL để lựa chọn phù hợp',
    category: 'Backend',
    date: '2 tuần trước',
    author: {
      name: 'Lê Văn C',
      avatar: 'https://i.pravatar.cc/150?img=3'
    }
  }
])
</script>
