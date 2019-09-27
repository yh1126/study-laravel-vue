<template>
  <div>
    <!-- header navigation -->
    <header>
      <!--Navコンポーネントの呼び出し -->
      <Navbar />
    </header>
    <main>
      <div class="container">
        <Message />
        <RouterView />
      </div>
    </main>
      <!--Footerコンポーネントの呼び出し -->
    <Footer />
  </div>
</template>


<script>
import Message from './components/Message.vue'
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import { UNAUTHORIZED, INTERNAL_SERVER_ERROR, NOT_FOUND } from './util'

export default {
  components: {
    Message,
    Navbar,
    Footer
  },
  computed: {
    errorCode () {
      return this.$store.state.error.code
    }
  },
  watch: {
    errorCode: {
      async handler (val) {
        if (val === INTERNAL_SERVER_ERROR) {
          this.$router.push('/500')
        } else if (val === UNAUTHORIZED) {
          // トークンをリフレッシュ
          await axios.get('/api/refresh-token')
          // ストアのuserをクリア
          this.$store.commit('auth/setUser', null)
          // ログイン画面へ
          this.$router.push('/login')
        } else if (val === NOT_FOUND) {
          thi$router.push('/not-found')
        }
      },
      immediate: true //handlerをすぐに実行する
    },
    $route () {
      this.$store.commit('error/setCode', null)
    }
  }
}
</script>