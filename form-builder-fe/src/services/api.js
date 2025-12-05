import axios from "axios";

const API_BASE_URL = "http://localhost:8000/api";

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    "Content-Type": "application/json",
  },
});

export default {
  // Forms
  getAllForms() {
    return apiClient.get("/forms");
  },

  getForm(id) {
    return apiClient.get(`/forms/${id}`);
  },

  createForm(formData) {
    return apiClient.post("/forms", formData);
  },

  updateForm(id, formData) {
    return apiClient.put(`/forms/${id}`, formData);
  },

  deleteForm(id) {
    return apiClient.delete(`/forms/${id}`);
  },

  // Form Responses
  submitFormResponse(formId, data) {
    return apiClient.post(`/forms/${formId}/submit`, data);
  },

  getFormResponses(formId) {
    return apiClient.get(`/forms/${formId}/responses`);
  },
};
