<template>
  <div class="loader">
    <p class="loading__text">
      <slot>loading...</slot>
    </p>
    <div class="loader__item loader__item--heart">
      <div></div>
    </div>
  </div>
</template>