import { createApp } from 'vue'
import { createStore } from 'vuex'
import App from './App.vue'
import axios from 'axios'
import VueAxios from 'vue-axios'



createApp(App).use(VueAxios, axios).use(store).mount('#app')
