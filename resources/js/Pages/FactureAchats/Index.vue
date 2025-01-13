<template>
  <AppLayout>
    <v-card>
      <v-card-title class="d-flex align-center">
        <span>Factures d'Achat</span>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="openDialog()">
          Nouvelle Facture
        </v-btn>
      </v-card-title>

      <v-card-text>
        <v-table>
          <thead>
            <tr>
              <th>Titre</th>
              <th>Fournisseur</th>
              <th>Date d'achat</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="facture in factureAchats" :key="facture.id">
              <td>{{ facture.titre }}</td>
              <td>{{ facture.fournisseur.nom }}</td>
              <td>{{ formatDate(facture.date_achat) }}</td>
              <td>{{ formatPrice(calculateTotal(facture)) }} FCFA</td>
              <td>
                <v-btn
                  icon="mdi-eye"
                  variant="text"
                  color="info"
                  size="small"
                  class="mr-2"
                  @click="openDetailsDialog(facture)"
                ></v-btn>
                <v-btn
                  icon="mdi-pencil"
                  variant="text"
                  color="primary"
                  size="small"
                  class="mr-2"
                  @click="openDialog(facture)"
                ></v-btn>
                <v-btn
                  icon="mdi-delete"
                  variant="text"
                  color="error"
                  size="small"
                  @click="deleteFacture(facture.id)"
                ></v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card-text>
    </v-card>

    <!-- Facture Dialog -->
    <v-dialog v-model="dialog" max-width="800px">
      <v-card>
        <v-card-title>
          {{ editedId ? 'Modifier la Facture' : 'Nouvelle Facture' }}
        </v-card-title>
        <v-card-text>
          <v-form @submit.prevent="submitFacture">
            <v-text-field
              v-model="form.titre"
              label="Titre"
              required
            ></v-text-field>

            <v-select
              v-model="form.fournisseur_id"
              :items="fournisseurs"
              item-title="nom"
              item-value="id"
              label="Fournisseur"
              required
            ></v-select>

            <v-text-field
              v-model="form.date_achat"
              label="Date d'achat"
              type="date"
              required
            ></v-text-field>

            <v-divider class="my-4"></v-divider>
            <div class="text-h6 mb-2">Articles</div>

            <div v-for="(article, index) in form.articles" :key="index" class="d-flex align-center gap-2 mb-4">
              <v-select
                v-model="article.id"
                :items="articles"
                item-title="nom"
                item-value="id"
                label="Article"
                class="flex-grow-1"
                required
              ></v-select>

              <v-text-field
                v-model.number="article.quantite"
                label="Quantité"
                type="number"
                min="1"
                required
                style="max-width: 100px;"
              ></v-text-field>

              <v-text-field
                v-model.number="article.prix_achat"
                label="Prix"
                type="number"
                min="0"
                required
                style="max-width: 150px;"
              ></v-text-field>

              <v-btn
                icon="mdi-delete"
                variant="text"
                color="error"
                size="small"
                @click="removeArticle(index)"
                v-if="form.articles.length > 1"
              ></v-btn>
            </div>

            <v-btn
              color="secondary"
              variant="text"
              prepend-icon="mdi-plus"
              @click="addArticle"
              class="mb-4"
            >
              Ajouter un article
            </v-btn>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" text @click="dialog = false">Annuler</v-btn>
              <v-btn color="primary" type="submit">
                {{ editedId ? 'Modifier' : 'Enregistrer' }}
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Details Dialog -->
    <v-dialog v-model="detailsDialog" max-width="600px">
      <v-card v-if="selectedFacture">
        <v-card-title>Détails de la Facture</v-card-title>
        <v-card-text>
          <div class="text-h6">{{ selectedFacture.titre }}</div>
          <div class="text-subtitle-1">
            Fournisseur: {{ selectedFacture.fournisseur.nom }}
          </div>
          <div class="text-subtitle-2 mb-4">
            Date: {{ formatDate(selectedFacture.date_achat) }}
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
              <tr v-for="article in selectedFacture.articles" :key="article.id">
                <td>{{ article.nom }}</td>
                <td>{{ article.pivot.quantite }}</td>
                <td>{{ formatPrice(article.pivot.prix_achat) }} FCFA</td>
                <td>{{ formatPrice(article.pivot.quantite * article.pivot.prix_achat) }} FCFA</td>
              </tr>
              <tr class="font-weight-bold">
                <td colspan="3" class="text-right">Total:</td>
                <td>{{ formatPrice(calculateTotal(selectedFacture)) }} FCFA</td>
              </tr>
            </tbody>
          </v-table>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="detailsDialog = false">Fermer</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  factureAchats: Array,
  fournisseurs: Array,
  articles: Array
})

const dialog = ref(false)
const detailsDialog = ref(false)
const editedId = ref(null)
const selectedFacture = ref(null)

const form = useForm({
  titre: '',
  fournisseur_id: '',
  date_achat: new Date().toISOString().substr(0, 10),
  articles: [
    {
      id: '',
      quantite: 1,
      prix_achat: ''
    }
  ]
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price)
}

const calculateTotal = (facture) => {
  return facture.articles.reduce((total, article) => {
    return total + (article.pivot.quantite * article.pivot.prix_achat)
  }, 0)
}

const openDialog = (facture = null) => {
  if (facture) {
    editedId.value = facture.id
    form.titre = facture.titre
    form.fournisseur_id = facture.fournisseur_id
    form.date_achat = facture.date_achat
    form.articles = facture.articles.map(article => ({
      id: article.id,
      quantite: article.pivot.quantite,
      prix_achat: article.pivot.prix_achat
    }))
  } else {
    editedId.value = null
    form.reset()
    form.articles = [{ id: '', quantite: 1, prix_achat: '' }]
  }
  dialog.value = true
}

const openDetailsDialog = (facture) => {
  selectedFacture.value = facture
  detailsDialog.value = true
}

const addArticle = () => {
  form.articles.push({
    id: '',
    quantite: 1,
    prix_achat: ''
  })
}

const removeArticle = (index) => {
  form.articles.splice(index, 1)
}

const submitFacture = () => {
  if (editedId.value) {
    form.put(route('facture-achats.update', editedId.value), {
      onSuccess: () => {
        dialog.value = false
        form.reset()
      }
    })
  } else {
    form.post(route('facture-achats.store'), {
      onSuccess: () => {
        dialog.value = false
        form.reset()
      }
    })
  }
}

const deleteFacture = (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) {
    router.delete(route('facture-achats.destroy', id))
  }
}
</script> 