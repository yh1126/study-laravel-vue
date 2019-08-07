<template>
  <nav class="navbar">
    <RouterLink class="bavbar_brand" to="/">
      Vuesplash
    </RouterLink>
    <div class="bavbar_menu">
        <div v-if="isLogin" class="navbar_item">
            <button class="button">
                <i class="icon ion-md-add"></i>
                Submit a photo
            </button>
        </div>

        <span v-if="isLogin" class="navbar_item">
          {{ username }}
        </span>
        <div v-else class="navbar_item">
          <RouterLink class="button button--link" to="/login">
            Login / Register
          </RouterLink>
        </div>
    </div>
  </nav>
</template>

<script>
export default {
  computed: {
    isLogin () {
      return this.$store.getters['auth/check']
    },
    usernme () {
      return this.$store.getters['auth/username']
    }
  }
}
</script>
