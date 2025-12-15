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

      <!-- Course Info Header -->
      <div class="p-6 bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex gap-4 items-start">
          <div
            class="flex justify-center items-center w-16 h-16 text-purple-600 bg-purple-100 rounded-xl"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-8 h-8"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"
              />
            </svg>
          </div>

          <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800">Ngân hàng câu hỏi</h1>
            <p class="mt-1 text-gray-600">{{ course.title }}</p>
            <div class="flex gap-4 items-center mt-3 text-sm text-gray-500">
              <span class="flex gap-1 items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="w-4 h-4"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"
                  />
                </svg>
                {{ questionBanks.length }} ngân hàng
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions Bar -->
    <div
      class="flex flex-col gap-4 justify-between items-start mb-6 sm:flex-row sm:items-center"
    >
      <!-- Search -->
      <SearchInput
        v-model="searchQuery"
        placeholder="Tìm kiếm ngân hàng câu hỏi..."
        size="lg"
        container-class="w-full sm:w-80"
        realtime
        :debounce="300"
      />

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
        Tạo ngân hàng mới
      </BaseButton>
    </div>

    <!-- Question Banks List -->
    <LessonList
      :lessons="filteredQuestionBanks"
      :loading="isLoading"
      loading-text="Đang tải ngân hàng câu hỏi..."
      empty-title="Chưa có ngân hàng câu hỏi nào"
      empty-subtitle="Tạo ngân hàng đầu tiên để bắt đầu thêm câu hỏi"
      @view="viewQuestionBank"
      @edit="editQuestionBank"
      @delete="deleteQuestionBank"
    >
      <template #empty-action>
        <BaseButton
          @click="openAddModal"
          variant="primary"
          size="sm"
          class="mt-4"
        >
          Tạo ngân hàng đầu tiên
        </BaseButton>
      </template>
    </LessonList>

    <!-- Add/Edit Question Bank Modal -->
    <FormAddModal
      v-model="showBankModal"
      :title="isEditing ? 'Sửa ngân hàng câu hỏi' : 'Tạo ngân hàng câu hỏi'"
      :submit-text="isEditing ? 'Cập nhật' : 'Tạo ngân hàng'"
      :fields="bankFormFields"
      :initial-data="editingBank"
      :loading="isSubmitting"
      size="md"
      @submit="handleBankSubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import SearchInput from "../../../components/ui/SearchInput.vue";
import BaseButton from "../../../components/ui/BaseButton.vue";
import BackButton from "../../../components/ui/BackButton.vue";
import FormAddModal from "../../../components/modal/FormAddModal.vue";
import LessonList from "../../../components/admin/CourseLesson/LessonList.vue";
import api from "../../../services/api";

const route = useRoute();
const router = useRouter();

// Course ID from route
const courseId = computed(() => route.params.id);

// Course info
const course = ref({
  title: "",
  description: "",
});

// Question banks data
const questionBanks = ref([]);
const isLoading = ref(false);
const searchQuery = ref("");

// Modal states
const showBankModal = ref(false);
const isEditing = ref(false);
const isSubmitting = ref(false);
const editingBank = ref({});

// Form fields
const bankFormFields = [
  {
    name: "tenNganHang",
    label: "Tên ngân hàng",
    type: "text",
    placeholder: "VD: Câu hỏi HTML cơ bản",
    required: true,
    default: "",
  },
  {
    name: "moTa",
    label: "Mô tả",
    inputType: "textarea",
    placeholder: "Mô tả ngắn về ngân hàng câu hỏi...",
    rows: 3,
    default: "",
  },
  {
    name: "capDoMacDinh",
    label: "Cấp độ mặc định",
    inputType: "select",
    required: true,
    default: 1,
    options: [
      { value: 1, label: "Dễ" },
      { value: 2, label: "Trung bình" },
      { value: 3, label: "Khó" },
    ],
  },
];

