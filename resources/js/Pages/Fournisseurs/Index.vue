<template>
  <AppLayout>
    <v-card>
      <v-card-title class="d-flex align-center">
        <span>Fournisseurs</span>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="openDialog()">
          Nouveau Fournisseur
        </v-btn>
      </v-card-title>

      <v-card-text>
        <v-table>
          <thead>
            <tr>
              <th>Nom</th>
              <th>Téléphone</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="fournisseur in fournisseurs" :key="fournisseur.id">
              <td>{{ fournisseur.nom }}</td>
              <td>{{ fournisseur.telephone }}</td>
              <td>
                <v-btn
                  icon="mdi-pencil"
                  variant="text"
                  color="primary"
                  size="small"
                  class="mr-2"
                  @click="openDialog(fournisseur)"
                ></v-btn>
                <v-btn
                  icon="mdi-delete"
                  variant="text"
                  color="error"
                  size="small"
                  @click="deleteFournisseur(fournisseur.id)"
                ></v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card-text>
    </v-card>

    <!-- Fournisseur Dialog -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          {{ editedId ? 'Modifier le Fournisseur' : 'Nouveau Fournisseur' }}
        </v-card-title>
        <v-card-text>
          <v-form @submit.prevent="submitFournisseur">
            <v-text-field
              v-model="form.nom"
              label="Nom"
              required
            ></v-text-field>

            <v-text-field
              v-model="form.telephone"
              label="Téléphone"
              required
            ></v-text-field>

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
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  fournisseurs: Array
})

const dialog = ref(false)
const editedId = ref(null)

const form = useForm({
  nom: '',
  telephone: ''
})

const openDialog = (fournisseur = null) => {
  if (fournisseur) {
    editedId.value = fournisseur.id
    form.nom = fournisseur.nom
    form.telephone = fournisseur.telephone
  } else {
    editedId.value = null
    form.reset()
  }
  dialog.value = true
}

const submitFournisseur = () => {
  if (editedId.value) {
    form.put(route('fournisseurs.update', editedId.value), {
      onSuccess: () => {
        dialog.value = false
        form.reset()
      }
    })
  } else {
    form.post(route('fournisseurs.store'), {
      onSuccess: () => {
        dialog.value = false
        form.reset()
      }
    })
  }
}

const deleteFournisseur = (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')) {
    router.delete(route('fournisseurs.destroy', id))
  }
}
</script> 