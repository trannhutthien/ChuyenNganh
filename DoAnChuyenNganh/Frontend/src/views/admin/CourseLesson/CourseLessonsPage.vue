<template>
  <div class="p-6">
    <!-- Back Button & Header -->
    <div class="mb-6">
      <BackButton to="/quan-ly/khoa-hoc" container-class="mb-4">
        Quay lại danh sách khóa học
      </BackButton>

      <!-- Course Info -->
      <LessonHeader
        :title="course.title"
        :thumbnail="course.thumbnail"
        :lesson-count="lessons.length"
        :total-duration="totalDuration"
      />
    </div>

    <!-- Actions Bar -->
    <div
      class="flex flex-col gap-4 justify-between items-start mb-6 sm:flex-row sm:items-center"
    >
      <!-- Search -->
      <SearchInput
        v-model="searchQuery"
        placeholder="Tìm kiếm bài học..."
        size="lg"
        container-class="w-full sm:w-80"
        realtime
        :debounce="300"
      />

      <!-- Action Buttons -->
      <div class="flex gap-3 items-center">
        <BaseButton @click="goToQuestionBank" variant="outline" size="sm">
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
                d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"
              />
            </svg>
          </template>
          Ngân hàng câu hỏi
        </BaseButton>

        <BaseButton @click="goToCreateQuiz" variant="outline" size="sm">
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
                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"
              />
            </svg>
          </template>
          Tạo bài kiểm tra
        </BaseButton>

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
          Thêm bài học
        </BaseButton>
      </div>
    </div>

    <!-- Lessons List -->
    <LessonList
      :lessons="filteredLessons"
      :loading="isLoading"
      @view="viewLesson"
      @edit="editLesson"
      @delete="deleteLesson"
    >
      <template #empty-action>
        <BaseButton
          @click="openAddModal"
          variant="primary"
          size="sm"
          class="mt-4"
        >
          Thêm bài học đầu tiên
        </BaseButton>
      </template>
    </LessonList>

    <!-- Add/Edit Lesson Modal -->
    <FormAddModal
      v-model="showLessonModal"
      :title="isEditing ? 'Sửa bài học' : 'Thêm bài học mới'"
      :submit-text="isEditing ? 'Cập nhật' : 'Thêm bài học'"
      :fields="lessonFormFields"
      :initial-data="editingLesson"
      :loading="isSubmitting"
      size="lg"
      @submit="handleLessonSubmit"
    />

    <!-- Add/Edit Quiz Modal -->
    <FormAddQuizModal
      v-model="showQuizModal"
      :khoa-hoc-id="courseId"
      :bai-hocs="lessons"
      :ngan-hangs="nganHangs"
      :initial-data="editingQuiz"
      :loading="isSubmittingQuiz"
      @submit="handleQuizSubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import SearchInput from "../../../components/ui/SearchInput.vue";
import BaseButton from "../../../components/ui/BaseButton.vue";
import BackButton from "../../../components/ui/BackButton.vue";
import LessonHeader from "../../../components/admin/CourseLesson/LessonHeader.vue";
import LessonList from "../../../components/admin/CourseLesson/LessonList.vue";
import FormAddModal from "../../../components/modal/FormAddModal.vue";
import FormAddQuizModal from "../../../components/modal/FormAddQuizModal.vue";
import { courseService } from "../../../services/courseService";
import api from "../../../services/api";

const route = useRoute();
const router = useRouter();

// Course ID from route
const courseId = computed(() => route.params.id);

