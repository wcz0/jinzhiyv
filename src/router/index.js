import { createRouter, createWebHashHistory, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(),
  routes: [
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
  ],
})

router.beforeEach((to, from, next) => {
  if (to.path === '/login') {
    next()
  }
  let siv = localStorage.getItem('siv')
  let stoken = localStorage.getItem('stoken')
  if(siv && stoken){
    next()
  }
  next('/login')
})

export default router