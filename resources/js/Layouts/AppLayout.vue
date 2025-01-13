<template>
  <v-app>
    <v-app-bar color="primary" app>
      <v-app-bar-title>Bayal POS</v-app-bar-title>
      <v-spacer></v-spacer>
      <Link :href="route('home')" class="v-btn v-btn--text text-white text-decoration-none mx-2">
        Ventes
      </Link>
      <Link :href="route('articles.index')" class="v-btn v-btn--text text-white text-decoration-none mx-2">
        Articles
      </Link>
      <Link :href="route('facture-achats.index')" class="v-btn v-btn--text text-white text-decoration-none mx-2">
        Factures
      </Link>
      <Link :href="route('fournisseurs.index')" class="v-btn v-btn--text text-white text-decoration-none mx-2">
        Fournisseurs
      </Link>
    </v-app-bar>

    <v-main>
      <v-container>
        <slot></slot>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const route = window.route
</script> 