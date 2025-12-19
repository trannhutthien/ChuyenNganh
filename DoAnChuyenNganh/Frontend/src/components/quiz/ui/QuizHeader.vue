<template>
  <div class="bg-white border-b border-gray-200 shadow-sm">
    <div class="px-6 py-6 mx-auto max-w-7xl">
      <!-- Back Button -->
      <BackButton :to="backTo" container-class="mb-4" @click="$emit('back')">
        {{ backText }}
      </BackButton>

      <div
        class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
      >
        <!-- Quiz Info -->
        <div class="flex-1">
          <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ quizTitle }}</h1>
          <p class="mb-4 text-gray-600">{{ quizDescription }}</p>

          <!-- Meta Information -->
          <div class="flex flex-wrap gap-4">
            <!-- Duration -->
            <div class="flex gap-2 items-center text-gray-700">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5 text-primary"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                />
              </svg>
              <span class="font-medium">{{ quizDuration }} phút</span>
            </div>

            <!-- Total Questions -->
            <div class="flex gap-2 items-center text-gray-700">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5 text-primary"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                />
              </svg>
              <span class="font-medium">{{ quizTotalQuestions }} câu hỏi</span>
            </div>

            <!-- Passing Score -->
            <div class="flex gap-2 items-center text-gray-700">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5 text-primary"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"
                />
              </svg>
              <span class="font-medium">Điểm đạt: {{ quizPassingScore }}</span>
            </div>

            <!-- Attempts Left -->
            <div
              v-if="quizAttemptsLeft !== null"
              class="flex gap-2 items-center text-gray-700"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5 text-amber-500"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                />
              </svg>
              <span class="font-medium text-amber-600"
                >Còn {{ quizAttemptsLeft }} lần làm</span
              >
            </div>

            <!-- Difficulty (Optional) -->
            <div v-if="quiz.difficulty" class="flex gap-2 items-center">
              <span
                class="px-3 py-1 text-xs font-semibold rounded-full"
                :class="difficultyClass"
              >
                {{ difficultyText }}
              </span>
            </div>
          </div>
        </div>

        <!-- Timer and Progress (Slot) -->
        <div v-if="showControls" class="lg:w-80">
          <slot name="controls">
            <!-- Default: Timer + Progress được truyền từ parent -->
          </slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import BackButton from "@/components/ui/BackButton.vue";

const props = defineProps({
  quiz: {
    type: Object,
    required: true,
    // Bỏ validator vì data có thể từ nhiều format khác nhau
  },
  showControls: {
    type: Boolean,
    default: false,
  },
  // Props cho BackButton
  backTo: {
    type: [String, Object],
    default: null, // null = router.back()
  },
  backText: {
    type: String,
    default: "Quay lại",
  },
});

defineEmits(["back"]);

// Computed để hỗ trợ cả 2 format (camelCase và snake_case)
const quizTitle = computed(
  () => props.quiz?.title || props.quiz?.tieuDe || "Bài kiểm tra"
);
const quizDescription = computed(
  () => props.quiz?.description || props.quiz?.moTa || ""
);
const quizDuration = computed(
  () => props.quiz?.duration_minutes || props.quiz?.thoiGianLamBai || 30
);
const quizTotalQuestions = computed(
  () =>
    props.quiz?.total_questions ||
    props.quiz?.soCauHoiThucTe ||
    props.quiz?.soCauHoi ||
    0
);
const quizPassingScore = computed(() => {
  const score = props.quiz?.passing_score || props.quiz?.diemDat || 5;
  return Number(score);
});
const quizAttemptsLeft = computed(() => {
  if (props.quiz?.attempts_left !== undefined) return props.quiz.attempts_left;
  if (
    props.quiz?.soLanLamToiDa !== undefined &&
    props.quiz?.soLanDaLam !== undefined
  ) {
    return props.quiz.soLanLamToiDa - props.quiz.soLanDaLam;
  }
  return null;
});

// Difficulty styling
const difficultyClass = computed(() => {
  const classes = {
    easy: "bg-green-100 text-green-700",
    medium: "bg-yellow-100 text-yellow-700",
    hard: "bg-red-100 text-red-700",
  };
  return (
    classes[props.quiz.difficulty?.toLowerCase()] || "bg-gray-100 text-gray-700"
  );
});

const difficultyText = computed(() => {
  const texts = {
    easy: "Dễ",
    medium: "Trung bình",
    hard: "Khó",
  };
  return texts[props.quiz.difficulty?.toLowerCase()] || props.quiz.difficulty;
});
</script>
