<template>
  <div >
    <el-tag class="qk-tag" v-for="(tag,index) in tags" :key="index" v-bind="attributes" :type="tag.type">{{ tag.value }}</el-tag>
  </div>
</template>

<script>
export default {
  name: 'TagField',
  props: {
    tags:{
      type:Array,
      default:() =>[]
    },
    tagProps:{
      type:Object,
      default:() => {}
    },
    value:{
      type:String,
      default:''
    },
  },
  computed: {

    attributes() {

      return {
        ...this.tagProps,
      }
    },
    /**
     * has value
     * @returns {boolean}
     */
    hasValue() {
      return this.field.value !== null
    }
  }
}
</script>
<style scoped>
  .qk-tag{
    margin-right: 6px;
    margin-bottom: 6px;
  }
  .index-icon{
    /*text-align: center;*/
  }
</style>
