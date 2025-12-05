<script setup>
import { ref, watch, onMounted } from "vue";
import FieldList from "../components/formbuilder/FieldList.vue";
import FormCanvas from "../components/formbuilder/FormCanvas.vue";
import FieldSettings from "../components/formbuilder/FieldSettings.vue";
import api from "../services/api";

const props = defineProps({
  editFormId: {
    type: Number,
    default: null,
  },
});

const emit = defineEmits(["formSaved"]);

const draggingField = ref(null);
const formFields = ref([]);
const selectedField = ref(null);
const formTitle = ref("Untitled Form");
const formDescription = ref("");
const isSaving = ref(false);
const saveMessage = ref("");

// Load form if editing
onMounted(async () => {
  if (props.editFormId) {
    try {
      const response = await api.getForm(props.editFormId);
      formTitle.value = response.data.title;
      formDescription.value = response.data.description || "";
      formFields.value = response.data.schema || [];
    } catch (error) {
      console.error("Error loading form:", error);
    }
  }
});

// Watch for field changes
watch(
  formFields,
  () => {
    if (selectedField.value) {
      const updated = formFields.value.find(
        (f) => f.id === selectedField.value.id
      );
      if (updated) {
        selectedField.value = updated;
      }
    }
  },
  { deep: true }
);

const handleFieldSelect = (field) => {
  selectedField.value = field;
};

const handleFieldUpdate = (updatedField) => {
  const index = formFields.value.findIndex((f) => f.id === updatedField.id);
  if (index !== -1) {
    formFields.value[index] = { ...updatedField };
  }
};

const handleFieldDelete = () => {
  if (selectedField.value) {
    formFields.value = formFields.value.filter(
      (f) => f.id !== selectedField.value.id
    );
    selectedField.value = null;
  }
};

const saveForm = async () => {
  if (formFields.value.length === 0) {
    saveMessage.value = "Please add at least one field";
    setTimeout(() => (saveMessage.value = ""), 3000);
    return;
  }

  isSaving.value = true;
  saveMessage.value = "";

  try {
    const formData = {
      title: formTitle.value,
      description: formDescription.value,
      schema: formFields.value,
    };

    if (props.editFormId) {
      await api.updateForm(props.editFormId, formData);
      saveMessage.value = "Form updated successfully!";
    } else {
      await api.createForm(formData);
      saveMessage.value = "Form saved successfully!";
    }

    setTimeout(() => {
      emit("formSaved");
    }, 1500);
  } catch (error) {
    console.error("Error saving form:", error);
    saveMessage.value = "Error saving form. Please try again.";
  } finally {
    isSaving.value = false;
  }
};

const clearForm = () => {
  formFields.value = [];
  selectedField.value = null;
  formTitle.value = "Untitled Form";
  formDescription.value = "";
};
</script>

<template>
  <div class="p-6">
    <!-- Form Header -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
      <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
          <label class="block text-sm font-semibold mb-1">Form Title</label>
          <input
            v-model="formTitle"
            type="text"
            class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Enter form title"
          />
        </div>
        <div class="flex-1 min-w-[200px]">
          <label class="block text-sm font-semibold mb-1">Description</label>
          <input
            v-model="formDescription"
            type="text"
            class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Enter form description"
          />
        </div>
        <div class="flex gap-2">
          <button
            @click="clearForm"
            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition"
          >
            Clear
          </button>
          <button
            @click="saveForm"
            :disabled="isSaving"
            class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition disabled:opacity-50"
          >
            {{ isSaving ? "Saving..." : props.editFormId ? "Update" : "Save" }}
          </button>
        </div>
      </div>
      <p
        v-if="saveMessage"
        :class="[
          'mt-2 text-sm',
          saveMessage.includes('Error') ? 'text-red-600' : 'text-green-600',
        ]"
      >
        {{ saveMessage }}
      </p>
    </div>

    <!-- Main Builder Area -->
    <div class="flex gap-5">
      <!-- Field List -->
      <div class="w-56">
        <FieldList @dragStart="draggingField = $event" />
      </div>

      <!-- Form Canvas -->
      <FormCanvas
        :draggingField="draggingField"
        :fields="formFields"
        @update:fields="formFields = $event"
        @selectField="handleFieldSelect"
        @dragEnd="draggingField = null"
      />

      <!-- Field Settings & JSON Preview -->
      <div class="w-80 flex flex-col gap-4">
        <FieldSettings
          :field="selectedField"
          @updateField="handleFieldUpdate"
          @deleteField="handleFieldDelete"
        />

        <!-- JSON Preview -->
        <div class="p-4 border rounded-lg bg-white shadow flex-1">
          <h2 class="font-bold mb-3">Generated JSON</h2>
          <pre class="bg-gray-100 p-3 rounded text-xs overflow-auto max-h-64">{{ JSON.stringify(formFields, null, 2) }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>
