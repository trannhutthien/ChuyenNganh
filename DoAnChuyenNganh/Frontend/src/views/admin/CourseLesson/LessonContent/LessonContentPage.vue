<template>
  <div class="p-6">
    <!-- Back Button & Header -->
    <div class="mb-6">
      <BackButton
        :to="`/quan-ly/khoa-hoc/${courseId}/bai-hoc`"
        container-class="mb-4"
      >
        Quay lại danh sách bài học
      </BackButton>

      <!-- Lesson Info Header -->
      <PageHeader
        :title="lesson.title"
        :subtitle="lesson.description || 'Quản lý nội dung chi tiết của bài học'"
        :badge="`Bài ${lesson.order}`"
        :icon-class="typeIconClass"
        :stats="headerStats"
      >
        <template #icon>
          <svg v-if="lesson.type === 'video'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
          </svg>
          <svg v-else-if="lesson.type === 'text' || lesson.type === 'paragraph'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
          </svg>
        </template>
        <template #badge-extra>
          <BadgeLabel :value="lesson.status" type="status" />
        </template>
      </PageHeader>
    </div>

    <!-- Actions Bar -->
    <div
      class="flex flex-col gap-4 justify-between items-start mb-6 sm:flex-row sm:items-center"
    >
      <!-- Content Type Filter -->
      <div class="flex gap-2 items-center">
        <button
          v-for="filter in contentFilters"
          :key="filter.value"
          @click="activeFilter = filter.value"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
            activeFilter === filter.value
              ? 'bg-primary text-white'
              : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
          ]"
        >
          {{ filter.label }}
        </button>
      </div>

      <!-- Add Button -->
      <BaseButton @click="openAddModal" variant="primary" size="sm">
        <template #icon>
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
              d="M12 4.5v15m7.5-7.5h-15"
            />
          </svg>
        </template>
        Thêm nội dung
      </BaseButton>
    </div>

    <!-- Content List -->
    <div
      class="overflow-hidden bg-white rounded-xl border border-gray-100 shadow-sm"
    >
      <!-- Loading State -->
      <div v-if="isLoading" class="p-8 text-center">
        <div
          class="mx-auto w-10 h-10 rounded-full border-b-2 animate-spin border-primary"
        ></div>
        <p class="mt-4 text-gray-500">Đang tải nội dung...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredContents.length === 0" class="p-12 text-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="mx-auto w-16 h-16 text-gray-300"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
          />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">
          Chưa có nội dung nào
        </h3>
        <p class="mt-2 text-gray-500">Bắt đầu thêm nội dung cho bài học này</p>
        <BaseButton
          @click="openAddModal"
          variant="primary"
          size="sm"
          class="mt-4"
        >
          Thêm nội dung đầu tiên
        </BaseButton>
      </div>

      <!-- Content Items -->
      <div v-else class="divide-y divide-gray-100">
        <template v-for="(item, index) in filteredContents" :key="item.id">
          <!-- Quiz Item -->
          <div
            v-if="item.itemType === 'quiz'"
            class="flex gap-4 items-center p-4 transition-colors hover:bg-purple-50/50 group"
          >
            <!-- Order Number -->
            <div
              class="flex justify-center items-center w-8 h-8 text-sm font-medium text-purple-600 bg-purple-100 rounded-full"
            >
              {{ index + 1 }}
            </div>

            <!-- Quiz Icon -->
            <div
              class="flex justify-center items-center w-10 h-10 text-purple-600 bg-purple-100 rounded-lg"
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
                  d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"
                />
              </svg>
            </div>

            <!-- Quiz Info -->
            <div class="flex-1 min-w-0">
              <div class="flex gap-2 items-center mb-1">
                <span
                  class="px-2 py-0.5 text-xs font-medium text-purple-700 bg-purple-100 rounded"
                >
                  Bài kiểm tra
                </span>
                <h3 class="font-medium text-gray-900 truncate">
                  {{ item.title }}
                </h3>
              </div>
              <p class="text-sm text-gray-500 truncate">
                {{ item.quizData?.ThietLapJson?.soCauHoi || 0 }} câu hỏi •
                {{ item.quizData?.ThietLapJson?.thoiGianLamBai || 0 }} phút •
                Điểm đạt: {{ item.quizData?.DiemDat || 0 }}%
              </p>
            </div>

            <!-- Quiz Actions -->
            <div
              class="flex gap-1 items-center opacity-0 transition-opacity group-hover:opacity-100"
            >
              <button
                @click="editQuiz(item.quizData)"
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
              <button
                @click="confirmDeleteQuiz(item.quizData)"
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

          <!-- Content Item -->
          <ContentItem
            v-else
            :content="item"
            :index="index"
            @edit="editContent"
            @delete="confirmDeleteContent"
            @preview="previewContent"
          />
        </template>
      </div>
    </div>

    <!-- Add/Edit Content Modal -->
    <FormAddModal
      v-model="showContentModal"
      :title="isEditing ? 'Sửa nội dung' : 'Thêm nội dung mới'"
      :submit-text="isEditing ? 'Cập nhật' : 'Thêm nội dung'"
      :fields="contentFormFields"
      :initial-data="editingContent"
      :loading="isSubmitting"
      size="xl"
      @submit="handleContentSubmit"
    />

    <!-- Preview Modal -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="showPreview"
          class="flex fixed inset-0 z-50 justify-center items-center p-4 bg-black/60"
          @click.self="showPreview = false"
        >
          <div
            class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden"
          >
            <div class="flex justify-between items-center p-4 border-b">
              <h3 class="text-lg font-semibold">Xem trước nội dung</h3>
              <button
                @click="showPreview = false"
                class="p-2 rounded-lg hover:bg-gray-100"
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
                    d="M6 18 18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
              <!-- Video Preview -->
              <div
                v-if="previewData?.type === 'video'"
                class="overflow-hidden bg-black rounded-lg aspect-video"
              >
                <iframe
                  v-if="previewData.content"
                  :src="getEmbedUrl(previewData.content)"
                  class="w-full h-full"
                  frameborder="0"
                  allowfullscreen
                ></iframe>
              </div>
              <!-- Heading Preview -->
              <div
                v-else-if="previewData?.type === 'heading'"
                class="max-w-none prose"
              >
                <h1 class="text-3xl font-bold">{{ previewData.content }}</h1>
              </div>
              <!-- Subheading Preview -->
              <div
                v-else-if="previewData?.type === 'subheading'"
                class="max-w-none prose"
              >
                <h2 class="text-2xl font-semibold">
                  {{ previewData.content }}
                </h2>
              </div>
              <!-- Paragraph/Text Preview -->
              <div
                v-else-if="previewData?.type === 'paragraph'"
                class="max-w-none prose"
                v-html="previewData.content"
              ></div>
              <!-- Quote Preview -->
              <div
                v-else-if="previewData?.type === 'quote'"
                class="pl-4 italic text-gray-700 border-l-4 border-primary"
              >
                {{ previewData.content }}
              </div>
              <!-- List Preview -->
              <div
                v-else-if="previewData?.type === 'list'"
                class="max-w-none prose"
                v-html="previewData.content"
              ></div>
              <!-- Image Preview -->
              <div
                v-else-if="previewData?.type === 'image'"
                class="text-center"
              >
                <img
                  :src="previewData.content"
                  class="mx-auto max-w-full rounded-lg"
                />
              </div>
              <!-- Default -->
              <div v-else class="py-8 text-center text-gray-500">
                Không có nội dung để xem trước
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete Confirm Modal -->
    <ConfirmModal
      v-model:show="showDeleteConfirm"
      title="Xóa nội dung"
      :message="`Bạn có chắc chắn muốn xóa phần nội dung này? Hành động này không thể hoàn tác.`"
      confirm-text="Xóa"
      confirm-variant="danger"
      @confirm="handleDelete"
    />

    <!-- Delete Quiz Confirm Modal -->
    <ConfirmModal
      v-model:show="showDeleteQuizConfirm"
      title="Xóa bài kiểm tra"
      :message="`Bạn có chắc chắn muốn xóa bài kiểm tra '${deletingQuiz?.TenBaiKiemTra}'? Hành động này không thể hoàn tác.`"
      confirm-text="Xóa"
      confirm-variant="danger"
      @confirm="handleDeleteQuiz"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import PageHeader from "../../../../layouts/PageHeader.vue";
