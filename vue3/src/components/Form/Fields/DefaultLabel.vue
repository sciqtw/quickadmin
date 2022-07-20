<template>
  <span>
    <span>{{ label }}</span>
    <el-tooltip :content="content" v-bind="attributes">
      <el-icon :class="icon" />
    </el-tooltip>
  </span>

</template>

<script>

export default {

  props: {
    field: {},
    label: {
      type: String,
      default: ''
    },
    content: {
      type: String,
      default: ''
    }
  },

  computed: {

    icon() {
      return  'el-icon-info'
    },
    /**
         * 默认属性
         * */
    defaultAttributes() {
      return {
        effect: 'dark',
        placement: 'top',
        content: ''
      }
    },
    /**
         * 扩展属性
         * */
    attributes() {
      // const attrs = this.field.label.attributes

      return {
        ...this.defaultAttributes
      }
    }
  }
}
</script>
