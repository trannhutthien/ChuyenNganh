<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center min-h-screen">
      <div
        class="w-12 h-12 rounded-full border-4 animate-spin border-primary border-t-transparent"
      ></div>
    </div>

    <!-- Error -->
    <div
      v-else-if="error"
      class="flex flex-col justify-center items-center min-h-screen"
    >
      <p class="mb-4 text-red-500">{{ error }}</p>
      <router-link
        to="/lo-trinh"
        class="px-4 py-2 text-white rounded-lg bg-primary hover:bg-primary/90"
      >
        Quay lại danh sách
      </router-link>
    </div>

    <!-- Content -->
    <template v-else-if="roadmap">
      <!-- Header -->
      <RoadmapHeader
        :title="roadmap.title"
        :description="roadmap.description"
        :icon="roadmap.icon || '📚'"
        :level="roadmap.level"
        :level-text="roadmap.levelText"
        :show-breadcrumb="true"
        :stats="headerStats"
      />

      <!-- Course Timeline -->
      <div class="px-6 py-10 mx-auto max-w-4xl">
        <h2 class="mb-8 text-2xl font-bold text-gray-800">
          Các khóa học trong lộ trình
        </h2>

        <!-- Timeline -->
        <div class="relative">
          <!-- Vertical Line -->
          <div class="absolute top-0 bottom-0 left-6 w-0.5 bg-gray-200"></div>

          <!-- Course Items -->
          <div class="space-y-6">
            <div
              v-for="(course, index) in roadmap.courses"
              :key="course.id"
              class="flex relative gap-6"
            >
              <!-- Timeline Node -->
              <div class="relative z-10 flex-shrink-0">
                <div
                  class="flex justify-center items-center w-12 h-12 font-bold text-white rounded-full"
                  :class="course.required ? 'bg-primary' : 'bg-gray-400'"
                >
                  {{ index + 1 }}
                </div>
              </div>

              <!-- Course Card -->
              <RoadmapCourseCard :course="course" @click="goToCourse" />
            </div>
          </div>
        </div>

        <!-- Empty Courses -->
        <div
          v-if="roadmap.courses && roadmap.courses.length === 0"
          class="py-12 text-center"
        >
          <div class="mb-4 text-5xl">📭</div>
          <p class="text-gray-500">Chưa có khóa học nào trong lộ trình này.</p>
        </div>

        <!-- CTA -->
        <div class="mt-12 text-center">
          <button
            v-if="roadmap.courses && roadmap.courses.length > 0"
            @click="startLearning"
            class="px-8 py-3 font-semibold text-white rounded-xl transition-colors bg-primary hover:bg-primary/90"
          >
            Bắt đầu học ngay
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { roadmapService } from "../../services/courseService.js";
import RoadmapHeader from "./RoadmapHeader.vue";
import RoadmapCourseCard from "./RoadmapCourseCard.vue";

const route = useRoute();
const router = useRouter();

const roadmap = ref(null);
const loading = ref(true);
const error = ref("");

// Computed stats cho header
const headerStats = computed(() => {
  if (!roadmap.value) return [];
  return [
    { iconType: "book", text: `${roadmap.value.totalCourses} khóa học` },
    { iconType: "clock", text: roadmap.value.duration || "Đang cập nhật" },
    {
      iconType: "check",
      text: `${roadmap.value.requiredCourses} bắt buộc, ${roadmap.value.optionalCourses} tùy chọn`,
    },
  ];
});

const fetchRoadmap = async () => {
  loading.value = true;
  error.value = "";

  try {
    const slug = route.params.slug;
    const response = await roadmapService.getBySlug(slug);
    roadmap.value = response.data;
  } catch (err) {
    console.error("Lỗi tải lộ trình:", err);
    error.value = "Không tìm thấy lộ trình này.";
  } finally {
    loading.value = false;
  }
};

const goToCourse = (courseId) => {
  router.push(`/learn/${courseId}`);
};

const startLearning = () => {
  if (roadmap.value?.courses?.length > 0) {
    const firstCourse = roadmap.value.courses[0];
    router.push(`/learn/${firstCourse.id}`);
  }
};

onMounted(() => {
  fetchRoadmap();
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
