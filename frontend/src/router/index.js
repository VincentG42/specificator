import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/questionnaire/:id',
    name: 'Questionnaire',
    component: () => import('../views/Questionnaire.vue')
  },
  {
    path: '/export/:id',
    name: 'Export',
    component: () => import('../views/Export.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
