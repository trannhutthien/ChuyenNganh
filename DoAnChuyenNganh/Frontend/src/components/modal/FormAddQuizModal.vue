<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="flex fixed inset-0 z-50 justify-center items-center p-4"
        @click.self="closeModal"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 backdrop-blur-sm bg-black/50"></div>

        <!-- Modal Content -->
        <div
          class="bg-white p-6 rounded-xl shadow-xl w-full max-w-2xl relative z-10 max-h-[90vh] overflow-y-auto"
        >
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">
              {{ isEdit ? "Chỉnh sửa bài kiểm tra" : "Thêm bài kiểm tra mới" }}
            </h2>
            <button
              @click="closeModal"
              class="p-1 rounded-full transition hover:bg-gray-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-6 h-6 text-gray-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleSubmit" class="space-y-5">
            <!-- Tiêu đề bài kiểm tra -->
            <BaseInput
              v-model="form.TieuDe"
              label="Tiêu đề bài kiểm tra"
              placeholder="Nhập tiêu đề bài kiểm tra"
              required
            />

            <!-- Loại bài kiểm tra -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Loại bài kiểm tra <span class="text-red-500">*</span>
              </label>
              <div class="flex gap-4">
                <label class="flex gap-2 items-center cursor-pointer">
                  <input
                    type="radio"
                    v-model="form.LoaiBaiKiemTra"
                    value="cuoi_khoa"
                    class="w-4 h-4 text-primary focus:ring-primary"
                  />
                  <span class="text-gray-700">Bài kiểm tra cuối khóa</span>
                </label>
                <label class="flex gap-2 items-center cursor-pointer">
                  <input
                    type="radio"
                    v-model="form.LoaiBaiKiemTra"
                    value="bai_hoc"
                    class="w-4 h-4 text-primary focus:ring-primary"
                  />
                  <span class="text-gray-700">Bài kiểm tra theo bài học</span>
                </label>
              </div>
            </div>

            <!-- Chọn bài học (nếu là bài kiểm tra theo bài học) -->
            <BaseInput
              v-if="form.LoaiBaiKiemTra === 'bai_hoc'"
              v-model="form.BaiHocId"
              label="Chọn bài học"
              input-type="select"
              :options="baiHocOptions"
              required
            />

            <!-- Điểm đạt -->
            <BaseInput
              v-model="form.DiemDat"
              label="Điểm đạt (thang 10)"
              type="number"
              placeholder="5.00"
              hint="Điểm tối thiểu để đạt bài kiểm tra"
            />

            <!-- Trạng thái -->
            <BaseInput
              v-model="form.TrangThai"
              label="Trạng thái"
              input-type="select"
              :options="trangThaiOptions"
            />

            <!-- Divider -->
            <div class="pt-4 border-t border-gray-200">
              <h3 class="mb-4 text-lg font-semibold text-gray-800">
                ⚙️ Thiết lập bài kiểm tra
              </h3>
            </div>

            <!-- Số câu hỏi -->
            <BaseInput
              v-model="form.thietLap.soCauHoi"
              label="Số câu hỏi"
              type="number"
              placeholder="10"
              hint="Số câu hỏi sẽ được random từ ngân hàng câu hỏi"
            />

            <!-- Thời gian làm bài -->
            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-700">
                Thời gian làm bài (phút)
              </label>
              <input
                v-model.number="form.thietLap.thoiGianLamBai"
                type="number"
                min="5"
                placeholder="30"
                class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
              />
              <p class="text-xs text-gray-500">
                Tối thiểu 5 phút. Để 0 nếu không giới hạn thời gian
              </p>
            </div>

            <!-- Số lần làm tối đa -->
            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-700">
                Số lần làm tối đa
              </label>
              <input
                v-model.number="form.thietLap.soLanLamToiDa"
                type="number"
                min="1"
                placeholder="3"
                class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
              />
              <p class="text-xs text-gray-500">
                Tối thiểu 1 lần. Để 0 nếu không giới hạn số lần
              </p>
            </div>

            <!-- Xáo trộn câu hỏi -->
            <div class="flex gap-3 items-center">
              <input
                type="checkbox"
                id="xaoTronCauHoi"
                v-model="form.thietLap.xaoTronCauHoi"
                class="w-4 h-4 rounded text-primary focus:ring-primary"
              />
              <label
                for="xaoTronCauHoi"
                class="text-sm text-gray-700 cursor-pointer"
              >
                Xáo trộn thứ tự câu hỏi
              </label>
            </div>

            <!-- Xáo trộn đáp án -->
            <div class="flex gap-3 items-center">
              <input
                type="checkbox"
                id="xaoTronDapAn"
                v-model="form.thietLap.xaoTronDapAn"
                class="w-4 h-4 rounded text-primary focus:ring-primary"
              />
              <label
                for="xaoTronDapAn"
                class="text-sm text-gray-700 cursor-pointer"
              >
                Xáo trộn thứ tự đáp án
              </label>
            </div>

            <!-- Hiển thị đáp án sau khi nộp -->
            <div class="flex gap-3 items-center">
              <input
                type="checkbox"
                id="hienThiDapAn"
                v-model="form.thietLap.hienThiDapAn"
                class="w-4 h-4 rounded text-primary focus:ring-primary"
              />
              <label
                for="hienThiDapAn"
                class="text-sm text-gray-700 cursor-pointer"
              >
                Hiển thị đáp án đúng sau khi nộp bài
              </label>
            </div>

            <!-- Chọn ngân hàng câu hỏi -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Ngân hàng câu hỏi
              </label>
              <p class="mb-2 text-xs text-gray-500">
                Chọn các ngân hàng câu hỏi để lấy câu hỏi (để trống = lấy từ tất
                cả ngân hàng của khóa học)
              </p>
              <div
                class="overflow-y-auto p-3 space-y-2 max-h-40 rounded-lg border border-gray-200"
              >
                <label
                  v-for="nganHang in nganHangOptions"
                  :key="nganHang.value"
                  class="flex gap-2 items-center p-1 rounded cursor-pointer hover:bg-gray-50"
                >
                  <input
                    type="checkbox"
                    :value="nganHang.value"
                    v-model="form.thietLap.nganHangIds"
                    class="w-4 h-4 rounded text-primary focus:ring-primary"
                  />
                  <span class="text-sm text-gray-700">{{
                    nganHang.label
                  }}</span>
                </label>
                <p
                  v-if="nganHangOptions.length === 0"
                  class="text-sm italic text-gray-400"
                >
                  Chưa có ngân hàng câu hỏi nào
                </p>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4 border-t border-gray-200">
              <BaseButton
                type="button"
                variant="outline"
                full-width
                @click="closeModal"
              >
                Hủy
              </BaseButton>
              <BaseButton
                type="submit"
                variant="primary"
                full-width
                :loading="loading"
                :disabled="loading"
              >
                {{ isEdit ? "Cập nhật" : "Tạo bài kiểm tra" }}
              </BaseButton>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import BaseInput from "../ui/BaseInput.vue";