import BackButton from "../../../../components/ui/BackButton.vue";
import BaseButton from "../../../../components/ui/BaseButton.vue";
import BadgeLabel from "../../../../components/admin/BadgeLabel.vue";
import FormAddModal from "../../../../components/modal/FormAddModal.vue";
import ConfirmModal from "../../../../components/modal/ConfirmModal.vue";
import ContentItem from "../../../../components/admin/CourseLesson/ContentItem.vue";
import api from "../../../../services/api";
import quizService from "../../../../services/quizService";

const route = useRoute();

// Route params
const courseId = computed(() => route.params.courseId);
const lessonId = computed(() => route.params.lessonId);

// Lesson info
const lesson = ref({
  title: "",
  description: "",
  type: "video",
  order: 1,
  duration: 0,
  status: 1,
});

// Content data
const contents = ref([]);
const lessonQuizzes = ref([]);
const isLoading = ref(false);

// Filter
const activeFilter = ref("all");
const contentFilters = [
  { value: "all", label: "Tất cả" },
  { value: "paragraph", label: "Văn bản" },
  { value: "heading", label: "Tiêu đề" },
  { value: "video", label: "Video" },
  { value: "image", label: "Hình ảnh" },
  { value: "quote", label: "Trích dẫn" },
  { value: "list", label: "Danh sách" },
  { value: "quiz", label: "Bài kiểm tra" },
];

