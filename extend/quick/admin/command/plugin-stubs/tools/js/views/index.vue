<template>
  <div>
    plugin-test
      <div>
          {{name}}
      </div>
  </div>
</template>

<script>

  import {ref} from 'vue'
  export default {
    setup() {
      const name = ref('hello')
      return {
          name
      }
    }
  }
</script>

<style scoped>

</style>
