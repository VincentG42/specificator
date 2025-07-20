<template>
  <div class="max-w-4xl mx-auto text-center py-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
      🎉 Spécifications générées !
    </h1>
    <p class="text-lg text-gray-600 mb-8">
      Vos spécifications sont prêtes à être téléchargées.
    </p>

    <button @click="downloadExport" class="bg-primary-500 hover:bg-primary-600 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
      ⬇️ Télécharger les spécifications
    </button>

    <div class="mt-12 text-gray-500 text-sm">
      <p>Merci d'avoir utilisé le Générateur de Spécifications.</p>
      <p>N'oubliez pas de valider et d'affiner le contenu généré.</p>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const downloadExport = async () => {
  try {
    const response = await axios.post(`/projects/${route.params.id}/export`, {}, { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `specifications-projet-${route.params.id}.zip`)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Erreur lors du téléchargement de l\'export:', error)
    alert('Erreur lors de la génération ou du téléchargement des spécifications.')
  }
}
</script>

