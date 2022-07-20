<template>
  <el-popconfirm  @confirm="confirm" @cancel="cancel">
    <template #reference>
      <span>
        <slot name="reference"></slot>
      </span>
    </template>
  </el-popconfirm>
</template>

<script>

  import {defineComponent, ref, reactive, computed, onMounted, inject, provide} from 'vue';
  import ElPopConfirm from "element-plus"
  import axios from '../utils/request'
  export default {
    name: 'QuickPopover',
    extends: {
      ElPopConfirm
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
    },
    setup(props, {slots,emit}) {

      const contentData = ref('')
      const loading = ref(false)
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
      const confirm = function () {
        handleResponse('confirm')
      }

      const cancel = function () {
        handleResponse('cancel')
      }

      const handleResponse = function (event) {
          emit(event,{})
          if(panel){
            panel.emit(event,{})
          }
      }




      return {
        contentData,
        loading,
        handleShow,
        handleResponse,
        cancel,
        confirm,

      }
    },

  };

</script>

<style lang="scss" scoped>

</style>
