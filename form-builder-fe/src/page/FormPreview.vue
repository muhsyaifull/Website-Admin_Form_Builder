<script setup>
import { ref, onMounted } from "vue";
import api from "../services/api";

const props = defineProps({
  formId: {
    type: Number,
    required: true,
  },
});

const emit = defineEmits(["back"]);

const form = ref(null);
const formData = ref({});
const isLoading = ref(true);
const isSubmitting = ref(false);
const submitMessage = ref("");
const submitSuccess = ref(false);

const loadForm = async () => {
  isLoading.value = true;
  try {
    const response = await api.getForm(props.formId);
    form.value = response.data;

    // Initialize form data
    form.value.schema.forEach((field) => {
      formData.value[field.model] = field.type === "checkbox" ? false : "";
    });
  } catch (error) {
    console.error("Error loading form:", error);
  } finally {
    isLoading.value = false;
  }
};

const submitForm = async () => {
  isSubmitting.value = true;
  submitMessage.value = "";

  try {
    await api.submitFormResponse(props.formId, formData.value);
    submitSuccess.value = true;
    submitMessage.value = "Form submitted successfully!";

    // Reset form
    Object.keys(formData.value).forEach((key) => {
      formData.value[key] = "";
    });
  } catch (error) {
    console.error("Error submitting form:", error);
    submitMessage.value =
      error.response?.data?.message || "Error submitting form";
    submitSuccess.value = false;
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(loadForm);
</script>

<template>
  <div class="p-6 max-w-2xl mx-auto">
    <button
      @click="emit('back')"
      class="mb-4 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition"
    >
      ← Back to List
    </button>

    <!-- Loading -->
    <div v-if="isLoading" class="text-center py-12">
      <div
        class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"
      ></div>
      <p class="mt-4 text-gray-600">Loading form...</p>
    </div>

    <!-- Form Preview -->
    <div v-else-if="form" class="bg-white rounded-lg shadow-lg p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ form.title }}</h1>
      <p v-if="form.description" class="text-gray-600 mb-6">
        {{ form.description }}
      </p>

      <form @submit.prevent="submitForm" class="space-y-5">
        <div v-for="field in form.schema" :key="field.id" class="space-y-1">
          <label class="block font-semibold text-gray-700">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
          </label>

          <!-- Text Input -->
          <input
            v-if="field.type === 'text'"
            v-model="formData[field.model]"
            type="text"
            :placeholder="field.placeholder"
            :required="field.required"
            class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />

          <!-- Email Input -->
          <input
            v-else-if="field.type === 'email'"
            v-model="formData[field.model]"
            type="email"
            :placeholder="field.placeholder"
            :required="field.required"
            class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />

          <!-- Number Input -->
          <input
            v-else-if="field.type === 'number'"
            v-model="formData[field.model]"
            type="number"
            :placeholder="field.placeholder"
            :required="field.required"
            class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />

          <!-- Date Input -->
          <input
            v-else-if="field.type === 'date'"
            v-model="formData[field.model]"
            type="date"
            :required="field.required"
            class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />

          <!-- Textarea -->
          <textarea
            v-else-if="field.type === 'textarea'"
            v-model="formData[field.model]"
            :placeholder="field.placeholder"
            :required="field.required"
            rows="4"
            class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          ></textarea>

          <!-- Select -->
          <select
            v-else-if="field.type === 'select'"
            v-model="formData[field.model]"
            :required="field.required"
            class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">Select an option</option>
            <option v-for="opt in field.options" :key="opt" :value="opt">
              {{ opt }}
            </option>
          </select>

          <!-- Checkbox -->
          <div v-else-if="field.type === 'checkbox'" class="flex items-center gap-2">
            <input
              v-model="formData[field.model]"
              type="checkbox"
              class="w-5 h-5"
            />
            <span>{{ field.placeholder || "Check this box" }}</span>
          </div>

          <!-- Radio -->
          <div v-else-if="field.type === 'radio'" class="space-y-2">
            <div
              v-for="opt in field.options"
              :key="opt"
              class="flex items-center gap-2"
            >
              <input
                v-model="formData[field.model]"
                type="radio"
                :value="opt"
                :name="field.model"
                :required="field.required"
                class="w-5 h-5"
              />
              <span>{{ opt }}</span>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="isSubmitting"
          class="w-full py-3 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition disabled:opacity-50"
        >
          {{ isSubmitting ? "Submitting..." : "Submit" }}
        </button>

        <!-- Submit Message -->
        <p
          v-if="submitMessage"
          :class="[
            'text-center p-3 rounded',
            submitSuccess
              ? 'bg-green-100 text-green-700'
              : 'bg-red-100 text-red-700',
          ]"
        >
          {{ submitMessage }}
        </p>
      </form>
    </div>
  </div>
</template>
