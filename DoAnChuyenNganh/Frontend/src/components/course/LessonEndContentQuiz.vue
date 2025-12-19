<template>
  <div
    v-if="quiz"
    class="p-6 mt-6 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-200 shadow-sm"
  >
    <div class="flex items-center gap-4">
      <!-- Icon -->
      <div
        class="flex justify-center items-center w-14 h-14 bg-purple-100 rounded-xl"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-7 h-7 text-purple-600"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"
          />
        </svg>
      </div>

      <!-- Info -->
      <div class="flex-1">
        <h3 class="text-lg font-semibold text-gray-800">
          {{ quiz.TenBaiKiemTra }}
        </h3>
        <p class="mt-1 text-sm text-gray-600">
          {{ quiz.ThietLapJson?.soCauHoi || 0 }} câu hỏi •
          {{ quiz.ThietLapJson?.thoiGianLamBai || 0 }} phút • Điểm đạt:
          {{ quiz.DiemDat || 0 }}%
        </p>
      </div>

      <!-- Button -->
      <BaseButton
        @click="$emit('start-quiz', quiz.id)"
        variant="primary"
        class="!bg-purple-600 hover:!bg-purple-700"
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
              d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"
            />
          </svg>
        </template>
        Làm bài kiểm tra
      </BaseButton>
    </div>
  </div>
</template>

<script setup>
import BaseButton from "../ui/BaseButton.vue";

defineProps({
  quiz: {
    type: Object,
    default: null,
  },
});

defineEmits(["start-quiz"]);
</script>
