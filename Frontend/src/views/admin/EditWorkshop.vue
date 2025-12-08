<template>
  <div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Modifier le Workshop</h1>

    <LoadingSpinner v-if="loading" />

    <div v-else class="bg-white rounded-lg shadow-md p-8">
      <Alert v-if="message.text" :type="message.type" :message="message.text" />

      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Titre *</label>
          <input
            v-model="form.title"
            type="text"
            required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
          />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2"
            >Description *</label
          >
          <textarea
            v-model="form.description"
            required
            rows="4"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
          ></textarea>
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2"
            >Instructeur *</label
          >
          <input
            v-model="form.instructor"
            type="text"
            required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
          />
        </div>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Date *</label>
            <input
              v-model="form.date"
              type="date"
              required
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
            />
          </div>

          <div>
            <label class="block text-gray-700 font-semibold mb-2"
              >Heure *</label
            >
            <input
              v-model="form.time"
              type="time"
              required
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
            />
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Lieu *</label>
          <input
            v-model="form.location"
            type="text"
            required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
          />
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 font-semibold mb-2"
            >Nombre maximum de participants *</label
          >
          <input
            v-model.number="form.max_participants"
            type="number"
            required
            min="1"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
          />
        </div>

        <div class="flex gap-4">
          <router-link
            to="/admin"
            class="flex-1 bg-gray-200 text-gray-800 text-center py-2 rounded-lg hover:bg-gray-300 transition"
          >
            Annuler
          </router-link>
          <button
            type="submit"
            :disabled="submitting"
            class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition disabled:bg-gray-400"
          >
            {{ submitting ? "Mise à jour..." : "Mettre à jour" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import Alert from "@/Components/Alert.vue";

const route = useRoute();
const router = useRouter();

const form = ref({
  title: "",
  description: "",
  instructor: "",
  date: "",
  time: "",
  location: "",
  max_participants: 30,
});

const loading = ref(true);
const submitting = ref(false);
const message = ref({ text: "", type: "info" });

const fetchWorkshop = async () => {
  try {
    const response = await api.getWorkshop(route.params.id);
    const workshop = response.data;

    form.value = {
      title: workshop.title,
      description: workshop.description,
      instructor: workshop.instructor,
      date: workshop.date,
      time: workshop.time,
      location: workshop.location,
      max_participants: workshop.max_participants,
    };
  } catch (error) {
    message.value = {
      text: "Erreur lors du chargement du workshop",
      type: "error",
    };
  } finally {
    loading.value = false;
  }
};

const handleSubmit = async () => {
  submitting.value = true;
  message.value = { text: "", type: "info" };

  try {
    await api.updateWorkshop(route.params.id, form.value);
    message.value = {
      text: "Workshop mis à jour avec succès!",
      type: "success",
    };
    setTimeout(() => {
      router.push("/admin");
    }, 2000);
  } catch (error) {
    message.value = {
      text: error.response?.data?.message || "Erreur lors de la mise à jour",
      type: "error",
    };
  } finally {
    submitting.value = false;
  }
};

onMounted(fetchWorkshop);
</script>
