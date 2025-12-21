<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <RoadmapHeader
      title="Lộ trình học tập"
      description="Chọn lộ trình phù hợp với mục tiêu nghề nghiệp của bạn. Mỗi lộ trình được thiết kế bài bản từ cơ bản đến nâng cao."
      :show-breadcrumb="false"
    />

    <!-- Content -->
    <div class="px-6 py-10 mx-auto max-w-6xl">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-20">
        <div
          class="w-12 h-12 rounded-full border-4 animate-spin border-primary border-t-transparent"
        ></div>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="py-20 text-center">
        <p class="mb-4 text-red-500">{{ error }}</p>
        <button
          @click="fetchRoadmaps"
          class="px-4 py-2 text-white rounded-lg bg-primary hover:bg-primary/90"
        >
          Thử lại
        </button>
      </div>

      <!-- Roadmap Grid -->
      <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <RoadmapCard
          v-for="roadmap in roadmaps"
          :key="roadmap.id"
          :roadmap="roadmap"
        />
      </div>

      <!-- Empty State -->
      <div
        v-if="!loading && !error && roadmaps.length === 0"
        class="py-20 text-center"
      >
        <div class="mb-4 text-6xl">🗺️</div>
        <h3 class="mb-2 text-xl font-semibold text-gray-800">
          Chưa có lộ trình nào
        </h3>
        <p class="text-gray-500">Các lộ trình học tập sẽ sớm được cập nhật.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { roadmapService } from "../services/courseService.js";
import RoadmapHeader from "../components/map/RoadmapHeader.vue";
import RoadmapCard from "../components/map/RoadmapCard.vue";

const roadmaps = ref([]);
const loading = ref(true);
const error = ref("");

const fetchRoadmaps = async () => {
  loading.value = true;
  error.value = "";

  try {
    const response = await roadmapService.getAll();
    roadmaps.value = response.data || [];
  } catch (err) {
    console.error("Lỗi tải lộ trình:", err);
    error.value = "Không thể tải danh sách lộ trình. Vui lòng thử lại.";
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchRoadmaps();
});
</script>

<style scoped></style>
