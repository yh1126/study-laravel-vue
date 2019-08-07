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
  }
}
</script>