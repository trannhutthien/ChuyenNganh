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
        <SectionHeader 
          title="Khóa học Pro"
          description="Khóa học chuyên sâu, chất lượng cao"
          badge="Premium"
          badge-type="premium"
          view-all-link="#"
        />

        <!-- Grid khóa học Pro -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <CourseCard 
            v-for="course in proCourses" 
            :key="course.id"
            :course="course"
            type="pro"
            @click="startLearning(course.id)"
          />
        </div>
      </div>
    </section>

    <!-- Row 3: Khóa học Cơ bản (Miễn phí) -->
    <section class="w-full px-6 py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto">
        <SectionHeader 
          title="Khóa học Cơ bản"
          description="Học lập trình hoàn toàn miễn phí"
          badge="Miễn phí"
          badge-type="free"
          view-all-link="#"
        />

        <!-- Grid khóa học miễn phí -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <CourseCard 
            v-for="course in freeCourses" 
            :key="course.id"
            :course="course"
            type="free"
            @click="startLearning(course.id)"
          />
        </div>
      </div>
    </section>

    <!-- Row 4: Lộ trình học tập -->
    <section class="w-full px-6 py-12 bg-white">
      <div class="max-w-7xl mx-auto">
        <SectionHeader 
          title="Lộ trình học tập"
          description="Định hướng phát triển sự nghiệp"
          :show-view-all="true"
          view-all-link="#"
        />

        <!-- Grid lộ trình -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <RoadmapCard 
            v-for="roadmap in roadmaps" 
            :key="roadmap.id"
            :roadmap="roadmap"
          />
        </div>
      </div>
    </section>

    <!-- Row 5: Bài viết mới nhất -->
    <section class="w-full px-6 py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto">
        <SectionHeader 
          title="Bài viết mới nhất"
          description="Chia sẻ kiến thức và kinh nghiệm"
          :show-view-all="true"
          view-all-link="#"
        />

        <!-- Grid bài viết -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <PostCard 
            v-for="post in posts" 
            :key="post.id"
            :post="post"
          />
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BannerSlider from '../components/ui/BannerSlider.vue'
import SectionHeader from '../components/home/SectionHeader.vue'
import CourseCard from '../components/home/CourseCard.vue'
import RoadmapCard from '../components/home/RoadmapCard.vue'
import PostCard from '../components/home/PostCard.vue'

const router = useRouter()

// ========== FUNCTIONS ==========
/**
 * Bắt đầu học khóa học
 * @param {number} courseId - ID của khóa học
 */
const startLearning = (courseId) => {
  router.push({ name: 'CourseLearning', params: { courseId } })
}

// ========== KHÓA HỌC PRO (CÓ PHÍ) ==========
const proCourses = ref([
  {
    id: 1,
    icon: '⚡',
    title: 'Vue.js 3 Composition API - Master Class',
    description: 'Khóa học Vue.js 3 chuyên sâu từ cơ bản đến nâng cao, dự án thực tế',
    students: '2,456',
    price: 799000,
    originalPrice: 1499000
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
    originalPrice: null
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
    price: 0,
    originalPrice: null
  },
  {
    id: 6,
    icon: '💛',
    title: 'JavaScript Căn bản',
    description: 'Nền tảng JavaScript từ đầu, biến, hàm, vòng lặp, DOM',
    students: '4,567',
    price: null,
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

// ========== LỘ TRÌNH HỌC TẬP ==========
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

// ========== BÀI VIẾT MỚI NHẤT ==========
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
