import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../views/HomePage.vue'
import CourseLearningPage from '../views/CourseLearningPage.vue'
import QuizPage from '../views/QuizPage.vue'
import FinalExamPage from '../views/FinalExamPage.vue'
import CourseManagementPage from '../views/admin/CourseManagementPage.vue'
import CourseLessonsPage from '../views/admin/CourseLessonsPage.vue'

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
  {
    path: '/quan-ly/khoa-hoc/:id/bai-hoc',
    name: 'CourseLessons',
    component: CourseLessonsPage,
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

// Navigation guard - kiểm tra authentication và roles
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('access_token') // Đúng key là 'access_token'
  const savedUser = localStorage.getItem('user')
  let userRoles = []
  
  if (savedUser) {
    try {
      const userData = JSON.parse(savedUser)
      userRoles = userData.roles || []
    } catch (error) {
      console.error('Lỗi đọc user data:', error)
    }
  }
  
  // Kiểm tra authentication
  if (to.meta.requiresAuth && !token) {
    // Nếu route yêu cầu auth nhưng chưa đăng nhập
    localStorage.setItem('redirectAfterLogin', to.fullPath)
    next({ name: 'Home', query: { login: 'required' } })
    return
  }
  
  // Kiểm tra roles nếu route yêu cầu
  if (to.meta.roles && to.meta.roles.length > 0) {
    const hasPermission = to.meta.roles.some(role => userRoles.includes(role))
    if (!hasPermission) {
      // Không có quyền truy cập
      console.warn('Không có quyền truy cập route:', to.path)
      next({ name: 'Home' })
      return
    }
  }
  
  next()
})

export default router
