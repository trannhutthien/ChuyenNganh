import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../views/HomePage.vue'
import CourseLearningPage from '../views/CourseLearningPage.vue'
import QuizPage from '../views/QuizPage.vue'
import FinalExamPage from '../views/FinalExamPage.vue'
import CourseManagementPage from '../views/admin/CourseManagementPage.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage
  },
  {
    path: '/learn/:courseId',
    name: 'CourseLearning',
    component: CourseLearningPage,
    props: true
  },
  {
    path: '/quiz/:quizId',
    name: 'Quiz',
    component: QuizPage,
    props: true
  },
  {
    path: '/course/:courseId/final-exam',
    name: 'FinalExam',
    component: FinalExamPage,
    props: true,
    meta: { requiresAuth: true }
  },
  // Admin routes
  {
    path: '/quan-ly/khoa-hoc',
    name: 'CourseManagement',
    component: CourseManagementPage,
    meta: { requiresAuth: true, roles: ['ADMIN', 'EDITOR'] }
  },
  // Catch-all route - redirect về trang chủ nếu không tìm thấy route
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard - kiểm tra authentication
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  
  if (to.meta.requiresAuth && !token) {
    // Nếu route yêu cầu auth nhưng chưa đăng nhập
    // Lưu route muốn đến để redirect sau khi đăng nhập
    localStorage.setItem('redirectAfterLogin', to.fullPath)
    // Redirect về trang chủ (sẽ hiện modal đăng nhập)
    next({ name: 'Home', query: { login: 'required' } })
  } else {
    next()
  }
})

export default router
