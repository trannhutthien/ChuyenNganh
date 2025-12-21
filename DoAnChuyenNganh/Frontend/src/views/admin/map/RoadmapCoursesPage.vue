<template>
  <div class="p-6">
    <!-- Header với Breadcrumb -->
    <div class="mb-6">
      <nav class="flex gap-2 items-center mb-3 text-sm text-gray-500">
        <router-link
          to="/quan-ly/lo-trinh"
          class="flex gap-1 items-center hover:text-primary"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-4 h-4"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
            />
          </svg>
          Quản lý lộ trình
        </router-link>
        <span>/</span>
        <span class="font-medium text-gray-800">{{
          roadmap?.title || "Khóa học"
        }}</span>
      </nav>

      <div class="flex justify-between items-start">
        <div class="flex gap-4 items-center">
          <div
            class="flex justify-center items-center w-16 h-16 text-4xl bg-gradient-to-br rounded-2xl from-primary/20 to-primary/5"
          >
            {{ roadmap?.icon || "📚" }}
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-800">
              {{ roadmap?.title || "Lộ trình" }}
            </h1>
            <p class="mt-1 text-gray-500">
              {{ courses.length }} khóa học trong lộ trình
            </p>
          </div>
        </div>
        <BaseButton @click="showAddModal = true" variant="primary" size="sm">
          <template #icon>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="2"
              stroke="currentColor"
              class="w-5 h-5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 4.5v15m7.5-7.5h-15"
              />
            </svg>
          </template>
          Thêm khóa học
        </BaseButton>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <div
        class="w-10 h-10 rounded-full border-4 animate-spin border-primary border-t-transparent"
      ></div>
    </div>

    <!-- Course List -->
    <div v-else>
      <!-- Drag & Drop List -->
      <div class="space-y-3">
        <TransitionGroup name="list">
          <div
            v-for="(course, index) in courses"
            :key="course.id"
            class="p-4 bg-white rounded-xl border border-gray-200 transition-all hover:shadow-lg hover:border-primary/30 group"
            :class="{ 'ring-2 ring-primary/50': draggingId === course.id }"
            draggable="true"
            @dragstart="handleDragStart($event, index)"
            @dragover.prevent="handleDragOver($event, index)"
            @dragend="handleDragEnd"
          >
            <div class="flex gap-4 items-center">
              <!-- Drag Handle -->
              <div
                class="text-gray-400 cursor-grab active:cursor-grabbing hover:text-gray-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="w-5 h-5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                  />
                </svg>
              </div>

              <!-- Order Badge -->
              <div
                class="flex flex-shrink-0 justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full"
                :class="
                  course.required
                    ? 'bg-gradient-to-br from-primary to-primary/80'
                    : 'bg-gray-400'
                "
              >
                {{ index + 1 }}
              </div>

              <!-- Course Thumbnail -->
              <div
                class="overflow-hidden flex-shrink-0 w-20 h-14 bg-gray-100 rounded-lg"
              >
                <img
                  v-if="course.thumbnail"
                  :src="course.thumbnail"
                  :alt="course.title"
                  class="object-cover w-full h-full"
                />
                <div
                  v-else
                  class="flex justify-center items-center w-full h-full text-2xl"
                >
                  📖
                </div>
              </div>

              <!-- Course Info -->
              <div class="flex-1 min-w-0">
                <div class="flex gap-2 items-center mb-1">
                  <h3 class="font-semibold text-gray-800 truncate">
                    {{ course.title }}
                  </h3>
                  <span
                    class="flex-shrink-0 px-2 py-0.5 text-xs font-medium rounded-full"
                    :class="
                      course.required
                        ? 'bg-primary/10 text-primary'
                        : 'bg-gray-100 text-gray-600'
                    "
                  >
                    {{ course.required ? "Bắt buộc" : "Tùy chọn" }}
                  </span>
                </div>
                <p class="text-sm text-gray-500 line-clamp-1">
                  {{ course.description }}
                </p>
                <div class="flex gap-4 items-center mt-1 text-xs text-gray-400">
                  <span class="flex gap-1 items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="w-3.5 h-3.5"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"
                      />
                    </svg>
                    {{ course.lessons || 0 }} bài học
                  </span>
                  <span
                    class="font-medium"
                    :class="
                      course.price > 0 ? 'text-orange-500' : 'text-green-500'
                    "
                  >
                    {{
                      course.price > 0 ? formatPrice(course.price) : "Miễn phí"
                    }}
                  </span>
                  <span
                    v-if="course.note"
                    class="text-blue-500 truncate max-w-[150px]"
                  >
                    📝 {{ course.note }}
                  </span>
                </div>
              </div>

              <!-- Actions -->
              <div
                class="flex gap-1 items-center opacity-0 transition-opacity group-hover:opacity-100"
              >
                <button
                  @click="openEditModal(course)"
                  class="p-2 text-blue-600 rounded-lg transition-colors hover:bg-blue-50"
                  title="Chỉnh sửa"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-4 h-4"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
                    />
                  </svg>
                </button>
                <button
                  @click="confirmRemove(course)"
                  class="p-2 text-red-600 rounded-lg transition-colors hover:bg-red-50"
                  title="Xóa khỏi lộ trình"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-4 h-4"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>

      <!-- Empty State -->
      <div
        v-if="courses.length === 0"
        class="py-20 text-center bg-white rounded-2xl border-2 border-gray-200 border-dashed"
      >
        <div
          class="flex justify-center items-center mx-auto mb-4 w-20 h-20 bg-gray-100 rounded-full"
        >
          <span class="text-4xl">📚</span>
        </div>
        <h3 class="mb-2 text-lg font-semibold text-gray-800">
          Chưa có khóa học nào
        </h3>
        <p class="mb-6 text-gray-500">
          Thêm khóa học vào lộ trình để học viên có thể theo dõi
        </p>
        <BaseButton @click="showAddModal = true" variant="primary" size="sm">
          Thêm khóa học đầu tiên
        </BaseButton>
      </div>
    </div>

    <!-- Add Course Modal -->
    <AddCourseToRoadmapModal
      :is-open="showAddModal"
      :roadmap-id="roadmapId"
      :existing-course-ids="existingCourseIds"
      @close="showAddModal = false"
      @added="handleCourseAdded"
    />

    <!-- Edit Course Modal -->
    <EditRoadmapCourseModal
      :is-open="showEditModal"
      :course="selectedCourse"
      :roadmap-id="roadmapId"
      @close="closeEditModal"
      @updated="handleCourseUpdated"
    />

    <!-- Remove Confirmation -->
    <ConfirmModal
      v-model:show="showRemoveModal"
      title="Xóa khóa học khỏi lộ trình"
      :message="`Bạn có chắc muốn xóa khóa học '${selectedCourse?.title}' khỏi lộ trình này?`"
      confirm-text="Xóa"
      confirm-variant="danger"
      :loading="removeLoading"
      @confirm="handleRemove"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import { roadmapService } from "../../../services/courseService.js";
