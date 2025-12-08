<template>
  <nav class="bg-indigo-600 text-white shadow-lg">
    <div class="container mx-auto px-4">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center space-x-8">
          <router-link to="/" class="text-xl font-bold flex items-center gap-1">
            Cyber <span class="typing">Nexus</span>
          </router-link>
          <router-link
            v-if="isAuthenticated"
            to="/my-registrations"
            class="hover:text-indigo-200"
          >
            Mes Inscriptions
          </router-link>
          <router-link v-if="isAdmin" to="/admin" class="hover:text-indigo-200">
            Admin
          </router-link>
        </div>

        <div class="flex items-center space-x-4">
          <template v-if="isAuthenticated">
            <span class="text-sm">Bonjour, {{ user?.name }}</span>
            <button
              @click="handleLogout"
              class="bg-indigo-700 px-4 py-2 rounded hover:bg-indigo-800"
            >
              Déconnexion
            </button>
          </template>
          <template v-else>
            <router-link to="/login" class="hover:text-indigo-200">
              Connexion
            </router-link>
            <router-link
              to="/register"
              class="bg-white text-indigo-600 px-4 py-2 rounded hover:bg-indigo-50"
            >
              Inscription
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const isAdmin = computed(() => authStore.isAdmin);
const user = computed(() => authStore.user);

const handleLogout = async () => {
  try {
    await authStore.logout();
    router.push("/login");
  } catch (error) {
    console.error("Logout error:", error);
  }
};
</script>

<style>
.typing {
  overflow: hidden;
  border-right: 2px solid #c7d2fe;
  white-space: nowrap;
  animation: typing 2s steps(6), blink 0.7s infinite step-end alternate,
    erase 2s steps(6) 3s infinite;
}

/* Tape le mot */
@keyframes typing {
  0% {
    width: 0;
  }
  100% {
    width: 100%;
  }
}

/* Efface le mot */
@keyframes erase {
  0% {
    width: 100%;
  }
  100% {
    width: 0;
  }
}

/* Curseur qui clignote */
@keyframes blink {
  0% {
    border-right-color: transparent;
  }
  100% {
    border-right-color: #c7d2fe;
  }
}
</style>
