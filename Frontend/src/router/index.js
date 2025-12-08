import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "home",
            component: () => import("@/views/Home.vue"),
        },
        {
            path: "/login",
            name: "login",
            component: () => import("@/views/Login.vue"),
        },
        {
            path: "/register",
            name: "register",
            component: () => import("@/views/Register.vue"),
        },
        {
            path: "/workshops",
            name: "workshops",
            component: () => import("@/views/Workshops.vue"),
        },
        {
            path: "/workshops/:id",
            name: "workshop-details",
            component: () => import("@/views/WorkshopDetails.vue"),
        },
        {
            path: "/my-registrations",
            name: "my-registrations",
            component: () => import("@/views/MyRegistrations.vue"),
            meta: { requiresAuth: true },
        },
        {
            path: "/admin",
            name: "admin",
            component: () => import("@/views/admin/Dashboard.vue"),
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/workshops/create",
            name: "admin-create-workshop",
            component: () => import("@/views/admin/CreateWorkshop.vue"),
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/workshops/:id/edit",
            name: "admin-edit-workshop",
            component: () => import("@/views/admin/EditWorkshop.vue"),
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/workshops/:id/registrations",
            name: "admin-workshop-registrations",
            component: () => import("@/views/admin/WorkshopRegistrations.vue"),
            meta: { requiresAuth: true, requiresAdmin: true },
        },
    ],
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next("/login");
    } else if (to.meta.requiresAdmin && !authStore.isAdmin) {
        next("/");
    } else {
        next();
    }
});

export default router;
