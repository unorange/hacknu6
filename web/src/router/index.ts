import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import DocumentView from '../views/DocumentView.vue'
import CourierView from '../views/CourierView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/order-document/:id',
      name: 'document',
      component: DocumentView
    },
    {
      path: '/courier',
      name: 'courier',
      component: CourierView
    }
  ]
})

export default router
