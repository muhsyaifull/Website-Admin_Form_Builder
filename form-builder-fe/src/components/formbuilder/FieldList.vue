<script setup>
const fields = [
  { type: "text", label: "Text Field", icon: "📝" },
  { type: "email", label: "Email Field", icon: "📧" },
  { type: "number", label: "Number Field", icon: "🔢" },
  { type: "date", label: "Date Field", icon: "📅" },
  { type: "textarea", label: "Textarea", icon: "📄" },
  { type: "select", label: "Dropdown", icon: "📋" },
  { type: "checkbox", label: "Checkbox", icon: "☑️" },
  { type: "radio", label: "Radio Button", icon: "🔘" },
];
</script>

<template>
  <div class="p-4 border rounded-lg bg-white shadow">
    <h2 class="font-bold mb-3 text-gray-700">📦 Available Fields</h2>

    <div class="flex flex-col gap-2">
      <div
        v-for="field in fields"
        :key="field.type"
        draggable="true"
        @dragstart="$emit('dragStart', field)"
        class="p-3 border rounded cursor-grab bg-gray-50 hover:bg-blue-50 hover:border-blue-300 transition flex items-center gap-2"
      >
        <span>{{ field.icon }}</span>
        <span>{{ field.label }}</span>
      </div>
    </div>

    <div class="mt-4 p-3 bg-blue-50 rounded text-xs text-blue-700">
      💡 Drag and drop fields to the canvas
    </div>
  </div>
</template>
