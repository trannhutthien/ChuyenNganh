<!--
/**
 * FILE: components/modal/QuizAddModal.vue
 * MÔ TẢ: Modal thêm/tạo bài kiểm tra mới
 * CHỨC NĂNG:
 * - Form tạo bài kiểm tra
 * - Fields: Tiêu đề, mô tả, thời gian, số câu hỏi
 * - Chọn ngân hàng câu hỏi
 * - Cấu hình thiết lập (xáo trộn, hiển thị đáp án...)
 * - Validation
 * - Submit để tạo quiz
 * ỨNG DỤNG:
 * - Admin QuizManagementPage: Tạo bài kiểm tra mới
 * - CourseDetailPage: Thêm quiz vào khóa học
 */
-->
<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
        @click.self="$emit('update:modelValue', false)"
      >
        <div
          class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col"
        >
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-xl font-semibold">
              {{ isEditing ? "Sửa bài kiểm tra" : "Tạo bài kiểm tra mới" }}
            </h3>
            <button
              @click="$emit('update:modelValue', false)"
              class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
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

          <!-- Body -->
          <div class="flex-1 overflow-y-auto p-6 space-y-4">
            <!-- Tiêu đề -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Tiêu đề bài kiểm tra <span class="text-red-500">*</span>
              </label>
              <input
                v-model="formData.tieuDe"
                type="text"
                placeholder="VD: Kiểm tra kiến thức HTML cơ bản"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              />
            </div>

            <!-- Bài học (optional) -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Gắn với bài học (Để trống nếu là bài kiểm tra cuối khóa)
              </label>
              <select
                v-model="formData.baiHocId"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              >
                <option :value="null">Bài kiểm tra cuối khóa</option>
                <option v-for="lesson in lessons" :key="lesson.id" :value="lesson.id">
                  Bài {{ lesson.order }}: {{ lesson.title }}
                </option>
              </select>
            </div>

            <!-- Ngân hàng câu hỏi -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Ngân hàng câu hỏi <span class="text-red-500">*</span>
              </label>
              <select
                v-model="formData.nganHangId"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              >
                <option :value="null">-- Chọn ngân hàng câu hỏi --</option>
                <option
                  v-for="bank in questionBanks"
                  :key="bank.id"
                  :value="bank.id"
                >
                  {{ bank.tenNganHang }} ({{ bank.soCauHoi }} câu hỏi)
                </option>
              </select>
            </div>

            <!-- Số câu hỏi -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Số câu hỏi <span class="text-red-500">*</span>
              </label>
              <input
                v-model.number="formData.soCauHoi"
                type="number"
                min="1"
                placeholder="10"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              />
              <p class="mt-1 text-xs text-gray-500">
                Số câu hỏi sẽ được random từ ngân hàng
              </p>
            </div>

            <!-- Thời gian làm bài -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Thời gian làm bài (phút) <span class="text-red-500">*</span>
              </label>
              <input
                v-model.number="formData.thoiGianPhut"
                type="number"
                min="1"
                placeholder="30"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              />
            </div>

            <!-- Điểm đạt -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Điểm đạt (%) <span class="text-red-500">*</span>
              </label>
              <input
                v-model.number="formData.diemDat"
                type="number"
                min="0"
                max="100"
                placeholder="70"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              />
              <p class="mt-1 text-xs text-gray-500">
                Học viên cần đạt ít nhất {{ formData.diemDat || 0 }}% để pass
              </p>
            </div>

            <!-- Số lần làm tối đa -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Số lần làm tối đa
              </label>
              <input
                v-model.number="formData.soLanLamToiDa"
                type="number"
                min="0"
                placeholder="0 = Không giới hạn"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              />
              <p class="mt-1 text-xs text-gray-500">
                0 = Không giới hạn số lần làm
              </p>
            </div>

            <!-- Trạng thái -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Trạng thái <span class="text-red-500">*</span>
              </label>
              <select
                v-model.number="formData.trangThai"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              >
                <option :value="1">Nháp</option>
                <option :value="2">Công khai</option>
                <option :value="3">Đóng</option>
              </select>
            </div>
          </div>

          <!-- Footer -->
          <div class="flex items-center justify-end gap-3 p-6 border-t">
            <button
              @click="$emit('update:modelValue', false)"
              type="button"
              class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              @click="handleSubmit"
              :disabled="loading || !isValid"
              type="button"
              class="px-4 py-2 text-white bg-primary rounded-lg hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ loading ? "Đang lưu..." : isEditing ? "Cập nhật" : "Tạo bài kiểm tra" }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from "vue";

const props = defineProps({
  modelValue: Boolean,
  isEditing: Boolean,
  initialData: Object,
  loading: Boolean,
  lessons: {
    type: Array,
    default: () => [],
  },
  questionBanks: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(["update:modelValue", "submit"]);

const formData = ref({
  tieuDe: "",
  baiHocId: null,
  nganHangId: null,
  soCauHoi: 10,
  thoiGianPhut: 30,
  diemDat: 70,
  soLanLamToiDa: 0,
  trangThai: 1,
});

// Watch for initial data changes
watch(
  () => props.initialData,
  (newData) => {
    if (newData && Object.keys(newData).length > 0) {
      formData.value = { ...newData };
    } else {
      formData.value = {
        tieuDe: "",
        baiHocId: null,
        nganHangId: null,
        soCauHoi: 10,
        thoiGianPhut: 30,
        diemDat: 70,
        soLanLamToiDa: 0,
        trangThai: 1,
      };
    }
  },
  { immediate: true, deep: true }
);

// Validation
const isValid = computed(() => {
  return (
    formData.value.tieuDe.trim() !== "" &&
    formData.value.nganHangId !== null &&
    formData.value.soCauHoi > 0 &&
    formData.value.thoiGianPhut > 0 &&
    formData.value.diemDat >= 0 &&
    formData.value.diemDat <= 100
  );
});

const handleSubmit = () => {
  if (!isValid.value) {
    alert("Vui lòng điền đầy đủ thông tin bắt buộc!");
    return;
  }

  emit("submit", formData.value);
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
