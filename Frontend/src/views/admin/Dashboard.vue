<template>
  <div class="admin-dashboard">
    <div class="dashboard-header">
      <h1 class="dashboard-title">Tableau de Bord Admin</h1>
      <router-link to="/admin/workshops/create" class="btn-create">
        <PlusIcon class="icon-small" />
        <span>Créer un Workshop</span>
      </router-link>
    </div>

    <LoadingSpinner v-if="loading" />

    <div v-else class="dashboard-content">
      <Alert v-if="message.text" :type="message.type" :message="message.text" />

      <div v-if="workshops.length === 0" class="empty-state">
        <p class="empty-text">Aucun workshop créé</p>
        <router-link to="/admin/workshops/create" class="btn-create-first">
          Créer le premier workshop
        </router-link>
      </div>

      <div v-else class="workshops-table-container">
        <table class="workshops-table">
          <thead>
            <tr>
              <th>Titre</th>
              <th>Date</th>
              <th>Inscrits</th>
              <th>Statut</th>
              <th class="actions-header">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="workshop in workshops"
              :key="workshop.id"
              class="workshop-row"
            >
              <td class="workshop-title-cell">
                <div class="workshop-title">{{ workshop.title }}</div>
                <div class="workshop-instructor">{{ workshop.instructor }}</div>
              </td>
              <td class="workshop-date-cell">
                <div class="date-text">{{ formatDate(workshop.date) }}</div>
                <div class="time-text">{{ formatTime(workshop.time) }}</div>
              </td>
              <td class="workshop-registrations-cell">
                <div class="registrations-count">
                  {{ workshop.registrations_count }} /
                  {{ workshop.max_participants }}
                </div>
                <div class="remaining-seats">
                  {{ workshop.remaining_seats }} places restantes
                </div>
              </td>
              <td class="workshop-status-cell">
                <span
                  :class="[
                    'status-badge',
                    workshop.is_full ? 'status-full' : 'status-available',
                  ]"
                >
                  {{ workshop.is_full ? "Complet" : "Disponible" }}
                </span>
              </td>
              <td class="workshop-actions-cell">
                <div class="actions-container">
                  <router-link
                    :to="`/admin/workshops/${workshop.id}/registrations`"
                    class="action-link action-view"
                  >
                    <UsersIcon class="action-icon" />
                    <span class="action-text">Voir inscrits</span>
                  </router-link>
                  <router-link
                    :to="`/admin/workshops/${workshop.id}/edit`"
                    class="action-link action-edit"
                  >
                    <PencilSquareIcon class="action-icon" />
                    <span class="action-text">Modifier</span>
                  </router-link>
                  <button
                    @click="handleDelete(workshop.id)"
                    :disabled="deleting === workshop.id"
                    class="action-button action-delete"
                  >
                    <TrashIcon class="action-icon" />
                    <span class="action-text">Supprimer</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  EyeIcon,
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  UsersIcon,
} from "@heroicons/vue/24/outline";
import { ref, onMounted } from "vue";
import api from "@/services/api";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import Alert from "@/Components/Alert.vue";

const workshops = ref([]);
const loading = ref(true);
const deleting = ref(null);
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