import BaseButton from "../ui/BaseButton.vue";

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  // ID khóa học (bắt buộc)
  khoaHocId: {
    type: [Number, String],
    required: true,
  },
  // Danh sách bài học của khóa học
  baiHocs: {
    type: Array,
    default: () => [],
  },
  // Danh sách ngân hàng câu hỏi của khóa học
  nganHangs: {
    type: Array,
    default: () => [],
  },
  // Dữ liệu ban đầu (khi edit)
  initialData: {
    type: Object,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue", "submit"]);

// Check if editing
const isEdit = computed(() => !!props.initialData?.BaiKiemTraId);

// Form data
const defaultForm = () => ({
  TieuDe: "",
  LoaiBaiKiemTra: "cuoi_khoa", // 'cuoi_khoa' hoặc 'bai_hoc'
  BaiHocId: "",
  DiemDat: 5,
  TrangThai: 2,
  thietLap: {
    soCauHoi: 10,
    thoiGianLamBai: 30,
    soLanLamToiDa: 0,
    xaoTronCauHoi: true,
    xaoTronDapAn: true,
    hienThiDapAn: true,
    nganHangIds: [],
  },
});

const form = ref(defaultForm());

// Options cho select bài học
const baiHocOptions = computed(() => {
  return [
    { value: "", label: "-- Chọn bài học --" },
    ...props.baiHocs.map((bh) => ({
      value: bh.id || bh.BaiHocId,
      label: bh.title || bh.TieuDe,
    })),
  ];
});

// Options cho select ngân hàng câu hỏi
const nganHangOptions = computed(() => {
  return props.nganHangs.map((nh) => ({
    value: nh.id || nh.NganHangId,
    label: nh.tenNganHang || nh.name || nh.TenNganHang,
  }));
});

// Options cho trạng thái
const trangThaiOptions = [
  { value: 1, label: "Nháp" },
  { value: 2, label: "Công khai" },
  { value: 3, label: "Đã đóng" },
];

// Watch modal open/close
watch(
  () => props.modelValue,
  (isOpen) => {
    if (isOpen) {
      if (props.initialData) {
        // Edit mode - load data
        const thietLap =
          props.initialData.ThietLapJson || props.initialData.thietLap || {};
        form.value = {
          TieuDe: props.initialData.TieuDe || props.initialData.title || "",
          LoaiBaiKiemTra: props.initialData.BaiHocId ? "bai_hoc" : "cuoi_khoa",
          BaiHocId: props.initialData.BaiHocId || "",
          DiemDat: props.initialData.DiemDat || 5,
          TrangThai: props.initialData.TrangThai || 2,
          thietLap: {
            soCauHoi: thietLap.soCauHoi || 10,
            thoiGianLamBai: thietLap.thoiGianLamBai || 30,
            soLanLamToiDa: thietLap.soLanLamToiDa || 0,
            xaoTronCauHoi: thietLap.xaoTronCauHoi ?? true,
            xaoTronDapAn: thietLap.xaoTronDapAn ?? true,
            hienThiDapAn: thietLap.hienThiDapAn ?? true,
            nganHangIds: thietLap.nganHangIds || [],
          },
        };
      } else {
        // Create mode - reset form
        form.value = defaultForm();
      }
    }
  },
  { immediate: true }
);

const closeModal = () => {
  emit("update:modelValue", false);
};

const handleSubmit = () => {
  // Validate tiêu đề
  if (!form.value.TieuDe.trim()) {
    alert("Vui lòng nhập tiêu đề bài kiểm tra");
    return;
  }

  // Validate bài học
  if (form.value.LoaiBaiKiemTra === "bai_hoc" && !form.value.BaiHocId) {
    alert("Vui lòng chọn bài học");
    return;
  }

  // Validate thời gian làm bài >= 5 phút
  const thoiGianLamBai = parseInt(form.value.thietLap.thoiGianLamBai) || 0;
  if (thoiGianLamBai > 0 && thoiGianLamBai < 5) {
    alert("Thời gian làm bài phải từ 5 phút trở lên");
    return;
  }

  // Validate số lần làm tối đa >= 1
  const soLanLamToiDa = parseInt(form.value.thietLap.soLanLamToiDa) || 0;
  if (soLanLamToiDa > 0 && soLanLamToiDa < 1) {
    alert("Số lần làm tối đa phải từ 1 trở lên");
    return;
  }

  // Build data to submit
  const data = {
    KhoaHocId: props.khoaHocId,
    BaiHocId:
      form.value.LoaiBaiKiemTra === "bai_hoc" ? form.value.BaiHocId : null,
    TieuDe: form.value.TieuDe.trim(),
    DiemDat: parseFloat(form.value.DiemDat) || 5,
    TrangThai: parseInt(form.value.TrangThai) || 2,
    ThietLapJson: {
      soCauHoi: parseInt(form.value.thietLap.soCauHoi) || 10,
      thoiGianLamBai: parseInt(form.value.thietLap.thoiGianLamBai) || 0,
      soLanLamToiDa: parseInt(form.value.thietLap.soLanLamToiDa) || 0,
      xaoTronCauHoi: form.value.thietLap.xaoTronCauHoi,
      xaoTronDapAn: form.value.thietLap.xaoTronDapAn,
      hienThiDapAn: form.value.thietLap.hienThiDapAn,
      nganHangIds: form.value.thietLap.nganHangIds,
    },
  };

  // If editing, include ID
  if (isEdit.value) {
    data.BaiKiemTraId = props.initialData.BaiKiemTraId;
  }

  emit("submit", data);
};
</script>

<style scoped>
/* Modal Transition */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
  transform: scale(0.95);
}
</style>
