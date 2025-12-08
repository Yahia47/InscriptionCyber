import { defineStore } from "pinia";
import api from "@/services/api";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("user")) || null,
        token: localStorage.getItem("token") || null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role === "admin",
    },

    actions: {
        async login(credentials) {
            try {
                const response = await api.login(credentials);
                this.user = response.data.user;
                this.token = response.data.token;

                localStorage.setItem("user", JSON.stringify(this.user));
                localStorage.setItem("token", this.token);

                return response.data;
            } catch (error) {
                throw error;
            }
        },

        async register(userData) {
            try {
                const response = await api.register(userData);
                this.user = response.data.user;
                this.token = response.data.token;

                localStorage.setItem("user", JSON.stringify(this.user));
                localStorage.setItem("token", this.token);

                return response.data;
            } catch (error) {
                throw error;
            }
        },

        async logout() {
            try {
                await api.logout();
            } catch (error) {
                console.error(error);
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem("user");
                localStorage.removeItem("token");
            }
        },
    },
});
