<template>
  <div>
    <slot name="prefix"/>
    <slot>
      <json-render v-if="value?.component" :render-data="value"></json-render>
      <template v-else>
        {{value}}
      </template>
    </slot>
    <slot  name="suffix"/>
  </div>

</template>

<script>
  export default {
    name: 'DefaultField',
    props: {
      container: {
        type: [Object, String],
        default: () => {
        }
      },
      value:{
        type: [Object, String],
        default: ''
      }
    },
    setup(props,{slot}){

    },
  }
</script>
<style lang="scss" scoped>


</style>
