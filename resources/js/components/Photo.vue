<template>
  <div class="photo">
    <figure class="photo__wrapper">
      <img
        class="photo__image"
        :class="imageClass"
        :src="item.url"
        :alt="`Photo by ${item.owner.name}`"
        @load="setAspectRatio"
        ref="image"
      >
    </figure>
    <RouterLink
      class="photo__overlay"
      :to="`/photos/${item.id}`"
      :title="`View the photo by ${item.owner.name}`"
    >
      <div class="photo__controls">
        <button
          class="photo__action photo__action--like"
          :class="{ 'photo__action--liked': item.liked_by_user }"
          title="Like Photo"
          @click.prevent="like"
        >
          <i class="icon ion-md-heart"></i>{{ item.likes_count }}
        </button>
        <!-- サーバに直接リクエストを送るためaタグを設定 -->
        <a
          class="photo__action"
          title="Download photo"
          @click.stop
          :href="`/photos/${item.id}/download`"
        >
          <i class="icon icon-md-arrow-round-down"></i>Download
        </a>
      </div>
      <div class="photo__username">
        {{ item.owner.name }}
      </div>
    </RouterLink>
  </div>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      landscape: false,
      portrait: false
    }
  },
  computed: {
    imageClass () {
      return {
        // 横長クラス
        'photo__image--landscape': this.landscape,
        // 縦長クラス
        'photo__image--portrait': this.portrait
      }
    }
  },
  methods: {
    setAspectRatio () {
      if (! this.$refs.image) {
        return false
      }

      const height = this.$refs.image.clientHeight
      const width = this.$refs.image.clientWidth
      // 縦横長比率 3:4 よりも横長のがzぴ
      this.landscape = height / width <= 0.75
      // 横長でなければ縦長
      this.portrait = ! this.landscape
    },
    like () {
      // emitの第一引数はイベントの名前, 第二引数はイベントハンドラに渡す引数
      this.$emit('like', {
        id: this.item.id,
        liked: this.item.liked_by_user
      })
    }
  },
  watch: {
    $route () {
      // ページが切り替わってから画像が読み込まれるまでの間に
      // 前のページの同じ位置にあった画像の表示が残ってしまうことを防ぐ
      this.landscape = false
      this.portrait = false
    }
  }
}
</script>