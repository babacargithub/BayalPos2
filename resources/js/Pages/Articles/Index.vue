<template>
  <AppLayout title="Articles">
    <v-card>
      <v-card-title class="d-flex align-center justify-space-between">
        <span>Articles</span>
        <v-btn color="primary" @click="dialog = true">
          Nouvel Article
        </v-btn>
      </v-card-title>

      <v-card-text>
        <v-table>
          <thead>
            <tr>
              <th>Nom</th>
              <th>Prix</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="article in articles" :key="article.id">
              <td>{{ article.nom }}</td>
              <td>{{ formatPrice(article.prix) }} FCFA</td>
              <td>
                <v-btn
                  icon="mdi-pencil"
                  size="small"
                  color="primary"
                  class="mr-2"
                  @click="editItem(article)"
                ></v-btn>
                <v-btn
                  icon="mdi-delete"
                  size="small"
                  color="error"
                  @click="deleteItem(article)"
                ></v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card-text>
    </v-card>

    <!-- Create/Edit Dialog -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          {{ editedIndex === -1 ? 'Nouvel Article' : 'Modifier Article' }}
        </v-card-title>

        <v-card-text>
          <v-form @submit.prevent="save">
            <v-text-field
              v-model="form.nom"
              label="Nom"
              required
              :error-messages="form.errors.nom"
            ></v-text-field>

            <v-text-field
              v-model.number="form.prix"
              label="Prix"
              type="number"
              min="0"
              required
              :error-messages="form.errors.prix"
            ></v-text-field>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" text @click="close">Annuler</v-btn>
              <v-btn color="primary" type="submit" :loading="form.processing">
                Enregistrer
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="dialogDelete" max-width="500px">
      <v-card>
        <v-card-title>Êtes-vous sûr de vouloir supprimer cet article ?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="error" text @click="closeDelete">Non</v-btn>
          <v-btn color="primary" text @click="deleteItemConfirm">Oui</v-btn>
          <v-spacer></v-spacer>
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
  articles: Array
})

const dialog = ref(false)
const dialogDelete = ref(false)
const editedIndex = ref(-1)
const editedItem = ref(null)

const defaultForm = {
  nom: '',
  prix: 0
}

const form = useForm(defaultForm)

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price)
}

const editItem = (item) => {
  editedIndex.value = props.articles.indexOf(item)
  editedItem.value = item
  form.nom = item.nom
  form.prix = item.prix
  dialog.value = true
}

const deleteItem = (item) => {
  editedIndex.value = props.articles.indexOf(item)
  editedItem.value = item
  dialogDelete.value = true
}

const deleteItemConfirm = () => {
  form.delete(route('articles.destroy', editedItem.value.id), {
    onSuccess: () => {
      closeDelete()
    }
  })
}

const close = () => {
  dialog.value = false
  form.reset()
  form.clearErrors()
  editedIndex.value = -1
}

const closeDelete = () => {
  dialogDelete.value = false
  editedIndex.value = -1
}

const save = () => {
  if (editedIndex.value > -1) {
    form.put(route('articles.update', editedItem.value.id), {
      onSuccess: () => {
        close()
      }
    })
  } else {
    form.post(route('articles.store'), {
      onSuccess: () => {
        close()
      }
    })
  }
}
</script> 