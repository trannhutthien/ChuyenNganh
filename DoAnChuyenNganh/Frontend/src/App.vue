<script setup>
import { useRoute } from 'vue-router'
import { computed, watch } from 'vue'
import AppHeader from './layouts/AppHeader.vue'
import AppSidebar from './layouts/AppSidebar.vue'
import AppFooter from './layouts/AppFooter.vue'

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
    <!-- Header luôn hiển thị -->
    <AppHeader />
    
    <!-- Container cho Sidebar + Main Content -->
    <div class="main-container">
      <!-- Sidebar luôn hiển thị -->
      <AppSidebar />
      
      <!-- Main Content bên phải - Thay đổi theo trang -->
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
