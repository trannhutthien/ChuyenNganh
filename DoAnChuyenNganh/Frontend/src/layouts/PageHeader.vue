<template>
  <div class="p-6 bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="flex gap-4 items-start">
      <!-- Icon/Image Slot -->
      <div v-if="$slots.icon || icon" :class="['w-16 h-16 rounded-xl flex items-center justify-center', iconClass]">
        <slot name="icon">
          <component v-if="icon" :is="icon" class="w-8 h-8" />
        </slot>
      </div>

      <!-- Image (alternative to icon) -->
      <img 
        v-else-if="image" 
        :src="image" 
        :alt="title"
        class="w-24 h-16 object-cover rounded-lg"
      />

      <!-- Content -->
      <div class="flex-1">
        <!-- Badge + Title Row -->
        <div v-if="badge" class="flex gap-3 items-center mb-2">
          <span class="px-3 py-1 text-sm font-medium rounded-full bg-primary/10 text-primary">
            {{ badge }}
          </span>
          <slot name="badge-extra"></slot>
        </div>

        <h1 class="text-2xl font-bold text-gray-800">{{ title }}</h1>
        <p v-if="subtitle" class="mt-1 text-gray-600">{{ subtitle }}</p>

        <!-- Stats -->
        <div v-if="stats.length > 0 || $slots.stats" class="flex flex-wrap gap-4 items-center mt-3 text-sm text-gray-500">
          <span v-for="(stat, index) in stats" :key="index" class="flex gap-1 items-center">
            <component v-if="stat.icon" :is="stat.icon" class="w-4 h-4" />
            <span v-else-if="stat.iconHtml" v-html="stat.iconHtml"></span>
            {{ stat.value }} {{ stat.label }}
          </span>
          <slot name="stats"></slot>
        </div>
      </div>

      <!-- Actions Slot -->
      <div v-if="$slots.actions" class="flex gap-2 items-center">
        <slot name="actions"></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  // Title & Subtitle
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  // Badge (optional - shows above title)
  badge: {
    type: String,
    default: ''
  },
  // Icon (component or null if using slot)
  icon: {
    type: [Object, Function],
    default: null
  },
  // Icon container class (bg color, text color)
  iconClass: {
    type: String,
    default: 'bg-blue-100 text-blue-600'
  },
  // Image URL (alternative to icon)
  image: {
    type: String,
    default: ''
  },
  // Stats array: [{ icon?, iconHtml?, value, label }]
  stats: {
    type: Array,
    default: () => []
  }
})
</script>
