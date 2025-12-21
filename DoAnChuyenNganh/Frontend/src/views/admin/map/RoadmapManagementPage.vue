<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <PageHeader
        title="Quản lý lộ trình"
        subtitle="Tạo và quản lý các lộ trình học tập"
        icon-class="text-indigo-600 bg-indigo-100"
        :stats="headerStats"
      >
        <template #icon>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
          </svg>
        </template>
      </PageHeader>
      <BaseButton @click="openCreateModal" variant="primary" size="sm">
        <template #icon>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
        </template>
        Thêm lộ trình
      </BaseButton>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Roadmap List -->
    <div v-else class="grid gap-4">
      <div
        v-for="roadmap in roadmaps"
        :key="roadmap.id"
        class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow"
      >
        <div class="flex items-start gap-4">
          <!-- Icon -->
          <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center text-3xl flex-shrink-0">
            {{ roadmap.icon || '📚' }}
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <h3 class="text-lg font-semibold text-gray-800">{{ roadmap.title }}</h3>
              <span
                class="px-2 py-0.5 text-xs font-medium rounded-full"
                :class="roadmap.status === 1 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
              >
                {{ roadmap.status === 1 ? 'Active' : 'Draft' }}
              </span>
              <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                {{ getLevelText(roadmap.level) }}
              </span>
            </div>
            <p class="text-sm text-gray-500 mb-2 line-clamp-1">{{ roadmap.description }}</p>
            <div class="flex items-center gap-4 text-sm text-gray-500">
              <span>{{ roadmap.totalCourses }} khóa học</span>
              <span>{{ roadmap.duration || 'Chưa cập nhật' }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2">
            <button
              @click="goToCoursesPage(roadmap)"
              class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
              title="Quản lý khóa học"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
              </svg>
            </button>
            <button
              @click="openEditModal(roadmap)"
              class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
              title="Chỉnh sửa"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
              </svg>
            </button>
            <button
              @click="confirmDelete(roadmap)"
              class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
              title="Xóa"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="roadmaps.length === 0" class="text-center py-20 bg-white rounded-xl border border-gray-200">
        <div class="text-5xl mb-4">🗺️</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Chưa có lộ trình nào</h3>
        <p class="text-gray-500 mb-4">Bắt đầu tạo lộ trình học tập đầu tiên</p>
        <BaseButton @click="openCreateModal" variant="primary" size="sm">
          Thêm lộ trình
        </BaseButton>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <RoadmapFormModal
      :is-open="showFormModal"
      :roadmap="selectedRoadmap"
      :loading="formLoading"
      @close="closeFormModal"
      @submit="handleFormSubmit"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      v-model:show="showDeleteModal"
      title="Xóa lộ trình"
      :message="`Bạn có chắc muốn xóa lộ trình '${selectedRoadmap?.title}'? Hành động này không thể hoàn tác.`"
      confirm-text="Xóa"
      confirm-variant="danger"
      :loading="deleteLoading"
      @confirm="handleDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { roadmapService } from '../../../services/courseService.js'
import RoadmapFormModal from './components/RoadmapFormModal.vue'
import ConfirmModal from '../../../components/modal/ConfirmModal.vue'
import PageHeader from '../../../layouts/PageHeader.vue'
import BaseButton from '../../../components/ui/BaseButton.vue'

const router = useRouter()

const roadmaps = ref([])
const loading = ref(true)

// Header stats
const headerStats = computed(() => [
  {
    iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" /></svg>',
    value: roadmaps.value.length,
    label: 'lộ trình'
  },
  {
    iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
    value: roadmaps.value.filter(r => r.status === 1).length,
    label: 'đang hoạt động'
  }
])
const showFormModal = ref(false)
const showDeleteModal = ref(false)
const selectedRoadmap = ref(null)
const formLoading = ref(false)
const deleteLoading = ref(false)

const fetchRoadmaps = async () => {
  loading.value = true
  try {
    const response = await roadmapService.getAllAdmin()
    roadmaps.value = response.data || []
  } catch (error) {
    console.error('Lỗi tải lộ trình:', error)
  } finally {
    loading.value = false
  }
}

const getLevelText = (level) => {
  const levels = { 1: 'Beginner', 2: 'Intermediate', 3: 'Advanced' }
  return levels[level] || 'Beginner'
}

const openCreateModal = () => {
  selectedRoadmap.value = null
  showFormModal.value = true
}

const openEditModal = (roadmap) => {
  selectedRoadmap.value = { ...roadmap }
  showFormModal.value = true
}

const closeFormModal = () => {
  showFormModal.value = false
  selectedRoadmap.value = null
}

const handleFormSubmit = async (formData) => {
  formLoading.value = true
  try {
    if (selectedRoadmap.value?.id) {
      await roadmapService.update(selectedRoadmap.value.id, formData)
    } else {
      await roadmapService.create(formData)
    }
    closeFormModal()
    fetchRoadmaps()
  } catch (error) {
    console.error('Lỗi lưu lộ trình:', error)
  } finally {
    formLoading.value = false
  }
}

const confirmDelete = (roadmap) => {
  selectedRoadmap.value = roadmap
  showDeleteModal.value = true
}

const handleDelete = async () => {
  deleteLoading.value = true
  try {
    await roadmapService.delete(selectedRoadmap.value.id)
    showDeleteModal.value = false
    fetchRoadmaps()
  } catch (error) {
    console.error('Lỗi xóa lộ trình:', error)
  } finally {
    deleteLoading.value = false
  }
}

const goToCoursesPage = (roadmap) => {
  router.push(`/quan-ly/lo-trinh/${roadmap.id}/khoa-hoc`)
}

onMounted(() => {
  fetchRoadmaps()
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