// Computed - Map ngân hàng câu hỏi sang format của LessonList
const filteredQuestionBanks = computed(() => {
  let banks = questionBanks.value;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    banks = banks.filter(
      (bank) =>
        bank.title.toLowerCase().includes(query) ||
        (bank.description && bank.description.toLowerCase().includes(query))
    );
  }

  return banks;
});

// Fetch course info
const fetchCourse = async () => {
  try {
    const response = await api.get(`/courses/${courseId.value}`);
    if (response.success) {
      course.value = {
        title: response.data.title,
        description: response.data.description,
      };
    }
  } catch (error) {
    console.error("Lỗi khi tải thông tin khóa học:", error);
  }
};

// Fetch question banks
const fetchQuestionBanks = async () => {
  isLoading.value = true;
  try {
    console.log("Fetching question banks for course:", courseId.value);
    const response = await api.get(
      `/ngan-hang-cau-hoi/khoa-hoc/${courseId.value}`
    );
    console.log("API Response:", response);

    // Kiểm tra response structure
    const data = Array.isArray(response.data)
      ? response.data
      : Array.isArray(response)
      ? response
      : [];
    console.log("Data to map:", data);

    // Map dữ liệu từ API sang format của LessonList
    questionBanks.value = data.map((bank) => ({
      id: bank.id,
      title: bank.tenNganHang,
      description: bank.moTa,
      type: "quiz", // Đặt type là quiz để icon hiển thị đúng
      order: bank.id,
      duration: bank.soCauHoi || 0, // Dùng số câu hỏi thay cho duration
      status: 1,
      // Giữ lại dữ liệu gốc để dùng khi edit
      _raw: bank,
    }));
    console.log("Loaded question banks:", questionBanks.value);
  } catch (error) {
    console.error("Lỗi khi tải ngân hàng câu hỏi:", error);
    console.error("Error details:", error.response || error);
  } finally {
    isLoading.value = false;
  }
};

// Actions
const openAddModal = () => {
  isEditing.value = false;
  editingBank.value = {};
  showBankModal.value = true;
};

const viewQuestionBank = (bank) => {
  router.push(`/quan-ly/ngan-hang-cau-hoi/${bank.id}/cau-hoi`);
};

const editQuestionBank = (bank) => {
  isEditing.value = true;
  const rawData = bank._raw || {};
  editingBank.value = {
    id: bank.id,
    tenNganHang: bank.title,
    moTa: bank.description || "",
    capDoMacDinh: rawData.capDoMacDinh || 1,
  };
  showBankModal.value = true;
};

const deleteQuestionBank = async (bank) => {
  if (!confirm(`Bạn có chắc muốn xóa ngân hàng "${bank.title}"?`)) return;

  try {
    await api.delete(`/ngan-hang-cau-hoi/${bank.id}`);
    console.log("Đã xóa ngân hàng:", bank);
    fetchQuestionBanks();
  } catch (error) {
    console.error("Lỗi khi xóa ngân hàng:", error);
    alert("Không thể xóa ngân hàng. Vui lòng thử lại.");
  }
};

const handleBankSubmit = async (formData) => {
  isSubmitting.value = true;
  try {
    const apiData = {
      khoaHocId: courseId.value,
      tenNganHang: formData.tenNganHang,
      moTa: formData.moTa,
      capDoMacDinh: formData.capDoMacDinh,
    };

    if (isEditing.value) {
      await api.put(`/ngan-hang-cau-hoi/${editingBank.value.id}`, apiData);
      console.log("Đã cập nhật ngân hàng:", apiData);
    } else {
      await api.post("/ngan-hang-cau-hoi", apiData);
      console.log("Đã tạo ngân hàng:", apiData);
    }

    showBankModal.value = false;
    fetchQuestionBanks();
  } catch (error) {
    console.error("Lỗi khi lưu ngân hàng:", error);
    alert("Không thể lưu ngân hàng. Vui lòng thử lại.");
  } finally {
    isSubmitting.value = false;
  }
};

// Init
onMounted(() => {
  fetchCourse();
  fetchQuestionBanks();
});
</script>
