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
          <!-- Back Button -->
          <BackButton to="/" container-class="mb-3">
            Quay lại trang chủ
          </BackButton>

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

            <!-- Bài kiểm tra cuối bài học -->
            <LessonEndContentQuiz
              v-if="!loadingContent"
              :quiz="lessonQuiz"
              @start-quiz="goToExam"
            />
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
      <!-- Truyền danh sách bài kiểm tra cuối khóa (BaiHocId = null) -->
      <LessonSidebar
        :lessons="activeLessons"
        :current-index="currentLessonIndex"
        :course-exams="courseExams"
        @select-lesson="selectLesson"
        @start-exam="goToExam"
      />
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import LessonSidebar from "../components/course/LessonSidebar.vue";
import LessonContentItem from "../components/course/LessonContentItem.vue";
import LessonEndContentQuiz from "../components/course/LessonEndContentQuiz.vue";
import BaseButton from "../components/ui/BaseButton.vue";
import BackButton from "../components/ui/BackButton.vue";
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
// Danh sách bài kiểm tra cuối khóa (BaiHocId = null)
const courseExams = ref([]);
// Bài kiểm tra cuối bài học hiện tại
const lessonQuiz = ref(null);

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

// Đi đến trang bài kiểm tra với quizId cụ thể
const goToExam = (quizId) => {
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

    // Load danh sách bài kiểm tra cuối khóa
    await loadCourseExams(courseId);
  } catch (err) {
    console.error("Error loading lessons:", err);
    error.value = "Không thể tải bài học. Vui lòng thử lại.";
  } finally {
    loading.value = false;
  }
};

// Load danh sách bài kiểm tra cuối khóa (BaiHocId = null)
const loadCourseExams = async (courseId) => {
  try {
    const response = await quizService.getQuizzesByCourseOnly(courseId);
    console.log("Course exams response:", response);
    // Lọc chỉ lấy bài kiểm tra có BaiHocId = null (bài kiểm tra cuối khóa)
    const exams = Array.isArray(response) ? response : response.data || [];
    courseExams.value = exams.filter(
      (exam) => !exam.baiHocId && exam.trangThai === 2
    );
    console.log("Filtered course exams (BaiHocId = null):", courseExams.value);
  } catch (err) {
    console.log("Error loading course exams:", err);
    courseExams.value = [];
  }
};

// Load nội dung chi tiết của bài học
const loadLessonContent = async (lessonId) => {
  loadingContent.value = true;
  lessonQuiz.value = null; // Reset quiz khi chuyển bài
  try {
    const response = await courseService.getLessonContent(lessonId);
    console.log("Lesson content response:", response);

    // Set nội dung bài học
    lessonContents.value = response.contents || [];
    console.log("Loaded lesson contents:", lessonContents.value);

    // Load bài kiểm tra cuối bài học
    await loadLessonQuiz(lessonId);
  } catch (err) {
    console.error("Error loading lesson content:", err);
    lessonContents.value = [];
  } finally {
    loadingContent.value = false;
  }
};

// Load bài kiểm tra cuối bài học (nếu có)
const loadLessonQuiz = async (lessonId) => {
  try {
    const response = await quizService.getQuizzesByLesson(lessonId);
    console.log("Lesson quiz response:", response);
    const quizzes = response.data || [];
    // Lấy bài kiểm tra đầu tiên có trạng thái công khai (trangThai = 2)
    lessonQuiz.value = quizzes.find((q) => q.TrangThai === 2) || null;
    console.log("Lesson quiz:", lessonQuiz.value);
  } catch (err) {
    console.log("Error loading lesson quiz:", err);
    lessonQuiz.value = null;
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
