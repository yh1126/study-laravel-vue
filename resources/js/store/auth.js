import Axios from "axios";

const state = {
  user: null
}

const getters = {}

const mutations = {
  // ミューテーションの第一引数は必ずステート
  setUser (state, user) {
    state.user = user
  }
}

const actions = {
  // アクションの第一引数は必ずcontext
  async register (context, data) {
    const response = await axios.post('/api/register', data)
    // ミューテーションを呼び出すメソッド = commit
    context.commit('setUser', response.data)
  },
  async login (context, data) {
    const response = await axios.post('/api/login', data)
    console.log(response)
    context.commit('setUser', response.data)
  },
  async logout (context, data) {
    const response = await axios.post('/api/logout', data)
    context.commit('setUser', response.null)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}