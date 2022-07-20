<template>
  <el-popover  @show="handleShow" >
    <slot>
      <json-render  v-if="contentData.component && !loading" :render-data="contentData" />
      <div  v-if="!contentData.component && !loading">{{contentData}}</div>
      <div style="text-align: center;" v-if="loading" >
        <el-button type="text" :loading="loading">Loading</el-button>
      </div>
    </slot>
    <template #reference>
      <span>
        <slot name="reference"></slot>
      </span>
    </template>
  </el-popover>
</template>

<script>

  import {ref, inject,  provide,onMounted,watch} from 'vue';
  import ElPopover from "element-plus"
  import axios from '../utils/request'
  export default {
    name: 'QuickPopover',
    extends: {
      ElPopover
    },
    props: {
      content: {
        type: [String, Object],
        default: ''
      },
      request:{
        type: [String, Object],
        default: ''
      },
      visible:{
        type:Boolean,
        default:false,
      }
    },
    setup(props, {slots,emit}) {

      const contentData = ref('')
      const loading = ref(false)
      const visibility = ref(false)


      const getContent = function (config) {

        if(loading.value){
          return
        }
        loading.value = true

        axios.request(config)
          .then(res => {
            loading.value = false
            if(!res.code ){
              contentData.value = res.data
            }
          })
          .finally(() => {
            loading.value = false
          })
      }

      watch(props.visible,(val) => {
        visibility.value = val
      })
      watch(visibility,(val) => {
        emit('update:visible',val)
      })

      onMounted(() => {
        contentData.value = props.content
        visibility.value = props.visible
        // console.log('-------popover',props)
      })


      const handleShow = () => {

        if(props.request){
          let requestConfig = {}
          if(typeof props.request === 'string'){
            requestConfig.url = props.request
          }else{
            requestConfig = Object.assign({},requestConfig,props.request)
          }
          getContent(requestConfig)

        }

      }

      const panel =  inject('response');
      const handleResponse = function (event,data) {
          console.log('-popover--handleResponse--------',event,data)
        if(panel){
          panel.emit(event,data)
        }
        emit(event,data)
        visibility.value = false
      }

      provide('response',{
        emit:handleResponse
      })



      return {
        contentData,
        loading,
        visibility,
        handleShow,
        handleResponse
      }
    },

  };

</script>

<style lang="scss" scoped>

</style>
