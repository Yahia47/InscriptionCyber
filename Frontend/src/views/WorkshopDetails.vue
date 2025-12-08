<template>
  <div class="max-w-3xl mx-auto">
    <LoadingSpinner v-if="loading" />

    <div v-else-if="workshop" class="bg-white rounded-lg shadow-lg p-8">
      <div class="mb-6">
        <div class="flex justify-between items-start mb-4">
          <h1 class="text-3xl font-bold text-gray-900">{{ workshop.title }}</h1>
          <span
            :class="[
              'px-4 py-2 rounded-full text-sm font-semibold',
              workshop.is_full
                ? 'bg-red-100 text-red-800'
                : 'bg-green-100 text-green-800',
            ]"
          >
            {{
              workshop.is_full
                ? "Complet"
                : `${workshop.remaining_seats} places restantes`
            }}
          </span>
        </div>
      </div>

      <div class="mb-8">
        <h2 class="text-xl font-semibold mb-3">Description</h2>
        <p class="text-gray-700 leading-relaxed">{{ workshop.description }}</p>
      </div>

      <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="flex items-start">
          <span class="text-2xl mr-3">👨‍🏫</span>
          <div>
            <p class="text-sm text-gray-500">Instructeur</p>
            <p class="font-semibold">{{ workshop.instructor }}</p>
          </div>
        </div>

        <div class="flex items-start">
          <span class="text-2xl mr-3">📅</span>
          <div>
            <p class="text-sm text-gray-500">Date</p>
            <p class="font-semibold">{{ formatDate(workshop.date) }}</p>
          </div>
        </div>

        <div class="flex items-start">
          <span class="text-2xl mr-3">⏰</span>
          <div>
            <p class="text-sm text-gray-500">Heure</p>
            <p class="font-semibold">{{ formatTime(workshop.time) }}</p>
          </div>
        </div>

        <div class="flex items-start">
          <span class="text-2xl mr-3">📍</span>
          <div>
            <p class="text-sm text-gray-500">Lieu</p>
            <p class="font-semibold">{{ workshop.location }}</p>
          </div>
        </div>

        <div class="flex items-start">
          <span class="text-2xl mr-3">👥</span>
          <div>
            <p class="text-sm text-gray-500">Capacité</p>
            <p class="font-semibold">
              {{ workshop.max_participants }} participants
            </p>
          </div>
        </div>

        <div class="flex items-start">
          <span class="text-2xl mr-3">✅</span>
          <div>
            <p class="text-sm text-gray-500">Inscrits</p>
            <p class="font-semibold">
              {{ workshop.registrations_count }} personnes
            </p>
          </div>
        </div>
      </div>

      <Alert v-if="message.text" :type="message.type" :message="message.text" />

      <div class="flex gap-4">
        <router-link
          to="/workshops"
          class="flex-1 bg-gray-200 text-gray-800 text-center py-3 rounded-lg hover:bg-gray-300 transition font-semibold"
        >
          Retour
        </router-link>

        <button
          v-if="isAuthenticated && !workshop.is_full"
          @click="handleRegister"
          :disabled="registering"
          class="flex-1 bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition font-semibold disabled:bg-gray-400"
        >
          {{ registering ? "Inscription..." : "S'inscrire maintenant" }}
        </button>

        <div v-else-if="!isAuthenticated" class="flex-1">
          <router-link
            to="/login"
            class="block bg-indigo-600 text-white text-center py-3 rounded-lg hover:bg-indigo-700 transition font-semibold"
          >
            Connectez-vous pour vous inscrire
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import api from "@/services/api";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import Alert from "@/Components/Alert.vue";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const workshop = ref(null);
const loading = ref(true);
const registering = ref(false);
const message = ref({ text: "", type: "info" });

const isAuthenticated = computed(() => authStore.isAuthenticated);

const fetchWorkshop = async () => {
  try {
    const response = await api.getWorkshop(route.params.id);
    workshop.value = response.data;
  } catch (error) {
    message.value = {
      text: "Erreur lors du chargement du workshop",
      type: "error",
    };
  } finally {
    loading.value = false;
  }
};

const handleRegister = async () => {
  registering.value = true;

  try {
    await api.registerWorkshop(workshop.value.id);
    message.value = {
      text: "Inscription réussie! Vous recevrez un email de confirmation.",
      type: "success",
    };
    await fetchWorkshop();
  } catch (error) {
    message.value = {
      text: error.response?.data?.message || "Erreur lors de l'inscription",
      type: "error",
    };
  } finally {
    registering.value = false;
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("fr-FR", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

const formatTime = (time) => {
  return new Date(`2000-01-01 ${time}`).toLocaleTimeString("fr-FR", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

onMounted(fetchWorkshop);
</script>