// Modal states
const showContentModal = ref(false);
const isEditing = ref(false);
const isSubmitting = ref(false);
const editingContent = ref({});

// Preview
const showPreview = ref(false);
const previewData = ref(null);

// Delete
const showDeleteConfirm = ref(false);
const deletingContent = ref(null);
const deletingQuiz = ref(null);
const showDeleteQuizConfirm = ref(false);

// Form fields config
const contentFormFields = computed(() => [
  {
    name: "LoaiNoiDung",
    label: "Loại nội dung",
    inputType: "select",
    required: true,
    default: "paragraph",
    options: [
      { value: "heading", label: "📌 Tiêu đề chính" },
      { value: "subheading", label: "📍 Tiêu đề phụ" },
      { value: "paragraph", label: "📝 Đoạn văn bản/HTML" },
      { value: "image", label: "🖼️ Hình ảnh" },
      { value: "video", label: "🎬 Video (YouTube/Vimeo)" },
      { value: "quote", label: "💬 Trích dẫn" },
      { value: "list", label: "📋 Danh sách" },
    ],
  },
  {
    name: "TieuDe",
    label: "Tiêu đề",
    type: "text",
    placeholder: "VD: Giới thiệu về HTML",
    required: true,
    default: "",
  },
  {
    name: "NoiDung",
    label: "Nội dung",
    inputType: "textarea",
    placeholder: "Nhập nội dung văn bản, URL video, URL hình ảnh...",
    rows: 8,
    required: true,
    default: "",
  },
  {
    name: "ThuTu",
    label: "Thứ tự",
    type: "number",
    placeholder: "1",
    required: true,
    default: contents.value.length + 1,
  },
]);

// Computed - Kết hợp nội dung và quiz
const allItems = computed(() => {
  const contentItems = contents.value.map((c) => ({
    ...c,
    itemType: "content",
  }));
  const quizItems = lessonQuizzes.value.map((q) => ({
    id: `quiz-${q.id}`,
    quizId: q.id,
    type: "quiz",
    itemType: "quiz",
    title: q.TenBaiKiemTra,
    content: q.MoTa || "",
    order: q.ThuTu || 999,
    quizData: q,
  }));
  return [...contentItems, ...quizItems].sort((a, b) => a.order - b.order);
});

const filteredContents = computed(() => {
  if (activeFilter.value === "all") return allItems.value;
  if (activeFilter.value === "quiz")
    return allItems.value.filter((c) => c.type === "quiz");
  return allItems.value.filter(
    (c) => c.type === activeFilter.value && c.itemType !== "quiz"
  );
});

const typeIconClass = computed(() => {
  const classes = {
    video: "bg-blue-100 text-blue-600",
    text: "bg-green-100 text-green-600",
    quiz: "bg-purple-100 text-purple-600",
    assignment: "bg-orange-100 text-orange-600",
  };
  return classes[lesson.value.type] || "bg-blue-100 text-blue-600";
});

// Header stats for PageHeader
const headerStats = computed(() => [
  {
    iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
    value: lesson.value.duration || 0,
    label: 'phút'
  },
  {
    iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>',
    value: contents.value.length,
    label: 'phần nội dung'
  }
]);

// Fetch lesson info
const fetchLesson = async () => {
  try {
    const response = await api.get(`/lessons/${lessonId.value}`);
    if (response.data.success) {
      const data = response.data.data;
      lesson.value = {
        title: data.title,
        description: data.description,
        type: data.type || "video",
        order: data.order,
        duration: data.duration || 0,
        status: data.status || 1,
      };
    }
  } catch (error) {
    console.error("Lỗi khi tải thông tin bài học:", error);
  }
};

// Fetch contents
const fetchContents = async () => {
  isLoading.value = true;
  try {
    const response = await api.get(`/lessons/${lessonId.value}/content`);
    console.log("API Response:", response); // Debug
    if (response.success) {
      contents.value = response.contents.map((item) => ({
        id: item.id,
        type: item.type,
        title: item.title || "",
        content: item.content,
        order: item.order,
      }));
      console.log("Contents loaded:", contents.value); // Debug
    }
  } catch (error) {
    console.error("Lỗi khi tải nội dung:", error);
  } finally {
    isLoading.value = false;
  }
};

