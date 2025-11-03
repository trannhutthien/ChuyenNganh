<script setup>
import AppHeader from './layouts/AppHeader.vue'
import AppSidebar from './layouts/AppSidebar.vue'
import AppFooter from './layouts/AppFooter.vue'
</script>

<template>
  <div id="app">
    <!-- Header sticky ở trên cùng -->
    <AppHeader />
    
    <!-- Container cho Sidebar + Main Content -->
    <div class="flex flex-1">
      <!-- Sidebar bên trái -->
      <AppSidebar />
      
      <!-- Main Content bên phải -->
      <main class="flex-1 ml-[100px]">
        <!-- Nội dung trang -->
        <div class="min-h-[calc(100vh-65px)] flex items-center justify-center text-gray-500">
          <p>Nội dung trang - Cuộn xuống để xem Footer</p>
        </div>
      </main>
    </div>
    
    <!-- Footer ở dưới cùng, dưới cả Sidebar và Content -->
    <AppFooter />
  </div>
</template>

<style scoped>
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
</style>
