<template>
  <div v-show="value" class="photo-form">
    <h2 class="title">Submit a Photo</h2>
    <form class="form" @submit.prevent="submit">
      <input class="form__item" type="file" @change="onFileChange">
      <output class="form__output" v-if="preview">
        <img :src="preview" alt="">
      </output>
      <div class="form__button">
        <button type="submit" class="button button--inverse">submit</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    value: {
      //form表示の制御を行う
      type: Boolean,
      required: true
    }
  },
  data () {
    return {
      preview: null
    }
  },
  methods: {
    // フォームでファイルが選択されたら実行される
    onFileChange (event) {
      // 何も選択されていなかったら処理中断
      if (event.target.files.length === 0) {
        this.reset()
        return false
      }

      // ファイルが画像ではなかったら処理中断
      if (! event.target.files[0].type.match('image.*')) {
        this.reset()
        return false;
      }

      // FileReaderクラスのインスタンを取得
      const reader = new FileReader()

      // ファイルを読み込み終わったタイミングで実行する処理
      reader.onload = e => {
        // previewに読み込み結果(データURL)を代入する
        // previewに値が入ると<output>煮付けたv-ifがtrueと判定される
        // また<ouput>内部の<img>のsrc属性はpreviewの値を参照しているので
        // 結果として画像が表示される
        this.preview = e.target.result
      }

      // ファイルを読み込む
      // 読み込まれたファイルはデータURL形式で受け取れる(上記onload参照)
      reader.readAsDataURL(event.target.files[0])
      this.photo = event.target.files[0]
    },
    // 入力欄の値とプレビュー表示をクリアするメソッド
    reset () {
      this.preview = ''
      this.photo = null
      // this.$elはコンポーネントそのもののDOM要素を指す
      this.$el.querySelector('input[type="file"]').value = null
    },
    async submit () {
      const formData = new FormData()
      formData.append('photo', this.photo)
      const response = await axios.post('/api/photos', formData)

      this.reset()
      this.$emit('input', false)
    }
  }
};
</script>