import { createRouter, createWebHistory } from 'vue-router'

import PageNotFound from "@/components/pages/PageNotFound.vue"
import GoogleAuth from '@/components/pages/GoogleAuth.vue'
import GoogleSheets from '@/components/pages/GoogleSheets.vue'

const routes = [
  { path: '/', component: GoogleAuth },
  { path: '/sheets', component: GoogleSheets },
  { path: '/:catchAll(.*)', component: PageNotFound },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router