// Lesson Form Fields Config
const lessonFormFields = computed(() => [
  {
    name: "TieuDe",
    label: "Tiêu đề bài học",
    type: "text",
    placeholder: "VD: Giới thiệu về HTML",
    required: true,
    default: "",
  },
  {
    name: "LoaiBaiHoc",
    label: "Loại bài học",
    inputType: "select",
    required: true,
    default: "video",
    options: [
      { value: "video", label: "Video bài giảng" },
      { value: "text", label: "Bài viết/Văn bản" },
      { value: "quiz", label: "Bài kiểm tra" },
      { value: "assignment", label: "Bài tập" },
    ],
  },
  {
    name: "MoTa",
    label: "Mô tả ngắn",
    inputType: "textarea",
    placeholder: "Mô tả nội dung bài học...",
    rows: 3,
    default: "",
  },
  {
    name: "VideoUrl",
    label: "URL Video",
    type: "text",
    placeholder: "https://youtube.com/watch?v=...",
    default: "",
    showIf: (form) => form.LoaiBaiHoc === "video",
  },
  {
    name: "NoiDung",
    label: "Nội dung bài học",
    inputType: "textarea",
    placeholder: "Nhập nội dung bài học...",
    rows: 6,
    default: "",
    showIf: (form) => form.LoaiBaiHoc === "text",
  },
  {
    name: "ThuTu",
    label: "Thứ tự",
    type: "number",
    placeholder: "1",
    required: true,
    default: lessons.value.length + 1,
  },
  {
    name: "ThoiLuong",
    label: "Thời lượng (phút)",
    type: "number",
    placeholder: "30",
    default: 0,
  },
  {
    name: "TrangThai",
    label: "Trạng thái",
    inputType: "select",
    required: true,
    default: 1,
    options: [
      { value: 1, label: "Hoạt động" },
      { value: 0, label: "Nháp" },
      { value: -1, label: "Ẩn" },
    ],
  },
]);

// Course info
const course = ref({
  title: "",
  thumbnail: "",
  description: "",
});

// Lessons data
const lessons = ref([]);
const isLoading = ref(false);
const searchQuery = ref("");

// Modal states
const showLessonModal = ref(false);
const isEditing = ref(false);
const isSubmitting = ref(false);
const editingLesson = ref({});

// Quiz Modal states
const showQuizModal = ref(false);
const isSubmittingQuiz = ref(false);
const editingQuiz = ref(null);
const nganHangs = ref([]);

// Computed
const totalDuration = computed(() => {
  return lessons.value.reduce((sum, lesson) => sum + (lesson.duration || 0), 0);
});

const filteredLessons = computed(() => {
  if (!searchQuery.value) return lessons.value;
  const query = searchQuery.value.toLowerCase();
  return lessons.value.filter((lesson) =>
    lesson.title.toLowerCase().includes(query)
  );
});

// Fetch course info
const fetchCourse = async () => {
  try {
    const response = await courseService.getById(courseId.value);
    if (response.success) {
      course.value = {
        title: response.data.title,
        thumbnail: response.data.thumbnail,
        description: response.data.description,
      };
    }
  } catch (error) {
    console.error("Lỗi khi tải thông tin khóa học:", error);
  }
};

// Fetch lessons
const fetchLessons = async () => {
  isLoading.value = true;
  try {
    const response = await courseService.getLessons(courseId.value);
    if (response.success) {
      lessons.value = response.data.map((lesson) => ({
        id: lesson.id,
        title: lesson.title,
        description: lesson.description,
        content: lesson.content,
        type: lesson.type || "video",
        order: lesson.order,
        duration: lesson.duration || 0,
        videoUrl: lesson.videoUrl,
        status: lesson.status || 1,
      }));
    }
  } catch (error) {
    console.error("Lỗi khi tải bài học:", error);
  } finally {
    isLoading.value = false;
  }
};

// Actions
const openAddModal = () => {
  isEditing.value = false;
  editingLesson.value = {
    order: lessons.value.length + 1,
  };
  showLessonModal.value = true;
};

const viewLesson = (lesson) => {
  // Điều hướng đến trang nội dung bài học
  router.push(
    `/quan-ly/khoa-hoc/${courseId.value}/bai-hoc/${lesson.id}/noi-dung`
  );
};

const editLesson = (lesson) => {
  isEditing.value = true;
  // Map dữ liệu từ API sang form fields (tiếng Việt)
  editingLesson.value = {
    id: lesson.id,
    TieuDe: lesson.title,
    MoTa: lesson.description || "",
    NoiDung: lesson.content || "",
    LoaiBaiHoc: lesson.type || "video",
    ThuTu: lesson.order || 1,
    ThoiLuong: lesson.duration || 0,
    VideoUrl: lesson.videoUrl || "",
    TrangThai: lesson.status || 1,
  };
  showLessonModal.value = true;
};

