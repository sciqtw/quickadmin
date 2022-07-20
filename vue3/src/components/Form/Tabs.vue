<template>

  <el-tabs
    v-model="value"
  >
    <slot name="item" />
  </el-tabs>
</template>

<script>
export default {
  name: 'Tabs',
  props: {

    title: {
      type: String,
      default: 'Tabs22'
    }

  },
  data() {
    return {
      activeName: 'd',
      value: 'f',
      showHeader: true
    }
  },
  computed: {

    tabsWithFields() {
      return _.map(this.tabs, tab => {
        return {
          name: tab.name,
          attributes: tab.attributes,
          fields: _.filter(this.fields, item => item.tab == tab.name)
        }
      })
    },

    /**
              * 默认属性
              * */
    defaultAttributes() {
      return {
        type: this.field.type || 'text',
        placeholder: this.field.placeholder
      }
    },
    /**
              * 扩展属性
              * */
    attributes() {
      const attrs = this.field.attributes

      return {
        ...this.defaultAttributes,
        ...attrs
      }
    }
  },
  methods: {
    change(e) {
      console.log(e)
    }
  }
}

</script>

<style scoped >
    .box-card{
        margin-bottom: 20px;
    }
</style>
