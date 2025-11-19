<template>
  <div class="relative w-full h-[400px] overflow-hidden rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600">
    <!-- Slider Container -->
    <div 
      class="flex transition-transform duration-500 ease-in-out h-full"
      :style="{ transform: `translateX(-${currentSlide * 100}%)` }"
    >
      <!-- Slide 1 -->
      <div class="min-w-full h-full flex items-center justify-between px-16 bg-gradient-to-r from-blue-600 to-indigo-700">
        <div class="text-white max-w-xl">
          <h2 class="text-5xl font-bold mb-4">Học Lập Trình Từ Cơ Bản</h2>
          <p class="text-xl mb-6 text-blue-100">Khóa học JavaScript, Python, Vue.js và nhiều hơn nữa</p>
          <BaseButton variant="primary" size="lg" class="!bg-white !text-blue-600 hover:!bg-blue-50 !shadow-xl">
            Khám phá ngay
          </BaseButton>
        </div>
        <div class="hidden md:block">
          <img src="https://placehold.co/400x300/4F46E5/FFF?text=Coding" alt="Banner 1" class="rounded-lg shadow-2xl">
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="min-w-full h-full flex items-center justify-between px-16 bg-gradient-to-r from-purple-600 to-pink-600">
        <div class="text-white max-w-xl">
          <h2 class="text-5xl font-bold mb-4">Lộ Trình Học Tập Chuyên Nghiệp</h2>
          <p class="text-xl mb-6 text-purple-100">Từ Junior đến Senior Developer</p>
          <BaseButton variant="primary" size="lg" class="!bg-white !text-purple-600 hover:!bg-purple-50 !shadow-xl">
            Xem lộ trình
          </BaseButton>
        </div>
        <div class="hidden md:block">
          <img src="https://placehold.co/400x300/9333EA/FFF?text=Roadmap" alt="Banner 2" class="rounded-lg shadow-2xl">
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="min-w-full h-full flex items-center justify-between px-16 bg-gradient-to-r from-orange-500 to-red-600">
        <div class="text-white max-w-xl">
          <h2 class="text-5xl font-bold mb-4">Khóa Học Miễn Phí</h2>
          <p class="text-xl mb-6 text-orange-100">Hơn 100+ khóa học chất lượng hoàn toàn miễn phí</p>
          <BaseButton variant="primary" size="lg" class="!bg-white !text-orange-600 hover:!bg-orange-50 !shadow-xl">
            Học ngay
          </BaseButton>
        </div>
        <div class="hidden md:block">
          <img src="https://placehold.co/400x300/EA580C/FFF?text=Free" alt="Banner 3" class="rounded-lg shadow-2xl">
        </div>
      </div>
    </div>

    <!-- Navigation Dots -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-3">
      <button
        v-for="(slide, index) in totalSlides"
        :key="index"
        @click="currentSlide = index"
        class="w-3 h-3 rounded-full transition-all"
        :class="currentSlide === index ? 'bg-white w-8' : 'bg-white/50 hover:bg-white/75'"
      ></button>
    </div>

    <!-- Previous Button -->
    <button
      @click="previousSlide"
      class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
      </svg>
    </button>

    <!-- Next Button -->
    <button
      @click="nextSlide"
      class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import BaseButton from './BaseButton.vue'

const currentSlide = ref(0)
const totalSlides = 3
let autoPlayInterval = null

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % totalSlides
}

const previousSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + totalSlides) % totalSlides
}

// Auto play
onMounted(() => {
  autoPlayInterval = setInterval(() => {
    nextSlide()
  }, 5000) // Chuyển slide mỗi 5 giây
})

onUnmounted(() => {
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
  }
})
</script>
