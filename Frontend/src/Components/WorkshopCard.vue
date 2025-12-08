<template>
  <div
    class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-6"
  >
    <div class="flex justify-between items-start mb-4">
      <h3 class="text-xl font-bold text-gray-800">{{ workshop.title }}</h3>
      <span
        :class="[
          'px-3 py-1 rounded-full text-xs font-semibold',
          workshop.is_full
            ? 'bg-red-100 text-red-800'
            : 'bg-green-100 text-green-800',
        ]"
      >
        {{
          workshop.is_full ? "Complet" : `${workshop.remaining_seats} places`
        }}
      </span>
    </div>

    <p class="text-gray-600 mb-4 line-clamp-2">{{ workshop.description }}</p>

    <div class="space-y-2 text-sm text-gray-700 mb-4">
      <div class="flex items-center">
        <span class="mr-2">👨‍🏫</span>
        <span>{{ workshop.instructor }}</span>
      </div>
      <div class="flex items-center">
        <span class="mr-2">📅</span>
        <span>{{ formatDate(workshop.date) }}</span>
      </div>
      <div class="flex items-center">
        <span class="mr-2">⏰</span>
        <span>{{ formatTime(workshop.time) }}</span>
      </div>
      <div class="flex items-center">
        <span class="mr-2">📍</span>
        <span>{{ workshop.location }}</span>
      </div>
    </div>

    <div class="flex gap-2">
      <router-link
        :to="`/workshops/${workshop.id}`"
        class="flex-1 bg-indigo-600 text-white text-center py-2 rounded hover:bg-indigo-700 transition"
      >
        Voir Détails
      </router-link>

      <button
        v-if="showRegisterButton && isAuthenticated"
        @click="$emit('register', workshop.id)"
        :disabled="workshop.is_full || isRegistering"
        class="flex-1 bg-green-600 text-white py-2 rounded hover:bg-green-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed"
      >
        {{ isRegistering ? "Inscription..." : "S'inscrire" }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useAuthStore } from "@/stores/auth";

const props = defineProps({
  workshop: {
    type: Object,
    required: true,
  },
  showRegisterButton: {
    type: Boolean,
    default: true,
  },
  isRegistering: {
    type: Boolean,
    default: false,
  },
});

defineEmits(["register"]);

const authStore = useAuthStore();
const isAuthenticated = computed(() => authStore.isAuthenticated);

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
</script>
