<template>
  <!-- Course Info -->
  <template v-if="columnKey === 'title'">
    <div class="flex items-center gap-3">
      <img 
        :src="row.thumbnail" 
        :alt="row.title"
        class="w-16 h-12 rounded-lg object-cover"
      />
      <div>
        <p class="font-medium text-gray-800 line-clamp-1">{{ row.title }}</p>
        <p class="text-sm text-gray-500">{{ row.createdAt }}</p>
      </div>
    </div>
  </template>

  <!-- Level (Cấp độ) -->
  <template v-else-if="columnKey === 'level'">
    <BadgeLabel :value="row.level" type="level" />
  </template>

  <!-- Price (Giá) -->
  <template v-else-if="columnKey === 'price'">
    <span :class="row.price > 0 ? 'text-primary font-semibold' : 'text-green-600 font-medium'">
      {{ row.price > 0 ? formatPrice(row.price) : 'Miễn phí' }}
    </span>
  </template>

  <!-- Category -->
  <template v-else-if="columnKey === 'category'">
    <BadgeLabel :value="row.category" type="category" />
  </template>

  <!-- Status -->
  <template v-else-if="columnKey === 'status'">
    <BadgeLabel :value="row.status" type="status" />
  </template>

  <!-- Actions -->
  <template v-else-if="columnKey === 'actions'">
    <TableActions 
      :row="row"
      @view="$emit('view', $event)"
      @edit="$emit('edit', $event)"
      @delete="$emit('delete', $event)"
    />
  </template>

  <!-- Default: hiển thị giá trị -->
  <template v-else>
    <span class="text-gray-700">{{ row[columnKey] }}</span>
  </template>
</template>

<script setup>
import BadgeLabel from './BadgeLabel.vue'
import TableActions from './TableActions.vue'

defineProps({
  row: {
    type: Object,
    required: true
  },
  columnKey: {
    type: String,
    required: true
  }
})

defineEmits(['view', 'edit', 'delete'])

// Format price to VND
const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
