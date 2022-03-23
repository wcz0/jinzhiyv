import { createRouter, createWebHashHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    component: () => import('@/pages/index/Index.vue'),
  },
  {
    path: '/login',
    component: () => import('@/pages/login/Index.vue'),
  },
  {
    path: '/:pathMatch(.*)*',
    component: () => import('@/pages/error/404.vue'),
  },
]

export default createRouter({
  history: createWebHashHistory(),
  routes,
})
