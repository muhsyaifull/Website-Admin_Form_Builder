<script setup>
import { ref, onMounted } from "vue";
import api from "../services/api";

const emit = defineEmits(["editForm", "previewForm"]);

const forms = ref([]);
const isLoading = ref(true);
const deleteConfirm = ref(null);

const loadForms = async () => {
  isLoading.value = true;
  try {
    const response = await api.getAllForms();
    forms.value = response.data;
  } catch (error) {
    console.error("Error loading forms:", error);
  } finally {
    isLoading.value = false;
  }
};

const deleteForm = async (id) => {
  try {
    await api.deleteForm(id);
    forms.value = forms.value.filter((f) => f.id !== id);
    deleteConfirm.value = null;
  } catch (error) {
    console.error("Error deleting form:", error);
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString("id-ID", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

onMounted(loadForms);
</script>

<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">📋 Saved Forms</h1>
      <button
        @click="loadForms"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
      >
        🔄 Refresh
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-12">
      <div
        class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"
      ></div>
      <p class="mt-4 text-gray-600">Loading forms...</p>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="forms.length === 0"
      class="text-center py-12 bg-white rounded-lg shadow"
    >
      <p class="text-gray-500 text-lg">No forms created yet.</p>
      <p class="text-gray-400">Go to Form Builder to create your first form!</p>
    </div>

    <!-- Forms Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="form in forms"
        :key="form.id"
        class="bg-white rounded-lg shadow-md hover:shadow-lg transition p-5"
      >
        <div class="flex justify-between items-start mb-3">
          <h3 class="font-bold text-lg text-gray-800">{{ form.title }}</h3>
          <span class="text-xs text-gray-400">#{{ form.id }}</span>
        </div>

        <p class="text-gray-600 text-sm mb-3">
          {{ form.description || "No description" }}
        </p>

        <div class="text-xs text-gray-400 mb-4">
          <p>Fields: {{ form.schema?.length || 0 }}</p>
          <p>Created: {{ formatDate(form.created_at) }}</p>
        </div>

        <div class="flex gap-2">
          <button
            @click="emit('previewForm', form.id)"
            class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition"
          >
            👁️ Preview
          </button>
          <button
            @click="emit('editForm', form.id)"
            class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition"
          >
            ✏️ Edit
          </button>
          <button
            @click="deleteConfirm = form.id"
            class="px-3 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition"
          >
            🗑️
          </button>
        </div>

        <!-- Delete Confirmation -->
        <div
          v-if="deleteConfirm === form.id"
          class="mt-3 p-3 bg-red-50 border border-red-200 rounded"
        >
          <p class="text-sm text-red-700 mb-2">Delete this form?</p>
          <div class="flex gap-2">
            <button
              @click="deleteForm(form.id)"
              class="flex-1 px-2 py-1 bg-red-600 text-white text-sm rounded"
            >
              Yes, Delete
            </button>
            <button
              @click="deleteConfirm = null"
              class="flex-1 px-2 py-1 bg-gray-300 text-gray-700 text-sm rounded"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
