<template>
  <div class="photo-list">
    <div class="grid">
      <Photo
        class="grid__item"
        v-for="photo in photos"
        :key="photo.id"
        :item="photo"
        @like="onLikeClick"
      />
    </div>
    <Pagination :current-page="currentPage" :last-page="lastPage" />
  </div>
</template>

<script>
import { OK } from '../util'
import Photo from '../components/Photo.vue'
import Pagination from '../components/Pagination.vue'

export default {
  components: {
    Photo,
    Pagination
  },
  data () {
    return {
      photos: [],
      currentPage: 0,
      lastPage: 0
    }
  },
  methods: {
    async fetchPhotos () {
      const response = await axios.get(`/api/photos/?page=${this.page}`)

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
      }

      this.photos = response.data.data
      this.currentPage = response.data.current_page
      this.lastPage = response.data.last_page
    },
    onLikeClick ({ id, liked }) {
      if (! this.$store.getters['auth/check']) {
        alert('いいね機能を使うにはログインしてください。')
        return false
      }

      // すでにいいねされている場合はいいねを削除する
      if (liked) {
        this.unlike(id)
      } else {
        this.like(id)
      }
    },
    async like (id) {
      const response = await axios.put(`/api/photos/${id}/like`)

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      // いいねされた写真のみcountを追加し、いいねされている状態に変更する
      this.photos = this.photos.map(photo => {
        if (photo.id === response.data.photo_id) {
          photo.likes_count += 1
          photo.liked_by_user = true
        }
        return photo
      })
    },
    async unlike (id) {
      const response = await axios.delete(`/api/photos/${id}/like`)

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.photos = this.photos.map(photo => {
        if (photo.id === response.data.photo_id) {
          photo.likes_count -= 1
          photo.liked_by_user = false
        }
        return photo
      })
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchPhotos()
      },
      immediate: true //コンポーネントが生成されたタイミングでも実行するようにする
    }
  }
}
</script>