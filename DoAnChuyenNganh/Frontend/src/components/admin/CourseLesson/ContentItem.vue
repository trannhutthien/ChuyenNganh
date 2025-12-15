<template>
  <div
    class="flex gap-4 items-center p-4 transition-colors hover:bg-gray-50 group"
  >
    <!-- Drag Handle -->
    <div
      class="p-2 text-gray-400 opacity-0 transition-opacity drag-handle cursor-grab active:cursor-grabbing hover:text-gray-600 group-hover:opacity-100"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
        />
      </svg>
    </div>

    <!-- Order Number -->
    <div
      class="flex justify-center items-center w-8 h-8 text-sm font-medium text-gray-600 bg-gray-100 rounded-full"
    >
      {{ index + 1 }}
    </div>

    <!-- Content Type Icon -->
    <div
      :class="[
        'w-10 h-10 rounded-lg flex items-center justify-center',
        typeIconClass,
      ]"
    >
      <!-- Text Icon -->
      <svg
        v-if="content.type === 'text'"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25"
        />
      </svg>
      <!-- Video Icon -->
      <svg
        v-else-if="content.type === 'video'"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"
        />
      </svg>
      <!-- Image Icon -->
      <svg
        v-else-if="content.type === 'image'"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
        />
      </svg>
      <!-- Code Icon -->
      <svg
        v-else-if="content.type === 'code'"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"
        />
      </svg>
      <!-- Heading Icon -->
      <svg
        v-else-if="content.type === 'heading'"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M2.25 7.125h-.002m19.5 0v-.003M2.25 16.875h19.5m-19.5 0v-.003M19.5 7.125v9.75M4.5 7.125v9.75m0 0h15"
        />
      </svg>
      <!-- Quote Icon -->
      <svg
        v-else-if="content.type === 'quote'"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"
        />
      </svg>
      <!-- Default Icon -->
      <svg
        v-else
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
        />
      </svg>
    </div>

    <!-- Content Info -->
    <div class="flex-1 min-w-0">
      <div class="flex gap-2 items-center mb-1">
        <span
          :class="['px-2 py-0.5 rounded text-xs font-medium', typeBadgeClass]"
        >
          {{ typeLabel }}
        </span>
        <h3 v-if="content.title" class="font-medium text-gray-900 truncate">
          {{ content.title }}
        </h3>
      </div>
      <p class="text-sm text-gray-500 truncate">
        {{ contentPreview }}
      </p>
    </div>

    <!-- Actions -->
    <div
      class="flex gap-1 items-center opacity-0 transition-opacity group-hover:opacity-100"
    >
      <!-- Preview Button -->
      <button
        @click="$emit('preview', content)"
        class="p-2 text-blue-600 rounded-lg transition-colors hover:bg-blue-50"
        title="Xem trước"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-5 h-5"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
          />
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
          />
        </svg>
      </button>

      <!-- Edit Button -->
      <button
        @click="$emit('edit', content)"
        class="p-2 text-yellow-600 rounded-lg transition-colors hover:bg-yellow-50"
        title="Chỉnh sửa"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-5 h-5"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
          />
        </svg>
      </button>

      <!-- Delete Button -->
      <button
        @click="$emit('delete', content)"
        class="p-2 text-red-600 rounded-lg transition-colors hover:bg-red-50"
        title="Xóa"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-5 h-5"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  content: {
    type: Object,
    required: true,
  },
  index: {
    type: Number,
    required: true,
  },
});

defineEmits(["edit", "delete", "preview"]);

// Type helpers
const typeLabel = computed(() => {
  const types = {
    text: "Văn bản",
    video: "Video",
    image: "Hình ảnh",
    code: "Code",
    heading: "Tiêu đề",
    quote: "Trích dẫn",
  };
  return types[props.content.type] || "Nội dung";
});

const typeIconClass = computed(() => {
  const classes = {
    text: "bg-green-100 text-green-600",
    video: "bg-blue-100 text-blue-600",
    image: "bg-purple-100 text-purple-600",
    code: "bg-gray-800 text-green-400",
    heading: "bg-orange-100 text-orange-600",
    quote: "bg-pink-100 text-pink-600",
  };
  return classes[props.content.type] || "bg-gray-100 text-gray-600";
});

const typeBadgeClass = computed(() => {
  const classes = {
    text: "bg-green-100 text-green-700",
    video: "bg-blue-100 text-blue-700",
    image: "bg-purple-100 text-purple-700",
    code: "bg-gray-100 text-gray-700",
    heading: "bg-orange-100 text-orange-700",
    quote: "bg-pink-100 text-pink-700",
  };
  return classes[props.content.type] || "bg-gray-100 text-gray-700";
});

// Content preview (truncated)
const contentPreview = computed(() => {
  const content = props.content.content || "";
  if (props.content.type === "video") {
    return content; // Show URL
  }
  if (props.content.type === "image") {
    return content; // Show URL
  }
  // Strip HTML tags for text content
  const stripped = content.replace(/<[^>]*>/g, "");
  return stripped.length > 100 ? stripped.substring(0, 100) + "..." : stripped;
});
</script>
