import { createStore } from 'vuex'

  let siv = localStorage.getItem('siv')
  let stoken = localStorage.getItem('stoken')

export default createStore({
  state: {
    siv: siv ?? null,
    stoken: stoken ?? null,
  },
  mutations: {
    setSiv(state, siv) {
      state.siv = siv
    },
    setStoken(state, stoken) {
      state.stoken = stoken
    }
  },
})
