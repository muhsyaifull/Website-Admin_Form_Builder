<script setup>
import { ref, watch } from "vue";
import draggable from "vuedraggable";

const props = defineProps({
  draggingField: Object,
  fields: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(["update:fields", "selectField", "dragEnd"]);

const formFields = ref([...props.fields]);
const selectedId = ref(null);

// Sync with parent
watch(
  () => props.fields,
  (newFields) => {
    formFields.value = [...newFields];
  },
  { deep: true }
);

const onDrop = () => {
  if (!props.draggingField) return;

  const newField = {
    id: Date.now(),
    type: props.draggingField.type,
    label: props.draggingField.label,
    model: props.draggingField.type + "_" + Date.now(),
    placeholder: "",
    required: false,
    options:
      props.draggingField.type === "select" ||
      props.draggingField.type === "radio"
        ? ["Option 1", "Option 2", "Option 3"]
        : [],
  };

  formFields.value.push(newField);
  emit("update:fields", formFields.value);
  emit("dragEnd");
};

const selectField = (field) => {
  selectedId.value = field.id;
  emit("selectField", field);
};

const onDragChange = () => {
  emit("update:fields", formFields.value);
};
</script>

<template>
  <div
    class="flex-1 p-4 border-dashed border-2 border-gray-300 rounded-lg bg-gray-50 min-h-[500px]"
    @dragover.prevent
    @drop="onDrop"
  >
    <h2 class="text-lg font-bold mb-3 text-gray-700">🎨 Form Canvas</h2>

    <div v-if="formFields.length === 0" class="text-center py-12 text-gray-400">
      <p class="text-4xl mb-2">📥</p>
      <p>Drag fields here to build your form</p>
    </div>

    <draggable
      v-model="formFields"
      item-key="id"
      @change="onDragChange"
      handle=".drag-handle"
      animation="200"
      ghost-class="opacity-50"
    >
      <template #item="{ element: field }">
        <div
          @click="selectField(field)"
          :class="[
            'p-4 mb-3 border rounded-lg bg-white shadow-sm cursor-pointer transition',
            selectedId === field.id
              ? 'ring-2 ring-blue-500 border-blue-500'
              : 'hover:shadow-md',
          ]"
        >
          <div class="flex items-center gap-2 mb-2">
            <span class="drag-handle cursor-move text-gray-400 hover:text-gray-600">⋮⋮</span>
            <label class="font-semibold text-gray-700 flex-1">
              {{ field.label }}
              <span v-if="field.required" class="text-red-500 ml-1">*</span>
            </label>
            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">
              {{ field.type }}
            </span>
          </div>

          <!-- Text Input -->
          <input
            v-if="field.type === 'text'"
            type="text"
            class="border p-2 w-full rounded bg-gray-50"
            :placeholder="field.placeholder || 'Text input'"
            disabled
          />

          <!-- Email Input -->
          <input
            v-else-if="field.type === 'email'"
            type="email"
            class="border p-2 w-full rounded bg-gray-50"
            :placeholder="field.placeholder || 'email@example.com'"
            disabled
          />

          <!-- Number Input -->
          <input
            v-else-if="field.type === 'number'"
            type="number"
            class="border p-2 w-full rounded bg-gray-50"
            :placeholder="field.placeholder || '0'"
            disabled
          />

          <!-- Date Input -->
          <input
            v-else-if="field.type === 'date'"
            type="date"
            class="border p-2 w-full rounded bg-gray-50"
            disabled
          />

          <!-- Textarea -->
          <textarea
            v-else-if="field.type === 'textarea'"
            class="border p-2 w-full rounded bg-gray-50"
            :placeholder="field.placeholder || 'Long text...'"
            rows="2"
            disabled
          ></textarea>

          <!-- Select -->
          <select
            v-else-if="field.type === 'select'"
            class="border p-2 w-full rounded bg-gray-50"
            disabled
          >
            <option>Select option...</option>
            <option v-for="opt in field.options" :key="opt">{{ opt }}</option>
          </select>

          <!-- Checkbox -->
          <div v-else-if="field.type === 'checkbox'" class="flex items-center gap-2">
            <input type="checkbox" class="w-5 h-5" disabled />
            <span class="text-gray-500">{{ field.placeholder || 'Checkbox option' }}</span>
          </div>

          <!-- Radio -->
          <div v-else-if="field.type === 'radio'" class="space-y-1">
            <div
              v-for="opt in field.options"
              :key="opt"
              class="flex items-center gap-2"
            >
              <input type="radio" class="w-4 h-4" disabled />
              <span class="text-gray-500">{{ opt }}</span>
            </div>
          </div>
        </div>
      </template>
    </draggable>
  </div>
</template>
