<template>
  <span v-html="html" />
</template>

<script>

export default {
  name: 'ShowHtml',
  props: {
    html: {
      type: String,
      default: ''
    }
  }
}
</script>

<style scoped>

</style>
