<template>
  <div class="p-6">
    <!-- Back Button & Header -->
    <div class="mb-6">
      <BackButton
        :to="`/quan-ly/khoa-hoc/${courseId}/ngan-hang-cau-hoi`"
        container-class="mb-4"
      >
        Quay lại ngân hàng câu hỏi
      </BackButton>

      <!-- Question Bank Info Header -->
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
                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
              />
            </svg>
          </div>

          <div class="flex-1">
            <div class="flex gap-3 items-center mb-2">
              <span
                :class="[
                  'px-3 py-1 rounded-full text-sm font-medium',
                  levelClass,
                ]"
              >
                {{ levelText }}
              </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">
              {{ questionBank.name }}
            </h1>
            <p class="mt-1 text-gray-600">
              {{
                questionBank.description || "Quản lý câu hỏi trong ngân hàng"
              }}
            </p>
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
                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                  />
                </svg>
                {{ questions.length }} câu hỏi
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
      <!-- Filters -->
      <div class="flex flex-wrap gap-2 items-center">
        <button
          v-for="filter in questionFilters"
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
        Thêm câu hỏi
      </BaseButton>
    </div>

    <!-- Questions List -->
    <div
      class="overflow-hidden bg-white rounded-xl border border-gray-100 shadow-sm"
    >
      <!-- Loading State -->
      <div v-if="isLoading" class="p-8 text-center">
        <div
          class="mx-auto w-10 h-10 rounded-full border-b-2 animate-spin border-primary"
        ></div>
        <p class="mt-4 text-gray-500">Đang tải câu hỏi...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredQuestions.length === 0" class="p-12 text-center">
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
            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
          />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">
          Chưa có câu hỏi nào
        </h3>
        <p class="mt-2 text-gray-500">Bắt đầu thêm câu hỏi vào ngân hàng này</p>
        <BaseButton
          @click="openAddModal"
          variant="primary"
          size="sm"
          class="mt-4"
        >
          Thêm câu hỏi đầu tiên
        </BaseButton>
      </div>

      <!-- Question Items -->
      <div v-else class="divide-y divide-gray-100">
        <QuestionItem
          v-for="(question, index) in filteredQuestions"
          :key="question.id"
          :question="question"
          :index="index"
          @edit="editQuestion"
          @delete="confirmDeleteQuestion"
          @preview="previewQuestion"
        />
      </div>
    </div>

    <!-- Add/Edit Question Modal -->
    <QuestionFormModal
      v-model="showQuestionModal"
      :is-editing="isEditing"
      :initial-data="editingQuestion"
      :initial-choices="choices"
      :loading="isSubmitting"
      @submit="handleQuestionSubmit"
    />

    <!-- Delete Confirm Modal -->
    <ConfirmModal
      v-model:show="showDeleteConfirm"
      title="Xóa câu hỏi"
      :message="`Bạn có chắc chắn muốn xóa câu hỏi này? Hành động này không thể hoàn tác.`"
      confirm-text="Xóa"
      confirm-variant="danger"
      @confirm="handleDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import BackButton from "../../../../components/ui/BackButton.vue";
import BaseButton from "../../../../components/ui/BaseButton.vue";
import ConfirmModal from "../../../../components/modal/ConfirmModal.vue";
import QuestionItem from "../../../../components/admin/CourseLesson/QuestionItem.vue";
import QuestionFormModal from "../../../../components/admin/CourseLesson/QuestionFormModal.vue";
import api from "../../../../services/api";

const route = useRoute();
const router = useRouter();

// Route params
const courseId = computed(() => route.params.courseId);
const questionBankId = computed(() => route.params.id);

// Question Bank info
const questionBank = ref({
  name: "",
  description: "",
  level: 1,
});

// Questions data
const questions = ref([]);
const isLoading = ref(false);

// Filter
const activeFilter = ref("all");
const questionFilters = [
  { value: "all", label: "Tất cả" },
  { value: "single", label: "Một đáp án" },
  { value: "multiple", label: "Nhiều đáp án" },
  { value: "true_false", label: "Đúng/Sai" },
  { value: "fill_blank", label: "Điền khuyết" },
];

// Modal states
const showQuestionModal = ref(false);
const isEditing = ref(false);
const isSubmitting = ref(false);
const editingQuestion = ref({});

// Choices management
const choices = ref([
  { noiDung: "", dungHaySai: false, thuTu: 1 },
  { noiDung: "", dungHaySai: false, thuTu: 2 },
]);

// Delete
const showDeleteConfirm = ref(false);
const deletingQuestion = ref(null);

// Computed
const levelClass = computed(() => {
  const level = questionBank.value.level || 1;
  const classes = {
    1: "bg-green-100 text-green-700",
    2: "bg-yellow-100 text-yellow-700",
    3: "bg-red-100 text-red-700",
  };
  return classes[level] || classes[1];
});

const levelText = computed(() => {
  const level = questionBank.value.level || 1;
  const texts = {
    1: "Dễ",
    2: "Trung bình",
    3: "Khó",
  };
  return texts[level] || texts[1];
});

const filteredQuestions = computed(() => {
  if (activeFilter.value === "all") return questions.value;
  return questions.value.filter((q) => q.type === activeFilter.value);
});

// Fetch question bank info
const fetchQuestionBank = async () => {
  try {
    console.log("Fetching question bank:", questionBankId.value);
    const response = await api.get(
      `/ngan-hang-cau-hoi/${questionBankId.value}`
    );
    console.log("Question bank response:", response);
    questionBank.value = {
      name: response.tenNganHang,
      description: response.moTa,
      level: response.capDoMacDinh || 1,
    };
  } catch (error) {
    console.error("Lỗi khi tải thông tin ngân hàng:", error);
    console.error("Error details:", error.response || error);
  }
};