// Fetch quizzes for this lesson
const fetchLessonQuizzes = async () => {
  try {
    const response = await quizService.getQuizzesByLesson(lessonId.value);
    if (response.success && response.data) {
      lessonQuizzes.value = response.data;
      console.log("Lesson quizzes loaded:", lessonQuizzes.value);
    }
  } catch (error) {
    console.error("Lỗi khi tải bài kiểm tra:", error);
  }
};

// Actions
const openAddModal = () => {
  isEditing.value = false;
  editingContent.value = {
    ThuTu: contents.value.length + 1,
  };
  showContentModal.value = true;
};

const editContent = (content) => {
  isEditing.value = true;
  editingContent.value = {
    id: content.id,
    LoaiNoiDung: content.type,
    TieuDe: content.title || "",
    NoiDung: content.content || "",
    ThuTu: content.order,
  };
  showContentModal.value = true;
};

const previewContent = (content) => {
  previewData.value = content;
  showPreview.value = true;
};

const confirmDeleteContent = (content) => {
  deletingContent.value = content;
  showDeleteConfirm.value = true;
};

// Quiz actions
const editQuiz = (quiz) => {
  // Emit event hoặc mở modal chỉnh sửa quiz
  // Có thể redirect đến trang chỉnh sửa quiz hoặc mở modal
  console.log("Edit quiz:", quiz);
  // TODO: Implement edit quiz modal or redirect
};

const confirmDeleteQuiz = (quiz) => {
  deletingQuiz.value = quiz;
  showDeleteQuizConfirm.value = true;
};

const handleDeleteQuiz = async () => {
  if (!deletingQuiz.value) return;

  try {
    await api.delete(`/bai-kiem-tra/${deletingQuiz.value.id}`);
    console.log("Đã xóa bài kiểm tra:", deletingQuiz.value);
    fetchLessonQuizzes();
  } catch (error) {
    console.error("Lỗi khi xóa bài kiểm tra:", error);
    alert("Không thể xóa bài kiểm tra. Vui lòng thử lại.");
  } finally {
    deletingQuiz.value = null;
    showDeleteQuizConfirm.value = false;
  }
};

// Handle form submit
const handleContentSubmit = async (formData) => {
  isSubmitting.value = true;
  try {
    const apiData = {
      LoaiNoiDung: formData.LoaiNoiDung,
      TieuDe: formData.TieuDe || null,
      NoiDung: formData.NoiDung || null,
      ThuTu: formData.ThuTu,
    };

    console.log("Submitting data:", apiData); // Debug

    if (isEditing.value) {
      await api.put(`/lesson-contents/${editingContent.value.id}`, apiData);
      console.log("Đã cập nhật nội dung:", apiData);
    } else {
      await api.post(`/lessons/${lessonId.value}/contents`, apiData);
      console.log("Đã thêm nội dung:", apiData);
    }

    showContentModal.value = false;
    fetchContents();
  } catch (error) {
    console.error("Lỗi khi lưu nội dung:", error);
    alert("Không thể lưu nội dung. Vui lòng thử lại.");
  } finally {
    isSubmitting.value = false;
  }
};

// Handle delete
const handleDelete = async () => {
  if (!deletingContent.value) return;

  try {
    await api.delete(`/lesson-contents/${deletingContent.value.id}`);
    console.log("Đã xóa nội dung:", deletingContent.value);
    fetchContents();
  } catch (error) {
    console.error("Lỗi khi xóa nội dung:", error);
    alert("Không thể xóa nội dung. Vui lòng thử lại.");
  } finally {
    deletingContent.value = null;
  }
};

// Handle drag end - update order
const onDragEnd = async () => {
  try {
    // Update order for all items
    const updates = contents.value.map((item, index) => ({
      id: item.id,
      order: index + 1,
    }));

    await api.put(`/lessons/${lessonId.value}/contents/order`, {
      items: updates,
    });
    console.log("Đã cập nhật thứ tự");
  } catch (error) {
    console.error("Lỗi khi cập nhật thứ tự:", error);
  }
};

// Get embed URL for videos
const getEmbedUrl = (url) => {
  if (!url) return "";

  // YouTube
  const youtubeMatch = url.match(
    /(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/
  );
  if (youtubeMatch) {
    return `https://www.youtube.com/embed/${youtubeMatch[1]}`;
  }

  // Vimeo
  const vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
  if (vimeoMatch) {
    return `https://player.vimeo.com/video/${vimeoMatch[1]}`;
  }

  return url;
};

// Init
onMounted(() => {
  fetchLesson();
  fetchContents();
  fetchLessonQuizzes();
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
