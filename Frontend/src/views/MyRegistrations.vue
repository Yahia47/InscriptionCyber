<template>
  <div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Mes Inscriptions</h1>

    <LoadingSpinner v-if="loading" />

    <div
      v-else-if="registrations.length === 0"
      class="text-center py-12 bg-white rounded-lg shadow-md"
    >
      <p class="text-gray-500 text-lg mb-4">
        Vous n'êtes inscrit à aucun workshop
      </p>
      <router-link
        to="/workshops"
        class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700"
      >
        Voir les workshops disponibles
      </router-link>
    </div>

    <div v-else class="space-y-4">
      <Alert v-if="message.text" :type="message.type" :message="message.text" />

      <div
        v-for="registration in registrations"
        :key="registration.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <h3 class="text-xl font-bold text-gray-900 mb-2">
              {{ registration.workshop.title }}
            </h3>
            <p class="text-gray-600 mb-4">
              {{ registration.workshop.description }}
            </p>

            <div class="grid md:grid-cols-2 gap-3 text-sm">
              <div class="flex items-center text-gray-700">
                <span class="mr-2">👨‍🏫</span>
                <span>{{ registration.workshop.instructor }}</span>
              </div>
              <div class="flex items-center text-gray-700">
                <span class="mr-2">📅</span>
                <span>{{ formatDate(registration.workshop.date) }}</span>
              </div>
              <div class="flex items-center text-gray-700">
                <span class="mr-2">⏰</span>
                <span>{{ formatTime(registration.workshop.time) }}</span>
              </div>
              <div class="flex items-center text-gray-700">
                <span class="mr-2">📍</span>
                <span>{{ registration.workshop.location }}</span>
              </div>
            </div>

            <p class="text-xs text-gray-500 mt-4">
              Inscrit le {{ formatDate(registration.registered_at) }}
            </p>
          </div>

          <button
            @click="handleCancel(registration.workshop.id)"
            :disabled="canceling === registration.workshop.id"
            class="ml-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition disabled:bg-gray-400"
          >
            {{
              canceling === registration.workshop.id
                ? "Annulation..."
                : "Annuler"
            }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import Alert from "@/Components/Alert.vue";

const registrations = ref([]);
const loading = ref(true);
const canceling = ref(null);
const message = ref({ text: "", type: "info" });

const fetchRegistrations = async () => {
  try {
    const response = await api.getMyRegistrations();
    registrations.value = response.data;
  } catch (error) {
    message.value = {
      text: "Erreur lors du chargement des inscriptions",
      type: "error",
    };
  } finally {
    loading.value = false;
  }
};

const handleCancel = async (workshopId) => {
  if (!confirm("Êtes-vous sûr de vouloir annuler cette inscription?")) return;

  canceling.value = workshopId;

  try {
    await api.cancelRegistration(workshopId);
    message.value = {
      text: "Inscription annulée avec succès",
      type: "success",
    };
    await fetchRegistrations();
  } catch (error) {
    message.value = {
      text: "Erreur lors de l'annulation",
      type: "error",
    };
  } finally {
    canceling.value = null;
    setTimeout(() => {
      message.value = { text: "", type: "info" };
    }, 5000);
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const formatTime = (time) => {
  return new Date(`2000-01-01 ${time}`).toLocaleTimeString("fr-FR", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

onMounted(fetchRegistrations);
</script>
