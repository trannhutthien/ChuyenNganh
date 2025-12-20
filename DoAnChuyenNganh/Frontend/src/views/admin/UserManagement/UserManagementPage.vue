<template>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-6">
      <div class="p-6 bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex gap-4 items-start">
          <div
            class="flex justify-center items-center w-16 h-16 text-blue-600 bg-blue-100 rounded-xl"
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
                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"
              />
            </svg>
          </div>
          <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800">Quản lý người dùng</h1>
            <p class="mt-1 text-gray-600">
              Quản lý tài khoản người dùng trong hệ thống
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
                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                  />
                </svg>
                {{ users.length }} người dùng
              </span>
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
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                  />
                </svg>
                {{ activeUsersCount }} đang hoạt động
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
      <div class="flex gap-3 items-center w-full sm:w-auto">
        <SearchInput
          v-model="searchQuery"
          placeholder="Tìm kiếm người dùng..."
          size="lg"
          container-class="w-full sm:w-80"
          realtime
          :debounce="300"
        />
        <select
          v-model="roleFilter"
          class="px-4 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20"
        >
          <option value="">Tất cả vai trò</option>
          <option value="admin">Admin</option>
          <option value="instructor">Giảng viên</option>
          <option value="student">Học viên</option>
        </select>
      </div>
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
        Thêm người dùng
      </BaseButton>
    </div>

    <!-- Users List -->
    <div
      class="overflow-hidden bg-white rounded-xl border border-gray-100 shadow-sm"
    >
      <!-- Loading -->
      <div v-if="isLoading" class="p-8 text-center">
        <div
          class="mx-auto w-10 h-10 rounded-full border-b-2 animate-spin border-primary"
        ></div>
        <p class="mt-4 text-gray-500">Đang tải...</p>
      </div>

      <!-- Empty -->
      <div v-else-if="filteredUsers.length === 0" class="p-12 text-center">
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
            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"
          />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">
          Chưa có người dùng
        </h3>
        <p class="mt-2 text-gray-500">Bắt đầu thêm người dùng mới</p>
      </div>

      <!-- User Items -->
      <div v-else class="divide-y divide-gray-100">
        <div
          v-for="user in filteredUsers"
          :key="user.id"
          class="flex gap-4 items-center p-4 transition-colors hover:bg-gray-50 group"
        >
          <!-- Avatar -->
          <div
            class="flex overflow-hidden justify-center items-center w-12 h-12 bg-gray-200 rounded-full"
          >
            <img
              v-if="user.avatar"
              :src="user.avatar"
              :alt="user.name"
              class="object-cover w-full h-full"
            />
            <span v-else class="text-lg font-semibold text-gray-500">{{
              user.name?.charAt(0)?.toUpperCase()
            }}</span>
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex gap-2 items-center mb-1">
              <h3 class="font-medium text-gray-900 truncate">
                {{ user.name }}
              </h3>
              <span
                :class="[
                  'px-2 py-0.5 rounded text-xs font-medium',
                  getRoleClass(user.role),
                ]"
                >{{ getRoleLabel(user.role) }}</span
              >
            </div>
            <p class="text-sm text-gray-500 truncate">{{ user.email }}</p>
          </div>

          <!-- Status -->
          <div class="hidden sm:block">
            <BadgeLabel :value="user.status" type="status" />
          </div>

          <!-- Actions -->
          <div
            class="flex gap-1 items-center opacity-0 transition-opacity group-hover:opacity-100"
          >
            <button
              @click="editUser(user)"
              class="p-2 text-yellow-600 rounded-lg hover:bg-yellow-50"
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
              @click="confirmDeleteUser(user)"
              class="p-2 text-red-600 rounded-lg hover:bg-red-50"
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
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <FormAddModal
      v-model="showUserModal"
      :title="isEditing ? 'Sửa người dùng' : 'Thêm người dùng'"
      :submit-text="isEditing ? 'Cập nhật' : 'Thêm'"
      :fields="userFormFields"
      :initial-data="editingUser"
      :loading="isSubmitting"
      size="lg"
      @submit="handleUserSubmit"
    />

    <!-- Delete Confirm -->
    <ConfirmModal
      v-model:show="showDeleteConfirm"
      title="Xóa người dùng"
      :message="`Bạn có chắc muốn xóa người dùng '${deletingUser?.name}'?`"
      confirm-text="Xóa"
      confirm-variant="danger"
      @confirm="handleDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import SearchInput from "../../../components/ui/SearchInput.vue";
