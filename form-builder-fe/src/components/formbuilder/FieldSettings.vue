<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  field: Object,
});

const emit = defineEmits(["updateField", "deleteField"]);

const localField = ref(null);

watch(
  () => props.field,
  (newField) => {
    localField.value = newField ? { ...newField } : null;
  },
  { immediate: true, deep: true }
);

const update = () => {
  if (localField.value) {
    emit("updateField", { ...localField.value });
  }
};

const addOption = () => {
  if (localField.value) {
    localField.value.options.push("New Option");
    update();
  }
};

const removeOption = (index) => {
  if (localField.value) {
    localField.value.options.splice(index, 1);
    update();
  }
};
</script>

<template>
  <div class="p-4 border rounded-lg bg-white shadow">
    <h2 class="font-bold mb-4 text-gray-700">Field Settings</h2>

    <div v-if="!localField" class="text-gray-400 text-sm text-center py-8">
      <p class="text-2xl mb-2">👆</p>
      <p>Click a field on the canvas to edit its settings</p>
    </div>

    <div v-else class="space-y-4">
      <!-- Field Type Badge -->
      <div class="flex items-center gap-2">
        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">
          {{ localField.type }}
        </span>
        <span class="text-xs text-gray-400">ID: {{ localField.id }}</span>
      </div>

      <!-- Label -->
      <div>
        <label class="block text-sm font-semibold mb-1">Label</label>
        <input
          v-model="localField.label"
          @input="update"
          class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <!-- Field Name/Model -->
      <div>
        <label class="block text-sm font-semibold mb-1">Field Name</label>
        <input
          v-model="localField.model"
          @input="update"
          class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-500 font-mono text-sm"
        />
      </div>

      <!-- Placeholder (not for checkbox/radio) -->
      <div v-if="!['checkbox', 'radio', 'select'].includes(localField.type)">
        <label class="block text-sm font-semibold mb-1">Placeholder</label>
        <input
          v-model="localField.placeholder"
          @input="update"
          class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <!-- Checkbox placeholder (as description) -->
      <div v-if="localField.type === 'checkbox'">
        <label class="block text-sm font-semibold mb-1">Description</label>
        <input
          v-model="localField.placeholder"
          @input="update"
          class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-500"
          placeholder="Checkbox description text"
        />
      </div>

      <!-- Required -->
      <label class="flex items-center gap-2 cursor-pointer">
        <input
          type="checkbox"
          v-model="localField.required"
          @change="update"
          class="w-5 h-5"
        />
        <span class="font-semibold">Required Field</span>
      </label>

      <!-- Options for Select/Radio -->
      <div v-if="['select', 'radio'].includes(localField.type)">
        <label class="block text-sm font-semibold mb-2">Options</label>

        <div
          v-for="(opt, i) in localField.options"
          :key="i"
          class="flex gap-2 mb-2"
        >
          <input
            v-model="localField.options[i]"
            @input="update"
            class="border p-2 flex-1 rounded focus:ring-2 focus:ring-blue-500"
          />
          <button
            @click="removeOption(i)"
            class="px-3 bg-red-500 text-white rounded hover:bg-red-600 transition"
          >
            ✕
          </button>
        </div>

        <button
          @click="addOption"
          class="w-full px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
        >
          + Add Option
        </button>
      </div>

      <!-- Delete Button -->
      <button
        @click="emit('deleteField')"
        class="w-full mt-4 px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
      >
        🗑️ Delete Field
      </button>
    </div>
  </div>
</template>
