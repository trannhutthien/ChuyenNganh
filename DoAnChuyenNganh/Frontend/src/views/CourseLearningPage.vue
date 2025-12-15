<template>
  <!-- Container có chiều cao trừ đi header (65px) -->
  <div class="flex bg-white" style="height: calc(100vh - 65px)">
    <!-- Loading State -->
    <div v-if="loading" class="flex flex-1 justify-center items-center">
      <div class="text-center">
        <div
          class="mx-auto mb-4 w-12 h-12 rounded-full border-b-2 animate-spin border-primary"
        ></div>
        <p class="text-gray-500">Đang tải bài học...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex flex-1 justify-center items-center">
      <div class="text-center">
        <p class="mb-4 text-red-500">{{ error }}</p>
        <button
          @click="loadLessons(route.params.courseId)"
          class="px-4 py-2 text-white rounded-lg bg-primary hover:bg-primary/90"
        >
          Thử lại
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="activeLessons.length === 0"
      class="flex flex-1 justify-center items-center"
    >
      <div class="text-center">
        <p class="text-gray-500">Khóa học này chưa có bài học nào.</p>
      </div>
    </div>

    <!-- Main Content -->
    <template v-else>
      <!-- Cột BÊN TRÁI - Nội dung bài học -->
      <div class="flex overflow-hidden flex-col flex-1 bg-white">
        <!-- Header của bài học -->
        <div class="px-6 py-4 bg-white border-b border-gray-200 shadow-sm">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-lg font-bold text-gray-800">{{ courseTitle }}</h1>
              <p class="text-sm text-gray-500">{{ currentLesson?.title }}</p>
            </div>
            <div class="flex gap-4 items-center">
              <div class="text-sm text-gray-500">
                Bài {{ currentLessonIndex + 1 }}/{{ activeLessons.length }}
              </div>
              <div class="overflow-hidden w-32 h-2 bg-gray-200 rounded-full">
                <div
                  class="h-full transition-all duration-300 bg-primary"
                  :style="{ width: progress + '%' }"
                ></div>
              </div>
              <span class="text-sm font-semibold text-primary"
                >{{ progress }}%</span
              >
            </div>
          </div>
        </div>

        <!-- Nội dung bài học (scrollable) -->
        <div class="overflow-y-auto flex-1 bg-gray-50">
          <div class="p-8 mx-auto max-w-4xl">
            <!-- Loading content -->
            <div
              v-if="loadingContent"
              class="flex justify-center items-center py-12"
            >
              <div
                class="w-8 h-8 rounded-full border-b-2 animate-spin border-primary"
              ></div>
              <span class="ml-3 text-gray-500">Đang tải nội dung...</span>
            </div>

            <!-- Tiêu đề bài học -->
            <div
              v-else
              class="p-6 mb-6 bg-white rounded-lg border border-gray-200 shadow-sm"
            >
              <h2 class="mb-2 text-2xl font-bold text-gray-800">
                {{ currentLesson?.title }}
              </h2>
              <p v-if="currentLesson?.description" class="text-gray-600">
                {{ currentLesson?.description }}
              </p>
            </div>

            <!-- Nội dung chi tiết từ database -->
            <div v-if="lessonContents.length > 0" class="space-y-4">
              <LessonContentItem
                v-for="item in lessonContents"
                :key="item.id"
                :item="item"
                class="p-6 bg-white rounded-lg border border-gray-200 shadow-sm"
              />
            </div>

            <!-- Placeholder nếu chưa có nội dung -->
            <div
              v-else-if="!loadingContent"
              class="p-6 text-center text-gray-500 bg-white rounded-lg border border-gray-200 shadow-sm"
            >
              <p>Bài học này chưa có nội dung chi tiết.</p>
            </div>
          </div>
        </div>

        <!-- Footer - Navigation buttons -->
        <div class="px-6 py-4 bg-white border-t border-gray-200 shadow-sm">
          <div class="flex justify-between items-center">
            <BaseButton
              @click="previousLesson"
              :disabled="currentLessonIndex === 0"
              variant="outline"
            >
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
                    d="M15.75 19.5L8.25 12l7.5-7.5"
                  />
                </svg>
              </template>
              Bài trước
            </BaseButton>

            <BaseButton @click="markAsCompleted" variant="outline">
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
                    d="M4.5 12.75l6 6 9-13.5"
                  />
                </svg>
              </template>
              {{ currentLesson?.completed ? "Đã hoàn thành" : "Hoàn thành" }}
            </BaseButton>

            <BaseButton
              @click="nextLesson"
              :disabled="currentLessonIndex === activeLessons.length - 1"
              variant="primary"
            >
              Bài tiếp theo
              <template #icon-right>
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
                    d="M8.25 4.5l7.5 7.5-7.5 7.5"
                  />
                </svg>
              </template>
            </BaseButton>
          </div>
        </div>
      </div>

      <!-- Cột BÊN PHẢI - Danh sách bài học -->
      <!-- File: CourseLearningPage.vue - Dòng 129-136 -->
      <!-- Truyền prop hasBaiKiemTra để kiểm soát hiển thị nút làm bài kiểm tra -->
      <LessonSidebar
        :lessons="activeLessons"
        :current-index="currentLessonIndex"
        :has-bai-kiem-tra="hasBaiKiemTra"
        @select-lesson="selectLesson"
        @start-final-exam="goToFinalExam"
      />
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import LessonSidebar from "../components/course/LessonSidebar.vue";
import LessonContentItem from "../components/course/LessonContentItem.vue";
import BaseButton from "../components/ui/BaseButton.vue";
import { courseService } from "../services/courseService.js";
import quizService from "../services/quizService.js";

