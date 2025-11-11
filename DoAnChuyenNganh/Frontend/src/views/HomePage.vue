<template>
  <div class="w-full">
    <!-- Row 1: Banner Slider - Quảng cáo khóa học -->
    <section class="w-full px-6 py-8 bg-gray-50">
      <div class="max-w-7xl mx-auto">
        <BannerSlider />
      </div>
    </section>

    <!-- Row 2: Khóa học phổ biến -->
    <section class="w-full px-6 py-12 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Khóa học phổ biến</h2>
            <p class="text-gray-600">Các khóa học được yêu thích nhất</p>
          </div>
          <a href="#" class="text-primary hover:text-primary-600 font-semibold flex items-center gap-2">
            Xem tất cả
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </a>
        </div>

        <!-- Grid khóa học -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div 
            v-for="course in popularCourses" 
            :key="course.id"
            class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
          >
            <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
              <span class="text-white text-4xl font-bold">{{ course.icon }}</span>
            </div>
            <div class="p-4">
              <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">{{ course.title }}</h3>
              <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ course.description }}</p>
              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">{{ course.students }} học viên</span>
                <span class="text-sm font-bold text-primary">{{ course.price }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Row 3: Lộ trình học tập -->
    <section class="w-full px-6 py-12 bg-gray-50">
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
    <section class="w-full px-6 py-12 bg-white">
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
import BannerSlider from '../components/BannerSlider.vue'

// Dữ liệu mẫu - Sẽ thay thế bằng API call
const popularCourses = ref([
  {
    id: 1,
    icon: '🚀',
    title: 'JavaScript từ Cơ bản đến Nâng cao',
    description: 'Khóa học JavaScript toàn diện cho người mới bắt đầu',
    students: '1,234',
    price: 'Miễn phí'
  },
  {
    id: 2,
    icon: '⚡',
    title: 'Vue.js 3 Composition API',
    description: 'Xây dựng ứng dụng web hiện đại với Vue 3',
    students: '856',
    price: '499,000đ'
  },
  {
    id: 3,
    icon: '🎯',
    title: 'Python cho Data Science',
    description: 'Phân tích dữ liệu và Machine Learning với Python',
    students: '2,105',
    price: '699,000đ'
  },
  {
    id: 4,
    icon: '💻',
    title: 'React Native - Lập trình Mobile',
    description: 'Phát triển ứng dụng di động đa nền tảng',
    students: '645',
    price: 'Miễn phí'
  }
])

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
