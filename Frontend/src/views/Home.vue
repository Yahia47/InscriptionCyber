<template>
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-12">
      <h1 class="text-5xl font-bold text-gray-900 mb-4">
        Bienvenue sur <span class="text-indigo-600 typing">CyberNexus</span>
      </h1>
      <p class="text-xl text-gray-600 mb-8">
        Plateforme de gestion des workshops
      </p>
      <router-link
        to="/workshops"
        class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition text-lg font-semibold"
      >
        Découvrir les Workshops
      </router-link>
    </div>

    <div class="grid md:grid-cols-3 gap-8 mt-16">
      <div class="text-center p-6 bg-white rounded-lg shadow-md">
        <div class="text-4xl mb-4">📚</div>
        <h3 class="text-xl font-bold mb-2">Apprendre</h3>
        <p class="text-gray-600">
          Des workshops de qualité sur les algorithmes
        </p>
      </div>
      <div class="text-center p-6 bg-white rounded-lg shadow-md">
        <div class="text-4xl mb-4">👥</div>
        <h3 class="text-xl font-bold mb-2">Collaborer</h3>
        <p class="text-gray-600">Rejoignez une communauté de passionnés</p>
      </div>
      <div class="text-center p-6 bg-white rounded-lg shadow-md">
        <div class="text-4xl mb-4">🚀</div>
        <h3 class="text-xl font-bold mb-2">Progresser</h3>
        <p class="text-gray-600">Améliorez vos compétences en programmation</p>
      </div>
    </div>
  </div>
</template>
