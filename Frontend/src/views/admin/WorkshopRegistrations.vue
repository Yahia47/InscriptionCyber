<template>
  <div class="registrations-page">
    <div class="page-header">
      <router-link to="/admin" class="back-link">
        ← Retour au tableau de bord
      </router-link>
      <h1 class="page-title">Inscrits au Workshop</h1>
    </div>

    <LoadingSpinner v-if="loading" />

    <div v-else class="page-content">
      <!-- Info Workshop -->
      <div class="workshop-info-card">
        <h2 class="workshop-title">{{ workshop?.title }}</h2>
        <p class="workshop-details">
          📅 {{ formatDate(workshop?.date) }} à {{ formatTime(workshop?.time) }}
        </p>
        <div class="workshop-stats">
          <div class="stat-item">
            <span class="stat-label">Total inscrits:</span>
            <span class="stat-value">{{ registrations.length }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Places disponibles:</span>
            <span class="stat-value">{{ workshop?.remaining_seats }}</span>
          </div>
        </div>
      </div>

      <!-- Actions rapides -->
      <div class="quick-actions-card">
        <h3 class="actions-title">Actions rapides</h3>
        <div class="actions-buttons">
          <button @click="exportToCSV" class="action-btn btn-export">
            📊 Exporter en CSV
          </button>
          <button @click="printList" class="action-btn btn-print">
            🖨️ Imprimer la liste
          </button>
          <button @click="copyEmails" class="action-btn btn-copy">
            📧 Copier les emails
          </button>
        </div>
        <Alert
          v-if="message.text"
          :type="message.type"
          :message="message.text"
          class="mt-4"
        />
      </div>

      <!-- Liste des inscrits -->
      <div class="registrations-card">
        <div class="card-header">
          <h3 class="card-title">Liste des inscrits</h3>
          <span class="registrations-count"
            >{{ registrations.length }} personne(s)</span
          >
        </div>

        <div v-if="registrations.length === 0" class="empty-state">
          Aucun inscrit pour le moment
        </div>

        <div v-else class="table-container">
          <table class="registrations-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Date d'inscription</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(reg, index) in registrations"
                :key="reg.id"
                class="table-row"
              >
                <td class="cell-number">{{ index + 1 }}</td>
                <td class="cell-name">{{ reg.user.name }}</td>
                <td class="cell-email">{{ reg.user.email }}</td>
                <td class="cell-date">
                  {{ formatRegistrationDate(reg.registered_at) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import api from "@/services/api";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import Alert from "@/Components/Alert.vue";

const route = useRoute();

const workshop = ref(null);
const registrations = ref([]);
const loading = ref(true);
const message = ref({ text: "", type: "info" });

const fetchRegistrations = async () => {
  try {
    const response = await api.getWorkshopRegistrations(route.params.id);
    workshop.value = response.data.workshop;
    registrations.value = response.data.registrations;
  } catch (error) {
    message.value = {
      text: "Erreur lors du chargement des inscriptions",
      type: "error",
    };
  } finally {
    loading.value = false;
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
  if (!time) return "";
  return new Date(`2000-01-01 ${time}`).toLocaleTimeString("fr-FR", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

const formatRegistrationDate = (date) => {
  return new Date(date).toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Exporter la liste en Excel (HTML structuré)
const exportToCSV = () => {
  if (registrations.value.length === 0) {
    message.value = {
      text: "Aucune donnée à exporter",
      type: "error",
    };
    return;
  }

  // Créer un tableau HTML bien structuré pour Excel
  let htmlContent = `
    <html xmlns:x="urn:schemas-microsoft-com:office:excel">
      <head>
        <meta charset="UTF-8">
        <style>
          table { 
            border-collapse: collapse; 
            width: 100%; 
            font-family: Arial, sans-serif;
          }
          .header-row { 
            background-color: #4f46e5; 
            color: white; 
            font-weight: bold;
            text-align: left;
          }
          .header-row th { 
            padding: 12px; 
            border: 1px solid #3730a3;
          }
          .workshop-info {
            background-color: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
          }
          .workshop-info h2 {
            color: #111827;
            margin: 0 0 10px 0;
          }
          .workshop-info p {
            color: #6b7280;
            margin: 5px 0;
          }
          tbody tr:nth-child(even) { 
            background-color: #f9fafb; 
          }
          tbody tr:hover { 
            background-color: #e5e7eb; 
          }
          td { 
            padding: 10px 12px; 
            border: 1px solid #e5e7eb;
            color: #374151;
          }
          .number-cell { 
            text-align: center; 
            font-weight: 600;
            color: #6b7280;
          }
          .name-cell { 
            font-weight: 500;
            color: #111827;
          }
          .email-cell {
            color: #4b5563;
          }
          .date-cell {
            color: #6b7280;
          }
          .footer {
            margin-top: 20px;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
            text-align: center;
            color: #6b7280;
          }
        </style>
      </head>
      <body>
        <div class="workshop-info">
          <h2>${workshop.value?.title}</h2>
          <p><strong>📅 Date:</strong> ${formatDate(
            workshop.value?.date
          )} à ${formatTime(workshop.value?.time)}</p>
          <p><strong>👥 Total inscrits:</strong> ${
            registrations.value.length
          } personne(s)</p>
          <p><strong>📊 Places restantes:</strong> ${
            workshop.value?.remaining_seats
          }</p>
          <p><strong>📝 Exporté le:</strong> ${new Date().toLocaleString(
            "fr-FR"
          )}</p>
        </div>
        
        <table>
          <thead>
            <tr class="header-row">
              <th style="width: 60px;">#</th>
              <th style="width: 250px;">Nom Complet</th>
              <th style="width: 300px;">Adresse Email</th>
              <th style="width: 200px;">Date d'Inscription</th>
            </tr>
          </thead>
          <tbody>
  `;

  registrations.value.forEach((reg, index) => {
    htmlContent += `
      <tr>
        <td class="number-cell">${index + 1}</td>
        <td class="name-cell">${reg.user.name}</td>
        <td class="email-cell">${reg.user.email}</td>
        <td class="date-cell">${formatRegistrationDate(reg.registered_at)}</td>
      </tr>
    `;
  });

  htmlContent += `
          </tbody>
        </table>
        
        <div class="footer">
          <p>Document généré automatiquement - ${workshop.value?.title}</p>
          <p>© ${new Date().getFullYear()} - Gestion des Workshops</p>
        </div>
      </body>
    </html>
  `;

  // Créer le fichier avec encodage UTF-8 pour Excel
  const blob = new Blob(["\ufeff", htmlContent], {
    type: "application/vnd.ms-excel;charset=utf-8;",
  });

  const link = document.createElement("a");
  const url = URL.createObjectURL(blob);
  link.setAttribute("href", url);
  link.setAttribute(
    "download",
    `Inscrits_${workshop.value?.title.replace(/[^a-z0-9]/gi, "_")}.xls`
  );
  link.click();
  URL.revokeObjectURL(url);

  message.value = {
    text: "Liste exportée avec succès en format Excel!",
    type: "success",
  };
  setTimeout(() => {
    message.value = { text: "", type: "info" };
  }, 3000);
};

// Imprimer la liste avec style
const printList = () => {
  // Créer une nouvelle fenêtre pour l'impression
  const printWindow = window.open("", "_blank");

  const printContent = `
    <!DOCTYPE html>
    <html lang="fr">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Liste des Inscrits - ${workshop.value?.title}</title>
      <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }
        
        body {
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          padding: 20mm;
          color: #374151;
          background: white;
        }
        
        .print-header {
          text-align: center;
          margin-bottom: 30px;
          border-bottom: 3px solid #4f46e5;
          padding-bottom: 20px;
        }
        
        .print-header h1 {
          color: #111827;
          font-size: 28px;
          margin-bottom: 10px;
        }
        
        .print-header .subtitle {
          color: #6b7280;
          font-size: 14px;
        }
        
        .workshop-details {
          background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
          padding: 20px;
          border-radius: 10px;
          margin-bottom: 30px;
          border: 2px solid #e5e7eb;
        }
        
        .workshop-details h2 {
          color: #111827;
          font-size: 22px;
          margin-bottom: 15px;
          border-bottom: 2px solid #4f46e5;
          padding-bottom: 10px;
        }
        
        .detail-row {
          display: flex;
          justify-content: space-between;
          padding: 10px 0;
          border-bottom: 1px solid #e5e7eb;
        }
        
        .detail-row:last-child {
          border-bottom: none;
        }
        
        .detail-label {
          font-weight: 600;
          color: #6b7280;
          font-size: 14px;
        }
        
        .detail-value {
          font-weight: 700;
          color: #4f46e5;
          font-size: 16px;
        }
        
        .stats-grid {
          display: grid;
          grid-template-columns: repeat(3, 1fr);
          gap: 15px;
          margin: 20px 0;
        }
        
        .stat-box {
          background: white;
          padding: 15px;
          border-radius: 8px;
          border: 2px solid #e5e7eb;
          text-align: center;
        }
        
        .stat-box .label {
          color: #6b7280;
          font-size: 12px;
          font-weight: 600;
          text-transform: uppercase;
          margin-bottom: 8px;
        }
        
        .stat-box .value {
          color: #4f46e5;
          font-size: 24px;
          font-weight: bold;
        }
        
        .table-section {
          margin-top: 30px;
        }
        
        .table-section h3 {
          color: #111827;
          font-size: 18px;
          margin-bottom: 15px;
          padding: 10px;
          background: #f3f4f6;
          border-radius: 8px;
          border-left: 4px solid #4f46e5;
        }
        
        table {
          width: 100%;
          border-collapse: collapse;
          margin-bottom: 20px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        thead {
          background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        }
        
        thead th {
          color: white;
          padding: 15px 12px;
          text-align: left;
          font-weight: 700;
          font-size: 13px;
          text-transform: uppercase;
          letter-spacing: 0.5px;
          border-right: 1px solid rgba(255,255,255,0.2);
        }
        
        thead th:last-child {
          border-right: none;
        }
        
        tbody tr {
          border-bottom: 1px solid #e5e7eb;
          transition: background-color 0.2s;
        }
        
        tbody tr:nth-child(even) {
          background-color: #f9fafb;
        }
        
        tbody tr:hover {
          background-color: #eef2ff;
        }
        
        tbody td {
          padding: 12px;
          font-size: 14px;
          border-right: 1px solid #e5e7eb;
        }
        
        tbody td:last-child {
          border-right: none;
        }
        
        .cell-number {
          text-align: center;
          font-weight: 700;
          color: #6b7280;
          background-color: #f3f4f6;
          width: 60px;
        }
        
        .cell-name {
          font-weight: 600;
          color: #111827;
        }
        
        .cell-email {
          color: #4b5563;
          font-style: italic;
        }
        
        .cell-date {
          color: #6b7280;
          font-size: 13px;
        }
        
        .print-footer {
          margin-top: 40px;
          padding-top: 20px;
          border-top: 2px solid #e5e7eb;
          text-align: center;
          color: #6b7280;
          font-size: 12px;
        }
        
        .print-footer p {
          margin: 5px 0;
        }
        
        .print-footer .bold {
          font-weight: 700;
          color: #4f46e5;
        }
        
        @media print {
          body {
            padding: 10mm;
          }
          
          .workshop-details,
          .stat-box {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
          }
          
          thead {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
          }
          
          tbody tr {
            page-break-inside: avoid;
          }
          
          @page {
            margin: 15mm;
            size: A4;
          }
        }
      </style>
    </head>
    <body>
      <div class="print-header">
        <h1>📋 Liste des Inscrits au Workshop</h1>
        <p class="subtitle">Document généré le ${new Date().toLocaleDateString(
          "fr-FR",
          {
            day: "numeric",
            month: "long",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
          }
        )}</p>
      </div>
      
      <div class="workshop-details">
        <h2>${workshop.value?.title}</h2>
        
        <div class="detail-row">
          <span class="detail-label">📅 Date du Workshop</span>
          <span class="detail-value">${formatDate(
            workshop.value?.date
          )} à ${formatTime(workshop.value?.time)}</span>
        </div>
        
        <div class="stats-grid">
          <div class="stat-box">
            <div class="label">👥 Total Inscrits</div>
            <div class="value">${registrations.value.length}</div>
          </div>
          <div class="stat-box">
            <div class="label">📊 Places Restantes</div>
            <div class="value">${workshop.value?.remaining_seats || 0}</div>
          </div>
          <div class="stat-box">
            <div class="label">🎯 Capacité Max</div>
            <div class="value">${workshop.value?.max_participants || 0}</div>
          </div>
        </div>
      </div>
      
      <div class="table-section">
        <h3>Liste Complète des Participants</h3>
        <table>
          <thead>
            <tr>
              <th style="width: 60px;">#</th>
              <th style="width: 30%;">Nom Complet</th>
              <th style="width: 35%;">Adresse Email</th>
              <th style="width: 25%;">Date d'Inscription</th>
            </tr>
          </thead>
          <tbody>
            ${registrations.value
              .map(
                (reg, index) => `
              <tr>
                <td class="cell-number">${index + 1}</td>
                <td class="cell-name">${reg.user.name}</td>
                <td class="cell-email">${reg.user.email}</td>
                <td class="cell-date">${formatRegistrationDate(
                  reg.registered_at
                )}</td>
              </tr>
            `
              )
              .join("")}
          </tbody>
        </table>
      </div>
      
      <div class="print-footer">
        <p><span class="bold">Document Officiel</span> - Gestion des Workshops</p>
        <p>© ${new Date().getFullYear()} - Tous droits réservés</p>
        <p>Généré automatiquement par le système de gestion</p>
      </div>
    </body>
    </html>
  `;

  printWindow.document.write(printContent);
  printWindow.document.close();

  // Attendre que le contenu soit chargé avant d'imprimer
  printWindow.onload = () => {
    setTimeout(() => {
      printWindow.print();
      // Fermer la fenêtre après l'impression (optionnel)
      // printWindow.close();
    }, 250);
  };

  message.value = {
    text: "Préparation de l'impression...",
    type: "info",
  };
  setTimeout(() => {
    message.value = { text: "", type: "info" };
  }, 2000);
};

// Copier tous les emails
const copyEmails = () => {
  if (registrations.value.length === 0) {
    message.value = {
      text: "Aucun email à copier",
      type: "error",
    };
    return;
  }

  const emails = registrations.value.map((reg) => reg.user.email).join(", ");
  navigator.clipboard.writeText(emails).then(() => {
    message.value = {
      text: `${registrations.value.length} email(s) copié(s) dans le presse-papiers!`,
      type: "success",
    };
    setTimeout(() => {
      message.value = { text: "", type: "info" };
    }, 3000);
  });
};

onMounted(fetchRegistrations);
</script>

<style scoped>
/* Container principal */
.registrations-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem;
}

/* Header */
.page-header {
  margin-bottom: 2rem;
}

.back-link {
  color: #4f46e5;
  text-decoration: none;
  display: inline-block;
  margin-bottom: 1rem;
  transition: all 0.2s ease;
}

.back-link:hover {
  text-decoration: underline;
  color: #3730a3;
}

.page-title {
  font-size: 1.875rem;
  font-weight: bold;
  color: #111827;
  margin: 0;
}

/* Content */
.page-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Workshop Info Card */
.workshop-info-card {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.workshop-title {
  font-size: 1.5rem;
  font-weight: bold;
  color: #111827;
  margin-bottom: 0.5rem;
}

.workshop-details {
  color: #6b7280;
  margin-bottom: 1rem;
  font-size: 1rem;
}

.workshop-stats {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: bold;
  color: #4f46e5;
}

/* Quick Actions Card */
.quick-actions-card {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.actions-title {
  font-size: 1.25rem;
  font-weight: bold;
  color: #111827;
  margin-bottom: 1rem;
}

.actions-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  border-radius: 0.5rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.btn-export {
  background-color: #10b981;
  color: white;
}

.btn-export:hover {
  background-color: #059669;
}

.btn-print {
  background-color: #6366f1;
  color: white;
}

.btn-print:hover {
  background-color: #4f46e5;
}

.btn-copy {
  background-color: #f59e0b;
  color: white;
}

.btn-copy:hover {
  background-color: #d97706;
}

/* Registrations Card */
.registrations-card {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.card-title {
  font-size: 1.25rem;
  font-weight: bold;
  color: #111827;
  margin: 0;
}

.registrations-count {
  background-color: #4f46e5;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
}

.empty-state {
  padding: 3rem 1.5rem;
  text-align: center;
  color: #6b7280;
  font-size: 1rem;
}

/* Table */
.table-container {
  overflow-x: auto;
}

.registrations-table {
  width: 100%;
  border-collapse: collapse;
}

.registrations-table thead {
  background-color: #f9fafb;
}

.registrations-table th {
  padding: 0.75rem 1.5rem;
  text-align: left;
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.registrations-table tbody {
  background-color: white;
}

.table-row {
  border-bottom: 1px solid #e5e7eb;
  transition: background-color 0.15s ease;
}

.table-row:hover {
  background-color: #f9fafb;
}

.registrations-table td {
  padding: 1rem 1.5rem;
  font-size: 0.875rem;
}

.cell-number {
  color: #6b7280;
  font-weight: 500;
}

.cell-name {
  font-weight: 500;
  color: #111827;
}

.cell-email {
  color: #4b5563;
}

.cell-date {
  color: #6b7280;
}

/* Print Styles */
@media print {
  /* Masquer les éléments non nécessaires */
  .back-link,
  .quick-actions-card {
    display: none !important;
  }

  .registrations-page {
    padding: 0;
    max-width: 100%;
  }

  /* En-tête de page pour l'impression */
  .page-header {
    margin-bottom: 1.5rem;
    page-break-after: avoid;
  }

  .page-title {
    color: #111827;
    font-size: 1.75rem;
    border-bottom: 3px solid #4f46e5;
    padding-bottom: 0.5rem;
  }

  /* Info Workshop stylisée */
  .workshop-info-card {
    box-shadow: none;
    border: 2px solid #e5e7eb;
    margin-bottom: 1.5rem;
    page-break-after: avoid;
    background-color: #f9fafb !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .workshop-title {
    color: #111827 !important;
    font-size: 1.5rem;
    border-bottom: 1px solid #d1d5db;
    padding-bottom: 0.5rem;
  }

  .workshop-details {
    font-size: 1rem;
    color: #374151 !important;
  }

  .workshop-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #d1d5db;
  }

  .stat-item {
    background-color: white !important;
    padding: 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid #e5e7eb;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .stat-label {
    font-size: 0.875rem;
    color: #6b7280 !important;
    font-weight: 600;
  }

  .stat-value {
    font-size: 1.5rem;
    color: #4f46e5 !important;
    font-weight: bold;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  /* Card des inscrits */
  .registrations-card {
    box-shadow: none;
    border: 2px solid #e5e7eb;
    page-break-inside: avoid;
  }

  .card-header {
    background-color: #f3f4f6 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    border-bottom: 2px solid #d1d5db;
  }

  .card-title {
    color: #111827 !important;
    font-size: 1.25rem;
  }

  .registrations-count {
    background-color: #4f46e5 !important;
    color: white !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  /* Table d'impression */
  .table-container {
    page-break-inside: auto;
  }

  .registrations-table {
    border-collapse: collapse;
    width: 100%;
  }

  .registrations-table thead {
    background-color: #4f46e5 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .registrations-table th {
    color: white !important;
    padding: 0.75rem;
    font-weight: bold;
    border: 1px solid #3730a3;
    font-size: 0.875rem;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .table-row {
    page-break-inside: avoid;
    border-bottom: 1px solid #d1d5db;
  }

  .table-row:nth-child(even) {
    background-color: #f9fafb !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .table-row:hover {
    background-color: transparent;
  }

  .registrations-table td {
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    font-size: 0.875rem;
    color: #374151 !important;
  }

  .cell-number {
    text-align: center;
    font-weight: 600;
    color: #6b7280 !important;
    background-color: #f3f4f6 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .cell-name {
    font-weight: 600;
    color: #111827 !important;
  }

  .cell-email {
    color: #4b5563 !important;
  }

  .cell-date {
    color: #6b7280 !important;
  }

  /* Pied de page */
  @page {
    margin: 1.5cm;
    @bottom-center {
      content: "Page " counter(page) " sur " counter(pages);
      font-size: 10pt;
      color: #6b7280;
    }
  }

  /* Ajouter un footer */
  .registrations-card::after {
    content: "Document généré le " attr(data-date)
      " - © 2024 Gestion des Workshops";
    display: block;
    text-align: center;
    padding: 1rem;
    margin-top: 1rem;
    color: #6b7280;
    font-size: 0.75rem;
    border-top: 1px solid #e5e7eb;
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-title {
    font-size: 1.5rem;
  }

  .workshop-title {
    font-size: 1.25rem;
  }

  .workshop-stats {
    gap: 1rem;
  }

  .stat-value {
    font-size: 1.25rem;
  }

  .actions-buttons {
    flex-direction: column;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .card-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }

  /* Transform table to cards on mobile */
  .registrations-table thead {
    display: none;
  }

  .registrations-table,
  .registrations-table tbody,
  .registrations-table tr,
  .registrations-table td {
    display: block;
    width: 100%;
  }

  .table-row {
    margin-bottom: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
  }

  .table-row:hover {
    background-color: white;
  }

  .registrations-table td {
    padding: 0.5rem 0;
    border: none;
  }

  .cell-number::before {
    content: "# ";
    font-weight: 600;
    color: #6b7280;
  }

  .cell-name::before {
    content: "👤 ";
    font-weight: 600;
  }

  .cell-email::before {
    content: "✉️ ";
    font-weight: 600;
  }

  .cell-date::before {
    content: "📅 ";
    font-weight: 600;
  }
}
</style>