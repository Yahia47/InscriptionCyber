<template>
  <div v-if="show" :class="['p-4 rounded-lg mb-4', typeClasses[type]]">
    <div class="flex items-center">
      <span class="mr-2">{{ typeIcons[type] }}</span>
      <p>{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  type: {
    type: String,
    default: "info",
    validator: (value) =>
      ["success", "error", "warning", "info"].includes(value),
  },
  message: {
    type: String,
    required: true,
  },
  show: {
    type: Boolean,
    default: true,
  },
});

const typeClasses = {
  success: "bg-green-100 text-green-800 border border-green-200",
  error: "bg-red-100 text-red-800 border border-red-200",
  warning: "bg-yellow-100 text-yellow-800 border border-yellow-200",
  info: "bg-blue-100 text-blue-800 border border-blue-200",
};

const typeIcons = {
  success: "✅",
  error: "❌",
  warning: "⚠️",
  info: "ℹ️",
};
</script>