import BaseButton from "../../../components/ui/BaseButton.vue";
import BadgeLabel from "../../../components/admin/BadgeLabel.vue";
import FormAddModal from "../../../components/modal/FormAddModal.vue";
import ConfirmModal from "../../../components/modal/ConfirmModal.vue";
import api from "../../../services/api";

const users = ref([]);
const isLoading = ref(false);
const searchQuery = ref("");
const roleFilter = ref("");
const showUserModal = ref(false);
const isEditing = ref(false);
const isSubmitting = ref(false);
const editingUser = ref({});
const showDeleteConfirm = ref(false);
const deletingUser = ref(null);

const activeUsersCount = computed(
  () => users.value.filter((u) => u.status === 1).length
);

const filteredUsers = computed(() => {
  let result = users.value;
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter(
      (u) =>
        u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q)
    );
  }
  if (roleFilter.value) {
    result = result.filter((u) => u.role === roleFilter.value);
  }
  return result;
});

const userFormFields = computed(() => [
  {
    name: "HoTen",
    label: "Họ và tên",
    type: "text",
    placeholder: "Nguyễn Văn A",
    required: true,
    default: "",
  },
  {
    name: "Email",
    label: "Email",
    type: "email",
    placeholder: "email@example.com",
    required: true,
    default: "",
  },
  {
    name: "MatKhau",
    label: "Mật khẩu",
    type: "password",
    placeholder: "••••••••",
    required: !isEditing.value,
    default: "",
    showIf: () => !isEditing.value,
  },
  {
    name: "SoDienThoai",
    label: "Số điện thoại",
    type: "text",
    placeholder: "0901234567",
    default: "",
  },
  {
    name: "VaiTro",
    label: "Vai trò",
    inputType: "select",
    required: true,
    default: "student",
    options: [
      { value: "admin", label: "Admin" },
      { value: "instructor", label: "Giảng viên" },
      { value: "student", label: "Học viên" },
    ],
  },
  {
    name: "TrangThai",
    label: "Trạng thái",
    inputType: "select",
    required: true,
    default: 1,
    options: [
      { value: 1, label: "Hoạt động" },
      { value: 0, label: "Khóa" },
    ],
  },
]);

const fetchUsers = async () => {
  isLoading.value = true;
  try {
    const response = await api.get("/users");
    users.value = (response.data || response || []).map((u) => ({
      id: u.NguoiDungId || u.id,
      name: u.HoTen || u.name,
      email: u.Email || u.email,
      phone: u.SoDienThoai || u.phone,
      role: u.VaiTro || u.role || "student",
      status: u.TrangThai ?? u.status ?? 1,
      avatar: u.AnhDaiDien || u.avatar,
    }));
  } catch (e) {
    console.error("Lỗi:", e);
  } finally {
    isLoading.value = false;
  }
};

const openAddModal = () => {
  isEditing.value = false;
  editingUser.value = {};
  showUserModal.value = true;
};

const editUser = (user) => {
  isEditing.value = true;
  editingUser.value = {
    id: user.id,
    HoTen: user.name,
    Email: user.email,
    SoDienThoai: user.phone || "",
    VaiTro: user.role,
    TrangThai: user.status,
  };
  showUserModal.value = true;
};

const confirmDeleteUser = (user) => {
  deletingUser.value = user;
  showDeleteConfirm.value = true;
};

const handleUserSubmit = async (formData) => {
  isSubmitting.value = true;
  try {
    const data = {
      HoTen: formData.HoTen,
      Email: formData.Email,
      SoDienThoai: formData.SoDienThoai,
      VaiTro: formData.VaiTro,
      TrangThai: formData.TrangThai,
    };
    if (!isEditing.value && formData.MatKhau) data.MatKhau = formData.MatKhau;
    if (isEditing.value) await api.put(`/users/${editingUser.value.id}`, data);
    else await api.post("/users", data);
    showUserModal.value = false;
    fetchUsers();
  } catch (e) {
    alert("Lỗi khi lưu người dùng");
  } finally {
    isSubmitting.value = false;
  }
};

const handleDelete = async () => {
  if (!deletingUser.value) return;
  try {
    await api.delete(`/users/${deletingUser.value.id}`);
    fetchUsers();
  } catch (e) {
    alert("Lỗi khi xóa");
  } finally {
    deletingUser.value = null;
  }
};

const getRoleLabel = (role) =>
  ({ admin: "Admin", instructor: "Giảng viên", student: "Học viên" }[role] ||
  role);
const getRoleClass = (role) =>
  ({
    admin: "bg-red-100 text-red-700",
    instructor: "bg-blue-100 text-blue-700",
    student: "bg-green-100 text-green-700",
  }[role] || "bg-gray-100 text-gray-700");

onMounted(() => fetchUsers());
</script>
