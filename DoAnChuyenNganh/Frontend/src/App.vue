<script setup>
import AppHeader from './layouts/AppHeader.vue'
import AppSidebar from './layouts/AppSidebar.vue'
import AppFooter from './layouts/AppFooter.vue'
import HomePage from './views/HomePage.vue'
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
        <!-- HomePage với 4 rows -->
        <HomePage />
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
