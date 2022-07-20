<template>
  <el-descriptions>
    <template v-for="(item,i) in items" :key="i">
      <component :is="item.component" v-bind="item.props">
        {{item.children}}
      </component>
<!--      <json-render   :render-data="item" />-->
    </template>
  </el-descriptions>
</template>

<script>

  import {ref, onMounted,} from 'vue';
  import ElDescriptions from "element-plus"
  export default {
    name: 'QkDescriptions',
    extends: {
      ElDescriptions
    },
    props: {
      items: {
        type: Array,
        default: () => []
      },
    },
    setup(props, {emit, slots}) {
      onMounted(() => {
        // console.log('dfdfd',props.items)
      })
      return {}
    }
  }
</script>

<style scoped>

</style>
