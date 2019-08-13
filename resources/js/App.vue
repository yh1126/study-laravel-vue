<template>
  <div>
    <!-- header navigation -->
    <header>
      <!--Navコンポーネントの呼び出し -->
      <Navbar />
    </header>
    <main>
      <div class="container">
        <RouterView />
      </div>
    </main>
      <!--Footerコンポーネントの呼び出し -->
    <Footer />
  </div>
</template>


<script>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'


export default {
  components: {
    Navbar,
    Footer
  },
  computed: {
    errorCode () {
      return this.$router.state.error.code
    }
  },
  watch: {
    errorCode: {
      handler (val) {
        if (val === INTERNAL_SERVER_ERROR) {
          this.$router.push('/500')
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