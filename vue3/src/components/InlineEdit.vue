<template>
  <div class="inline-edit">
    <json-render v-if="editIng" :render-data="editContent" @response="handleResponse"></json-render>
    <div @click="editIng = true" v-else>
      <slot></slot>
    </div>
  </div>

</template>

<script>
  import {watch, ref, provide, computed, onMounted, inject} from 'vue';

  export default {
    name: 'InlineEdit',

    props: {
      content: {
        type: [String, Object],
        default: ''
      },
      edit: {
        type: Boolean,
        default: false,
      }
    },
    setup(props, {slots, emit}) {

      const editIng = ref(false);

      watch(props.edit, (value) => {
        editIng.value = value
      })

      onMounted(() => {
        editIng.value = props.edit
      });


      const editContent = computed(() => {
        if(props.content && props.content.component){
          return Object.assign({}, props.content)
        }
        return {
          component:'span',
          children:props.content
        }

      })


      const panel = inject('response');
      const handleResponse = function (event, data) {
        console.log('-inlineedit--handleResponse--------', event, data)
        if (panel) {
          panel.emit(event, data)
        }
        emit(event, data)
        editIng.value = false
        emit('update:edit', editIng.value)
      }

      provide('response', {
        emit: handleResponse
      })

      return {
        handleResponse,
        editContent,
        editIng
      }
    },

  };

</script>

<style lang="scss" scoped>
  .inline-edit{
    :deep .el-form-item{
      margin-bottom: 0 !important;
    }
  }

</style>
