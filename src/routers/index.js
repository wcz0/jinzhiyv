import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      componet: ()=> import('@/pages/Index/Index.vue')
    },
    {
      path: '/login',
      componet: ()=> import('@/pages/Login/Index.vue')
    },
    {
      path: '/:pathMatch(.*)',
      componet: ()=> import('@/pages/Error/404.vue')
    }
  ]
})
