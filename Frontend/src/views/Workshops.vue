<template>
  <div>
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">
        Workshops Disponibles
      </h1>
      <p class="text-gray-600">
        Découvrez et inscrivez-vous aux prochains workshops organisés par
        <span class="text-black fw-bold">CyberNexus</span>.
      </p>
    </div>

    <Alert
      v-if="message.text"
      :type="message.type"
      :message="message.text"
      :show="true"
    />

    <LoadingSpinner v-if="loading" />

    <div v-else-if="workshops.length === 0" class="text-center py-12">
      <p class="text-gray-500 text-lg">
        Aucun workshop disponible pour le moment
      </p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <WorkshopCard
        v-for="workshop in workshops"
        :key="workshop.id"
        :workshop="workshop"
        :is-registering="registeringId === workshop.id"
        @register="handleRegister"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import WorkshopCard from "@/Components/WorkshopCard.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import Alert from "@/Components/Alert.vue";

const workshops = ref([]);
const loading = ref(true);
const registeringId = ref(null);
const message = ref({ text: "", type: "info" });

const fetchWorkshops = async () => {
  try {
    const response = await api.getWorkshops();
    workshops.value = response.data;
  } catch (error) {
    message.value = {
      text: "Erreur lors du chargement des workshops",
      type: "error",
    };
  } finally {
    loading.value = false;
  }
};

const handleRegister = async (workshopId) => {
  registeringId.value = workshopId;

  try {
    await api.registerWorkshop(workshopId);
    message.value = {
      text: "Inscription réussie!",
      type: "success",
    };
    await fetchWorkshops();
  } catch (error) {
    message.value = {
      text: error.response?.data?.message || "Erreur lors de l'inscription",
      type: "error",
    };
  } finally {
    registeringId.value = null;
    setTimeout(() => {
      message.value = { text: "", type: "info" };
    }, 5000);
  }
};

onMounted(fetchWorkshops);
</script>
