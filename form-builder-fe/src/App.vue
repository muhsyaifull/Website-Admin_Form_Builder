<script setup>
import { ref } from "vue";
import FormBuilder from "./page/Formbuilder.vue";
import FormList from "./page/FormList.vue";
import FormPreview from "./page/FormPreview.vue";

const currentPage = ref("builder");
const selectedFormId = ref(null);

const navigateTo = (page, formId = null) => {
  currentPage.value = page;
  selectedFormId.value = formId;
};
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white p-4 shadow-lg">
      <div class="container mx-auto flex items-center justify-between">
        <h1 class="text-xl font-bold">📝 Vue Form Builder</h1>
        <div class="flex gap-4">
          <button
            @click="navigateTo('builder')"
            :class="[
              'px-4 py-2 rounded transition',
              currentPage === 'builder'
                ? 'bg-white text-blue-600'
                : 'hover:bg-blue-500',
            ]"
          >
            Form Builder
          </button>
          <button
            @click="navigateTo('list')"
            :class="[
              'px-4 py-2 rounded transition',
              currentPage === 'list'
                ? 'bg-white text-blue-600'
                : 'hover:bg-blue-500',
            ]"
          >
            Form List
          </button>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main class="container mx-auto py-6">
      <FormBuilder
        v-if="currentPage === 'builder'"
        :editFormId="selectedFormId"
        @formSaved="navigateTo('list')"
      />
      <FormList
        v-else-if="currentPage === 'list'"
        @editForm="navigateTo('builder', $event)"
        @previewForm="navigateTo('preview', $event)"
      />
      <FormPreview
        v-else-if="currentPage === 'preview'"
        :formId="selectedFormId"
        @back="navigateTo('list')"
      />
    </main>
  </div>
</template>

<style>
@import "tailwindcss";
</style>
