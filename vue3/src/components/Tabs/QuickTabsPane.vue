<template>
  <div></div>
</template>
<script>
export default {
  name: 'QuickTabsPane',
  props: {
    removeBottom: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      activeName: ''
    }
  },
  computed: {
    defaultAttrs() {
      return {
        ...this.$attrs
      }
    },

    attrs() {
      return {
        ...this.defaultAttrs
      }
    }
  },
  methods: {

  }
}
</script>

<style lang="scss" scoped>

  .tab-mb-0{
    :deep .el-tabs__header{
      margin-bottom: 0px;
    }
    :deep .el-tabs__header{
      width: 100%;
      margin-right:0px;
      margin-left:0px;
    }
    :deep.el-tabs__active-bar.is-left{
      width: 3px;
    }
  }
</style>
