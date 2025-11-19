<script setup>
import { useRoute } from 'vue-router'
import { computed } from 'vue'
import AppHeader from '../components/layout/AppHeader.vue'
import AppSidebar from '../components/layout/AppSidebar.vue'
import AppFooter from '../components/layout/AppFooter.vue'

const route = useRoute()

// Kiểm tra xem có phải trang học không
const isLearningPage = computed(() => {
  return route.path.startsWith('/learn')
})
</script>

<template>
  <div class="default-layout">
    <AppHeader />
    <div class="main-container">
      <AppSidebar />
      <main class="main-content">
        <slot />
      </main>
    </div>
    <AppFooter v-if="!isLearningPage" />
  </div>
</template>

<style scoped>
.default-layout {
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
