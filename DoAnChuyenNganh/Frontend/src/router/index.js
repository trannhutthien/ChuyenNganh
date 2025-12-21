import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../views/HomePage.vue'
import CourseLearningPage from '../views/CourseLearningPage.vue'
import QuizPage from '../views/QuizPage.vue'
import FinalExamPage from '../views/FinalExamPage.vue'
import RoadmapListPage from '../views/RoadmapListPage.vue'
import RoadmapDetailPage from '../components/map/RoadmapDetailPage.vue'
import CourseManagementPage from '../views/admin/CourseManagementPage.vue'
import CourseLessonsPage from '../views/admin/CourseLesson/CourseLessonsPage.vue'
import LessonContentPage from '../views/admin/CourseLesson/LessonContent/LessonContentPage.vue'
import CourseQuestionBank from '../views/admin/CourseLesson/CourseQuestionBank.vue'
import QuestionPage from '../views/admin/CourseLesson/QuestionBank/QuestionPage.vue'
import UserManagementPage from '../views/admin/UserManagement/UserManagementPage.vue'
import RoadmapManagementPage from '../views/admin/map/RoadmapManagementPage.vue'
import RoadmapCoursesPage from '../views/admin/map/RoadmapCoursesPage.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage
  },
  {
    path: '/lo-trinh',
    name: 'RoadmapList',
    component: RoadmapListPage
  },
  {
    path: '/lo-trinh/:slug',
    name: 'RoadmapDetail',
    component: RoadmapDetailPage,
    props: true
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
  {
    path: '/quan-ly/khoa-hoc/:courseId/bai-hoc/:lessonId/noi-dung',
    name: 'LessonContent',
    component: LessonContentPage,
    meta: { requiresAuth: true, roles: ['ADMIN', 'EDITOR'] }
  },
  {
    path: '/quan-ly/khoa-hoc/:id/ngan-hang-cau-hoi',
    name: 'CourseQuestionBank',
    component: CourseQuestionBank,
    meta: { requiresAuth: true, roles: ['ADMIN', 'EDITOR'] }
  },
  {
    path: '/quan-ly/ngan-hang-cau-hoi/:id/cau-hoi',
    name: 'Questions',
    component: QuestionPage,
    meta: { requiresAuth: true, roles: ['ADMIN', 'EDITOR'] }
  },
  {
    path: '/quan-ly/nguoi-dung',
    name: 'UserManagement',
    component: UserManagementPage,
    meta: { requiresAuth: true, roles: ['ADMIN'] }
  },
  {
    path: '/quan-ly/lo-trinh',
    name: 'RoadmapManagement',
    component: RoadmapManagementPage,
    meta: { requiresAuth: true, roles: ['ADMIN', 'EDITOR'] }
  },
  {
    path: '/quan-ly/lo-trinh/:id/khoa-hoc',
    name: 'RoadmapCourses',
    component: RoadmapCoursesPage,
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
