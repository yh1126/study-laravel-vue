<template>
  <div class="container--small">
    <ul class="tab">
      <!-- @clickでclickされた時の挙動を設定できる -->
      <li
        class="tab__item"
        :class="{'tab__item--active': tab === 1 }"
        @click="tab = 1"
      >Login</li>
      <li
        class="tab__item"
        :class="{'tab__item--active': tab === 2 }"
        @click="tab = 2"
      >Register</li>
    </ul>
    <!-- tabという変数の値によって表示する内容を切り替える -->
    <div class="panel" v-show="tab === 1">Login Form</div>
    <div class="panel" v-show="tab === 2">Register Form</div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      tab: 1
    }
  }
}
</script>
