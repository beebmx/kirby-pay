<template>
    <div class="kp-tag-text k-button kp-cursor-default">
        <k-tag>{{ text }}</k-tag>
    </div>
</template>

<script>
  export default {
    props: {
      text: String
    }
  }
</script>

<style scoped>
    .kp-tag-text {
        cursor: default;
    }
    .kp-tag-text .k-tag {
        cursor: default;
        padding: 0.4rem 0;
    }
</style>