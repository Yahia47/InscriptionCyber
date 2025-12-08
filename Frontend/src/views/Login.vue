<template>
  <div class="max-w-md mx-auto">
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6 text-center">Connexion</h2>

      <Alert v-if="error" type="error" :message="error" />

      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
            placeholder="votre@email.com"
          />
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 font-semibold mb-2"
            >Mot de passe</label
          >
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
            placeholder="••••••••"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition disabled:bg-gray-400"
        >
          {{ loading ? "Connexion..." : "Se connecter" }}
        </button>
      </form>

      <p class="text-center mt-4 text-gray-600">
        Pas encore de compte?
        <router-link to="/register" class="text-indigo-600 hover:underline">
          S'inscrire
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import Alert from "@/Components/Alert.vue";

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  email: "",
  password: "",
});

const loading = ref(false);
const error = ref("");

const handleLogin = async () => {
  loading.value = true;
  error.value = "";

  try {
    await authStore.login(form.value);
    router.push("/");
  } catch (err) {
    error.value = err.response?.data?.message || "Erreur de connexion";
  } finally {
    loading.value = false;
  }
};
</script>
