<template>
  <AppLayout>
    <v-card>
      <v-card-title class="d-flex align-center">
        <span>Ventes du Jour</span>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="dialog = true">
          Nouvelle Vente
        </v-btn>
      </v-card-title>

      <v-card-text>
        <v-tabs v-model="activeTab">
          <v-tab value="today">Aujourd'hui</v-tab>
          <v-tab value="history">Historique</v-tab>
        </v-tabs>

        <v-window v-model="activeTab">
          <v-window-item value="today">
            <div class="text-h6 my-4">
              Total: {{ formatPrice(total) }} FCFA
            </div>

            <v-table>
              <thead>
                <tr>
                  <th>Article</th>
                  <th>Quantité</th>
                  <th>Prix</th>
                  <th>Total</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="vente in ventes" :key="vente.id">
                  <td>{{ vente.article.nom }}</td>
                  <td>{{ vente.quantite }}</td>
                  <td>{{ formatPrice(vente.prix) }} FCFA</td>
                  <td>{{ formatPrice(vente.prix * vente.quantite) }} FCFA</td>
                  <td>
                    <v-btn
                      icon="mdi-delete"
                      variant="text"
                      color="error"
                      size="small"
                      @click="deleteVente(vente.id)"
                    ></v-btn>
                  </td>
                </tr>
              </tbody>
            </v-table>
          </v-window-item>

          <v-window-item value="history">
            <v-row class="my-4" align="center">
              <v-col cols="auto">
                <v-date-picker
                  v-model="selectedDate"
                  @update:model-value="loadVentesByDate"
                  locale="fr"
                ></v-date-picker>
              </v-col>
              <v-col>
                <div v-if="historicalVentes.length > 0">
                  <div class="text-h6 mb-4">
                    Total: {{ formatPrice(historicalTotal) }} FCFA
                  </div>
                  <v-table>
                    <thead>
                      <tr>
                        <th>Article</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="vente in historicalVentes" :key="vente.id">
                        <td>{{ vente.article.nom }}</td>
                        <td>{{ vente.quantite }}</td>
                        <td>{{ formatPrice(vente.prix) }} FCFA</td>
                        <td>{{ formatPrice(vente.prix * vente.quantite) }} FCFA</td>
                      </tr>
                    </tbody>
                  </v-table>
                </div>
                <div v-else class="text-center">
                  Aucune vente pour cette date
                </div>
              </v-col>
            </v-row>
          </v-window-item>
        </v-window>
      </v-card-text>
    </v-card>

    <!-- Nouvelle Vente Dialog -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>Nouvelle Vente</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="submitVente">
            <v-select
              v-model="form.article_id"
              :items="articles"
              item-title="nom"
              item-value="id"
              label="Article"
              required
            ></v-select>

            <v-text-field
              v-model.number="form.quantite"
              label="Quantité"
              type="number"
              min="1"
              required
            ></v-text-field>

            <v-text-field
              v-model.number="form.prix"
              label="Prix"
              type="number"
              min="0"
              required
            ></v-text-field>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" text @click="dialog = false">Annuler</v-btn>
              <v-btn color="primary" type="submit">Enregistrer</v-btn>
            </v-card-actions>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  ventes: Array,
  total: Number,
  articles: Array
})

const dialog = ref(false)
const activeTab = ref('today')
const selectedDate = ref(new Date())
const historicalVentes = ref([])
const historicalTotal = ref(0)

const form = useForm({
  article_id: '',
  quantite: 1,
  prix: ''
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price)
}

const submitVente = () => {
  form.post(route('ventes.store'), {
    onSuccess: () => {
      dialog.value = false
      form.reset()
    }
  })
}

const deleteVente = (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette vente ?')) {
    router.delete(route('ventes.destroy', id))
  }
}

const loadVentesByDate = async () => {
  if (!selectedDate.value) return

  try {
    const formattedDate = selectedDate.value.toISOString().split('T')[0]
    const response = await fetch(`/ventes/by-date?date=${formattedDate}`)
    const data = await response.json()
    historicalVentes.value = data.ventes
    historicalTotal.value = data.total
  } catch (error) {
    console.error('Error loading ventes:', error)
  }
}

// Watch for changes in the selected date
watch(selectedDate, (newDate) => {
  if (newDate) {
    loadVentesByDate()
  }
})
</script> 