const deleteLesson = async (lesson) => {
  try {
    await api.delete(`/lessons/${lesson.id}`);
    console.log("Đã xóa bài học:", lesson);
    fetchLessons();
  } catch (error) {
    console.error("Lỗi khi xóa bài học:", error);
    alert("Không thể xóa bài học. Vui lòng thử lại.");
  }
};

const handleLessonSubmit = async (formData) => {
  isSubmitting.value = true;
  try {
    const apiData = {
      KhoaHocId: courseId.value,
      TieuDe: formData.TieuDe,
      MoTa: formData.MoTa,
      NoiDung: formData.NoiDung,
      LoaiBaiHoc: formData.LoaiBaiHoc,
      ThuTu: formData.ThuTu,
      ThoiLuong: formData.ThoiLuong,
      VideoUrl: formData.VideoUrl,
      TrangThai: formData.TrangThai,
    };

    if (isEditing.value) {
      await api.put(`/lessons/${editingLesson.value.id}`, apiData);
      console.log("Đã cập nhật bài học:", apiData);
    } else {
      await api.post("/lessons", apiData);
      console.log("Đã thêm bài học:", apiData);
    }

    showLessonModal.value = false;
    fetchLessons();
  } catch (error) {
    console.error("Lỗi khi lưu bài học:", error);
    alert("Không thể lưu bài học. Vui lòng thử lại.");
  } finally {
    isSubmitting.value = false;
  }
};

// Navigate to question bank
const goToQuestionBank = () => {
  router.push(`/quan-ly/khoa-hoc/${courseId.value}/ngan-hang-cau-hoi`);
};

// Open Quiz Modal
const goToCreateQuiz = async () => {
  // Fetch ngân hàng câu hỏi trước khi mở modal
  await fetchNganHangs();
  editingQuiz.value = null;
  showQuizModal.value = true;
};

// Fetch ngân hàng câu hỏi
const fetchNganHangs = async () => {
  try {
    const response = await api.get(
      `/ngan-hang-cau-hoi/khoa-hoc/${courseId.value}`
    );
    // API trả về array trực tiếp, không có wrapper success/data
    console.log("Ngân hàng câu hỏi response:", response);
    nganHangs.value = Array.isArray(response) ? response : response.data || [];
  } catch (error) {
    console.error("Lỗi khi tải ngân hàng câu hỏi:", error);
    nganHangs.value = [];
  }
};

// Handle Quiz Submit
const handleQuizSubmit = async (formData) => {
  isSubmittingQuiz.value = true;
  try {
    // Chuyển đổi từ PascalCase sang camelCase theo yêu cầu API
    const apiData = {
      khoaHocId: formData.KhoaHocId,
      baiHocId: formData.BaiHocId || null,
      tieuDe: formData.TieuDe,
      diemDat: formData.DiemDat,
      trangThai: formData.TrangThai,
      thietLapJson: formData.ThietLapJson,
    };

    console.log("Sending quiz data:", apiData);

    if (formData.BaiKiemTraId) {
      // Update
      await api.put(`/bai-kiem-tra/${formData.BaiKiemTraId}`, apiData);
      console.log("Đã cập nhật bài kiểm tra:", apiData);
    } else {
      // Create
      await api.post("/bai-kiem-tra", apiData);
      console.log("Đã tạo bài kiểm tra:", apiData);
    }
    showQuizModal.value = false;
    // Có thể refresh danh sách bài kiểm tra nếu cần
  } catch (error) {
    console.error("Lỗi khi lưu bài kiểm tra:", error);
    alert("Không thể lưu bài kiểm tra. Vui lòng thử lại.");
  } finally {
    isSubmittingQuiz.value = false;
  }
};

// Init
onMounted(() => {
  fetchCourse();
  fetchLessons();
});
</script>
