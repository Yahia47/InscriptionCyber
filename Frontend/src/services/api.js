import axios from "axios";

const api = axios.create({
  baseURL: "http://localhost:8000/api",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// Intercepteur pour ajouter le token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Intercepteur pour gérer les erreurs
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

export default {
  // Auth
  register: (data) => api.post("/register", data),
  login: (data) => api.post("/login", data),
  logout: () => api.post("/logout"),
  getUser: () => api.get("/user"),

  // Workshops
  getWorkshops: () => api.get("/workshops"),
  getWorkshop: (id) => api.get(`/workshops/${id}`),
  registerWorkshop: (id) => api.post(`/workshops/${id}/register`),
  getMyRegistrations: () => api.get("/my-registrations"),
  cancelRegistration: (id) => api.delete(`/workshops/${id}/cancel`),

  // Admin
  createWorkshop: (data) => api.post("/admin/workshops", data),
  updateWorkshop: (id, data) => api.put(`/admin/workshops/${id}`, data),
  deleteWorkshop: (id) => api.delete(`/admin/workshops/${id}`),
  getWorkshopRegistrations: (id) =>
    api.get(`/admin/workshops/${id}/registrations`),
  sendMessage: (id, data) =>
    api.post(`/admin/workshops/${id}/send-message`, data),
};