const handleDelete = async (workshopId) => {
  if (!confirm("Êtes-vous sûr de vouloir supprimer ce workshop?")) return;

  deleting.value = workshopId;

  try {
    await api.deleteWorkshop(workshopId);
    message.value = {
      text: "Workshop supprimé avec succès",
      type: "success",
    };
    await fetchWorkshops();
  } catch (error) {
    message.value = {
      text: "Erreur lors de la suppression",
      type: "error",
    };
  } finally {
    deleting.value = null;
    setTimeout(() => {
      message.value = { text: "", type: "info" };
    }, 5000);
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const formatTime = (time) => {
  return new Date(`2000-01-01 ${time}`).toLocaleTimeString("fr-FR", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

onMounted(fetchWorkshops);
</script>

<style scoped>
/* Container principal */
.admin-dashboard {
  padding: 1rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* Header du dashboard */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.dashboard-title {
  font-size: 1.875rem;
  font-weight: bold;
  color: #111827;
  margin: 0;
}

/* Bouton créer workshop */
.btn-create {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #4f46e5;
  color: white;
  padding: 0.625rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 500;
  transition: background-color 0.2s ease;
}

.btn-create:hover {
  background-color: #4338ca;
}

.icon-small {
  width: 1rem;
  height: 1rem;
}

/* Contenu du dashboard */
.dashboard-content {
  margin-top: 1.5rem;
}

/* État vide */
.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.empty-text {
  color: #6b7280;
  font-size: 1.125rem;
  margin-bottom: 1rem;
}

.btn-create-first {
  display: inline-block;
  background-color: #4f46e5;
  color: white;
  padding: 0.625rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  transition: background-color 0.2s ease;
}

.btn-create-first:hover {
  background-color: #4338ca;
}

/* Container du tableau */
.workshops-table-container {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
}

/* Tableau des workshops */
.workshops-table {
  width: 100%;
  border-collapse: collapse;
}

.workshops-table thead {
  background-color: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.workshops-table th {
  padding: 0.75rem 1.5rem;
  text-align: left;
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.actions-header {
  text-align: right;
}

.workshops-table tbody {
  background-color: white;
}

.workshop-row {
  border-bottom: 1px solid #e5e7eb;
  transition: background-color 0.15s ease;
}

.workshop-row:hover {
  background-color: #f9fafb;
}

.workshops-table td {
  padding: 1rem 1.5rem;
}

/* Cellule titre */
.workshop-title-cell {
  min-width: 250px;
}

.workshop-title {
  font-weight: 500;
  color: #111827;
  margin-bottom: 0.25rem;
}

.workshop-instructor {
  font-size: 0.875rem;
  color: #6b7280;
}

/* Cellule date */
.workshop-date-cell {
  min-width: 150px;
}

.date-text {
  font-size: 0.875rem;
  color: #374151;
  margin-bottom: 0.25rem;
}

.time-text {
  font-size: 0.875rem;
  color: #6b7280;
}

/* Cellule inscriptions */
.workshop-registrations-cell {
  min-width: 150px;
}

.registrations-count {
  font-size: 0.875rem;
  font-weight: 500;
  color: #111827;
  margin-bottom: 0.25rem;
}

.remaining-seats {
  font-size: 0.75rem;
  color: #6b7280;
}

/* Cellule statut */
.workshop-status-cell {
  min-width: 120px;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 9999px;
}

.status-full {
  background-color: #fee2e2;
  color: #991b1b;
}

.status-available {
  background-color: #d1fae5;
  color: #065f46;
}

/* Cellule actions */
.workshop-actions-cell {
  min-width: 300px;
}

.actions-container {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.action-link,
.action-button {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.15s ease;
  white-space: nowrap;
}

.action-button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
}

.action-icon {
  width: 1.25rem;
  height: 1.25rem;
  flex-shrink: 0;
}

.action-view {
  color: #4f46e5;
}

.action-view:hover {
  color: #3730a3;
}

.action-edit {
  color: #2563eb;
}

.action-edit:hover {
  color: #1e40af;
}

.action-delete {
  color: #dc2626;
}

.action-delete:hover {
  color: #991b1b;
}

.action-delete:disabled {
  color: #9ca3af;
  cursor: not-allowed;
  opacity: 0.5;
}

/* Responsive Design */

/* Tablettes (768px - 1024px) */
@media (max-width: 1024px) {
  .dashboard-title {
    font-size: 1.5rem;
  }

  .workshops-table th,
  .workshops-table td {
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
  }

  .workshop-title-cell {
    min-width: 200px;
  }

  .workshop-actions-cell {
    min-width: 250px;
  }

  .actions-container {
    gap: 0.5rem;
  }

  .action-text {
    display: none;
  }

  .action-icon {
    width: 1.5rem;
    height: 1.5rem;
  }
}

/* Mobile (max 767px) */
@media (max-width: 767px) {
  .admin-dashboard {
    padding: 0.5rem;
  }

  .dashboard-header {
    flex-direction: column;
    align-items: stretch;
    margin-bottom: 1.5rem;
  }

  .dashboard-title {
    font-size: 1.25rem;
    text-align: center;
  }

  .btn-create {
    justify-content: center;
    width: 100%;
  }

  /* Transformation du tableau en cartes sur mobile */
  .workshops-table-container {
    border-radius: 0;
  }

  .workshops-table thead {
    display: none;
  }

  .workshops-table,
  .workshops-table tbody,
  .workshops-table tr,
  .workshops-table td {
    display: block;
    width: 100%;
  }

  .workshop-row {
    margin-bottom: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    background-color: white;
  }

  .workshop-row:hover {
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .workshops-table td {
    padding: 0.5rem 0;
    border: none;
  }

  .workshop-title-cell,
  .workshop-date-cell,
  .workshop-registrations-cell,
  .workshop-status-cell,
  .workshop-actions-cell {
    min-width: auto;
  }

  .workshop-title-cell {
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
  }

  .workshop-title {
    font-size: 1.125rem;
    margin-bottom: 0.375rem;
  }

  .workshop-date-cell::before {
    content: "📅 ";
    font-weight: 600;
    color: #6b7280;
  }

  .workshop-registrations-cell::before {
    content: "👥 ";
    font-weight: 600;
    color: #6b7280;
  }

  .workshop-status-cell {
    margin: 0.75rem 0;
  }

  .actions-container {
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
  }

  .action-link,
  .action-button {
    width: 100%;
    justify-content: center;
    padding: 0.625rem 1rem;
    border-radius: 0.375rem;
    border: 1px solid currentColor;
  }

  .action-text {
    display: inline;
  }

  .action-view {
    background-color: #eef2ff;
  }

  .action-edit {
    background-color: #dbeafe;
  }

  .action-delete {
    background-color: #fee2e2;
  }

  .empty-state {
    padding: 2rem 1rem;
  }

  .empty-text {
    font-size: 1rem;
  }
}

/* Très petits écrans (max 375px) */
@media (max-width: 375px) {
  .dashboard-title {
    font-size: 1.125rem;
  }

  .workshop-title {
    font-size: 1rem;
  }

  .action-link,
  .action-button {
    font-size: 0.8125rem;
    padding: 0.5rem 0.75rem;
  }

  .action-icon {
    width: 1rem;
    height: 1rem;
  }
}
</style>