import Axios from "axios";

const state = {
  user: null
}

const getters = {
  // !!を二つ使うときにundefindやnullのときにbooleanを返すようにする
  check: state => !! state.user,
  username: state => state.user ? state.user.name : ''
}

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
  },
  async currentUser (context, data) {
    const response = await axios.get('/api/user')
    const user = response.data || null
    context.commit('setUser', user)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}