const router = useRouter();
const route = useRoute();

// ========== STATE CHÍNH ==========
const courseTitle = ref("");
const currentLessonIndex = ref(0);
const loading = ref(true);
const loadingContent = ref(false);
const error = ref(null);

// ========== DATA - Load từ API ==========
const lessons = ref([]);
const lessonContents = ref([]); // Nội dung chi tiết của bài học hiện tại
const finalExam = ref(null); // Thông tin bài kiểm tra cuối khóa
// File: CourseLearningPage.vue - Dòng 164
// Biến kiểm tra khóa học này có bài kiểm tra cuối khóa hay không
const hasBaiKiemTra = ref(false);

// ========== COMPUTED ==========
// Chỉ lấy bài học có trạng thái hoạt động (status = 1)
const activeLessons = computed(() => {
  return lessons.value.filter(
    (lesson) => lesson.status === 1 || lesson.status === "1"
  );
});

const currentLesson = computed(() => {
  if (activeLessons.value.length === 0) return null;
  return activeLessons.value[currentLessonIndex.value];
});

const completedCount = computed(() => {
  return activeLessons.value.filter((lesson) => lesson.completed).length;
});

const progress = computed(() => {
  if (activeLessons.value.length === 0) return 0;
  return Math.round((completedCount.value / activeLessons.value.length) * 100);
});

// ========== METHODS - Navigation & Actions ==========
const selectLesson = async (index) => {
  currentLessonIndex.value = index;
  // Load nội dung bài học mới
  await loadLessonContent(activeLessons.value[index].id);
};

const nextLesson = async () => {
  if (currentLessonIndex.value < activeLessons.value.length - 1) {
    currentLessonIndex.value++;
    await loadLessonContent(currentLesson.value.id);
  }
};

const previousLesson = async () => {
  if (currentLessonIndex.value > 0) {
    currentLessonIndex.value--;
    await loadLessonContent(currentLesson.value.id);
  }
};

const markAsCompleted = () => {
  if (activeLessons.value[currentLessonIndex.value]) {
    activeLessons.value[currentLessonIndex.value].completed =
      !activeLessons.value[currentLessonIndex.value].completed;
  }
};

const goToQuiz = () => {
  const quizId = 1; // TODO: Lấy từ course data
  router.push(`/quiz/${quizId}`);
};

// Đi đến trang bài kiểm tra cuối khóa
const goToFinalExam = () => {
  const quizId = 1; // TODO: Lấy quiz ID thực tế
  router.push(`/quiz/${quizId}`);
};

// ========== API CALLS ==========
const loadLessons = async (courseId) => {
  loading.value = true;
  error.value = null;
  try {
    const response = await courseService.getLessons(courseId);
    console.log("API Response:", response);

    // Set course title
    if (response.course) {
      courseTitle.value = response.course.title;
    }

    // Set lessons
    lessons.value = response.data || [];
    console.log("Loaded lessons:", lessons.value);

    // Load nội dung bài học đầu tiên có trạng thái hoạt động
    const firstActiveLesson = lessons.value.find(
      (l) => l.status === 1 || l.status === "1"
    );
    if (firstActiveLesson) {
      await loadLessonContent(firstActiveLesson.id);
    }

    // Load thông tin bài kiểm tra cuối khóa
    await loadFinalExam(courseId);
  } catch (err) {
    console.error("Error loading lessons:", err);
    error.value = "Không thể tải bài học. Vui lòng thử lại.";
  } finally {
    loading.value = false;
  }
};

// File: CourseLearningPage.vue - Dòng 254-270
// Load thông tin bài kiểm tra cuối khóa từ API
// Nếu có bài kiểm tra -> hasBaiKiemTra = true -> hiển thị nút làm bài
// Nếu không có -> hasBaiKiemTra = false -> ẩn nút làm bài
const loadFinalExam = async (courseId) => {
  try {
    const response = await quizService.getFinalExam(courseId);
    console.log("Final exam response:", response);
    finalExam.value = response.baiKiemTra || null;
    // Cập nhật hasBaiKiemTra dựa trên kết quả API
    hasBaiKiemTra.value = !!response.baiKiemTra;
  } catch (err) {
    console.log("No final exam found or error:", err);
    finalExam.value = null;
    // Không có bài kiểm tra -> ẩn nút
    hasBaiKiemTra.value = false;
  }
};

// Load nội dung chi tiết của bài học
const loadLessonContent = async (lessonId) => {
  loadingContent.value = true;
  try {
    const response = await courseService.getLessonContent(lessonId);
    console.log("Lesson content response:", response);

    // Set nội dung bài học
    lessonContents.value = response.contents || [];
    console.log("Loaded lesson contents:", lessonContents.value);
  } catch (err) {
    console.error("Error loading lesson content:", err);
    lessonContents.value = [];
  } finally {
    loadingContent.value = false;
  }
};

// Load lessons khi component mount
onMounted(() => {
  const courseId = route.params.courseId;
  console.log("CourseLearningPage mounted, courseId:", courseId);
  if (courseId) {
    loadLessons(courseId);
  } else {
    error.value = "Không tìm thấy khóa học";
    loading.value = false;
  }
});
</script>

<style scoped>
/* Minimal styles - Components handle their own styling */
</style>