// Fetch questions
const fetchQuestions = async () => {
  isLoading.value = true;
  try {
    console.log("Fetching questions for bank:", questionBankId.value);
    const response = await api.get(
      `/ngan-hang-cau-hoi/${questionBankId.value}/cau-hoi`
    );
    console.log("API Response:", response);

    // Kiểm tra response structure
    const data = Array.isArray(response.data)
      ? response.data
      : Array.isArray(response)
      ? response
      : [];
    console.log("Data to map:", data);

    questions.value = data.map((q) => ({
      id: q.id,
      type: q.loai,
      question: q.deBai,
      explanation: q.giaiThich,
      difficulty: q.doKho,
      tags: q.chuDeTags,
      order: q.thuTu,
      choices: q.luaChons || [],
    }));
    console.log("Loaded questions:", questions.value);
  } catch (error) {
    console.error("Lỗi khi tải câu hỏi:", error);
    console.error("Error details:", error.response || error);
  } finally {
    isLoading.value = false;
  }
};

// Actions
const openAddModal = () => {
  isEditing.value = false;
  editingQuestion.value = {};

  // Reset choices
  choices.value = [
    { noiDung: "", dungHaySai: false, thuTu: 1 },
    { noiDung: "", dungHaySai: false, thuTu: 2 },
  ];

  showQuestionModal.value = true;
};

const editQuestion = (question) => {
  isEditing.value = true;
  editingQuestion.value = {
    id: question.id,
    loai: question.type,
    deBai: question.question,
    giaiThich: question.explanation || "",
    doKho: question.difficulty || 1,
    chuDeTags: question.tags || "",
    thuTu: question.order || 1,
  };

  // Load choices
  if (question.choices && question.choices.length > 0) {
    choices.value = question.choices.map((c) => ({
      noiDung: c.noiDung,
      dungHaySai: c.dungHaySai,
      thuTu: c.thuTu,
    }));
  } else {
    choices.value = [
      { noiDung: "", dungHaySai: false, thuTu: 1 },
      { noiDung: "", dungHaySai: false, thuTu: 2 },
    ];
  }

  showQuestionModal.value = true;
};

const previewQuestion = (question) => {
  // TODO: Mở preview modal để xem câu hỏi dạng read-only
  console.log("Preview question:", question);
  alert(
    "Chức năng xem trước đang được phát triển. Hiện tại hãy dùng nút Sửa để xem chi tiết câu hỏi."
  );
};

const confirmDeleteQuestion = (question) => {
  deletingQuestion.value = question;
  showDeleteConfirm.value = true;
};

// Handle form submit
const handleQuestionSubmit = async (data) => {
  isSubmitting.value = true;
  try {
    // Validate choices
    const validChoices = data.luaChons.filter((c) => c.noiDung.trim() !== "");
    if (validChoices.length < 2) {
      alert("Vui lòng thêm ít nhất 2 lựa chọn");
      isSubmitting.value = false;
      return;
    }

    // Ensure dungHaySai is boolean
    const formattedChoices = validChoices.map((c, index) => ({
      noiDung: c.noiDung.trim(),
      dungHaySai: Boolean(c.dungHaySai),
      thuTu: c.thuTu || index + 1,
    }));

    const apiData = {
      loai: data.loai,
      deBai: data.deBai.trim(),
      giaiThich: data.giaiThich?.trim() || null,
      doKho: parseInt(data.doKho) || 1,
      chuDeTags: data.chuDeTags?.trim() || null,
      thuTu: parseInt(data.thuTu) || 1,
      luaChons: formattedChoices,
    };

    console.log("Submitting data:", apiData);

    if (isEditing.value) {
      await api.put(
        `/ngan-hang-cau-hoi/${questionBankId.value}/cau-hoi/${editingQuestion.value.id}`,
        apiData
      );
      console.log("Đã cập nhật câu hỏi:", apiData);
    } else {
      await api.post(
        `/ngan-hang-cau-hoi/${questionBankId.value}/cau-hoi`,
        apiData
      );
      console.log("Đã thêm câu hỏi:", apiData);
    }

    showQuestionModal.value = false;
    fetchQuestions();
  } catch (error) {
    console.error("Lỗi khi lưu câu hỏi:", error);

    // api.js interceptor trả về data thay vì error.response
    // Nên error chính là data từ backend
    const errors = error.errors || error.response?.data?.errors;
    const message = error.message || error.response?.data?.message;

    console.error("Validation errors:", errors);
    console.error("Error message:", message);

    // Log chi tiết từng field error
    if (errors) {
      Object.entries(errors).forEach(([field, messages]) => {
        console.error(`Field "${field}":`, messages);
      });
    }

    let errorMsg = "Không thể lưu câu hỏi. Vui lòng thử lại.";

    if (errors) {
      // Format validation errors
      errorMsg = Object.entries(errors)
        .map(([field, messages]) => `${field}: ${messages.join(", ")}`)
        .join("\n");
    } else if (message) {
      errorMsg = message;
    }

    alert(errorMsg);
  } finally {
    isSubmitting.value = false;
  }
};

// Handle delete
const handleDelete = async () => {
  if (!deletingQuestion.value) return;

  try {
    await api.delete(
      `/ngan-hang-cau-hoi/${questionBankId.value}/cau-hoi/${deletingQuestion.value.id}`
    );
    console.log("Đã xóa câu hỏi:", deletingQuestion.value);
    fetchQuestions();
  } catch (error) {
    console.error("Lỗi khi xóa câu hỏi:", error);
    alert("Không thể xóa câu hỏi. Vui lòng thử lại.");
  } finally {
    deletingQuestion.value = null;
  }
};

// Init
onMounted(() => {
  fetchQuestionBank();
  fetchQuestions();
});
</script>
