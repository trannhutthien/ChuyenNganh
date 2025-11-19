<script setup>
import { useRoute } from 'vue-router'
import { computed, watch } from 'vue'
import AppHeader from './layouts/partials/Header.vue'
import AppSidebar from './layouts/partials/Sidebar.vue'
import AppFooter from './layouts/partials/Footer.vue'

const route = useRoute()

// Kiểm tra xem có phải trang học không
const isLearningPage = computed(() => {
  return route.path.startsWith('/learn')
})

// Debug: Log route changes
watch(() => route.path, (newPath) => {
  console.log('Current path:', newPath)
  console.log('Is learning page:', isLearningPage.value)
}, { immediate: true })
</script>

<template>
  <div id="app">
    <header class="w-full h-[65px] bg-gray-100 flex items-center px-4 sticky top-0 z-50 shadow-md">
      <AppHeader />
    </header>
    <div class="main-container">
      <AppSidebar />
      <main class="main-content">
        <!-- Router View - Hiển thị các trang -->
        <router-view />
      </main>
    </div>
    
    <!-- Footer - Ẩn khi ở trang học, hiện khi ở trang khác -->
    <AppFooter v-if="!isLearningPage" />
  </div>
</template>

<style scoped>
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-container {
  display: flex;
  flex: 1;
  width: 100%;
}

.main-content {
  flex: 1;
  margin-left: 100px;
  width: calc(100% - 100px);
  overflow-x: hidden;
}
</style>
