<template>
  <div>{{item.value}} {{item.lable}}</div>
</template>

<script>

export default {
  name: 'ShowHtml',
  props: {
    html: {
      type: String,
      default: ''
    },
    item:{
      type:Object,
      default:() => {}
    }
  },
  setup(props){

  },
}
</script>

<style scoped>

</style>
