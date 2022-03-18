import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

const template

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      componet: ()=> import('@/pages/index.vue')
    },
    {
      path: '/login',
      componet: ()=> import('@/pages/login.vue')
    },
    {
      path: '/:pathMatch(.*)',
      componet: 
    }
  ]
})
