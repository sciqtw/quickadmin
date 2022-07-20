<template>
  <div class="page">
    <page-header  v-if="showHeader">
      <template #title>
        <slot name="title">
          <div class="header-title">{{ title }} </div>
        </slot>
      </template>
      <template #content>
        <slot name="description">
          <div class="header-content">{{ description }}</div>
        </slot>
        <slot name="tools"> </slot>
      </template>

    </page-header>
    <page-main>
      <slot />
    </page-main>
  </div>
</template>

<script>

export default {
  name: 'QuickPage',
  props: {
    title: String,
    description: String,
    showHeader: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      activeName: ''
    }
  },
  computed: {},
  watch: {},
  async created() {

  },
  methods: {}
}
</script>

<style scoped lang="scss">

  .header-content{
    padding-bottom: 15px;
  }


</style>