import AddCourseToRoadmapModal from "./components/AddCourseToRoadmapModal.vue";
import EditRoadmapCourseModal from "./components/EditRoadmapCourseModal.vue";
import ConfirmModal from "../../../components/modal/ConfirmModal.vue";
import BaseButton from "../../../components/ui/BaseButton.vue";

const route = useRoute();
const roadmapId = route.params.id;

const roadmap = ref(null);
const courses = ref([]);
const loading = ref(true);
const showAddModal = ref(false);
const showEditModal = ref(false);
const showRemoveModal = ref(false);
const selectedCourse = ref(null);
const removeLoading = ref(false);

// Drag & Drop
const draggingId = ref(null);
const dragOverIndex = ref(null);

const existingCourseIds = computed(() =>
  courses.value.map((c) => c.courseId || c.id)
);

const fetchData = async () => {
  loading.value = true;
  try {
    const response = await roadmapService.getBySlug(roadmapId);
    roadmap.value = response.data;
    courses.value = (response.data.courses || []).sort(
      (a, b) => a.order - b.order
    );
  } catch (error) {
    console.error("Lỗi tải dữ liệu:", error);
  } finally {
    loading.value = false;
  }
};

const formatPrice = (price) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(price);
};

// Drag & Drop handlers
const handleDragStart = (e, index) => {
  draggingId.value = courses.value[index].id;
  e.dataTransfer.effectAllowed = "move";
};

const handleDragOver = (e, index) => {
  e.preventDefault();
  dragOverIndex.value = index;
};

const handleDragEnd = async () => {
  if (dragOverIndex.value !== null && draggingId.value) {
    const fromIndex = courses.value.findIndex((c) => c.id === draggingId.value);
    const toIndex = dragOverIndex.value;

    if (fromIndex !== toIndex) {
      // Reorder locally
      const item = courses.value.splice(fromIndex, 1)[0];
      courses.value.splice(toIndex, 0, item);

      // Update order on server
      try {
        await Promise.all(
          courses.value.map((course, idx) =>
            roadmapService.updateCourse(
              roadmapId,
              course.courseId || course.id,
              { order: idx + 1 }
            )
          )
        );
      } catch (error) {
        console.error("Lỗi cập nhật thứ tự:", error);
        fetchData(); // Reload if error
      }
    }
  }

  draggingId.value = null;
  dragOverIndex.value = null;
};

const openEditModal = (course) => {
  selectedCourse.value = { ...course };
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  selectedCourse.value = null;
};

const confirmRemove = (course) => {
  selectedCourse.value = course;
  showRemoveModal.value = true;
};

const handleRemove = async () => {
  removeLoading.value = true;
  try {
    await roadmapService.removeCourse(
      roadmapId,
      selectedCourse.value.courseId || selectedCourse.value.id
    );
    showRemoveModal.value = false;
    fetchData();
  } catch (error) {
    console.error("Lỗi xóa khóa học:", error);
  } finally {
    removeLoading.value = false;
  }
};

const handleCourseAdded = () => {
  showAddModal.value = false;
  fetchData();
};

const handleCourseUpdated = () => {
  closeEditModal();
  fetchData();
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}

.list-move {
  transition: transform 0.3s ease;
}
</style>
