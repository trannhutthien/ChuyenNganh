import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../views/HomePage.vue'
import CourseLearningPage from '../views/CourseLearningPage.vue'
import QuizPage from '../views/QuizPage.vue'
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
  // Admin routes
  {
    path: '/quan-ly/khoa-hoc',
    name: 'CourseManagement',
    component: CourseManagementPage,
    meta: { requiresAuth: true, roles: ['ADMIN', 'EDITOR'] }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
