<template>
  <div v-show="value" class="photo-form">
    <h2 class="title">Submit a Photo</h2>
    <form class="form">
      <input class="form__item" type="file" />
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
  }
};
</script>