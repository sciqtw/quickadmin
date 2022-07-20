<template>
  <json-render
    v-if="renderComponent"
    @click="handleAction"
    :disabled="disabled"
    :render-data="renderComponent"/>
  <span @click="handleAction" v-else>
      <slot>{{display || 'button'}}</slot>
  </span>


</template>

<script>

  import {useAction} from "../utils/handleAction";
  import {defineComponent, ref, provide, computed, reactive} from 'vue';

  export default {
    name: 'QuickAction',
    props: {
      action: {
        type: Object,
        default: () => {
        }
      },
      display: {
        type: Object,
        default: () => {
        }
      },
      params: {
        type: Object,
        default: () => {
        }
      },
      data: {
        default: () => []
      },
      disabled: {
        type: Boolean,
        default: false
      },
      beforeClose: {
        type: Function,
      },
    },
    setup(props, {emit, slots}) {

      const renderComponent = computed(() => {

        const action = props.action;
        if (action && action.action && ['popover', 'popconfrim'].indexOf(action.action) !== -1 && action.params.config) {

          let display = {
            component: 'span',
            children:props.display || 'action',
            props: {},
            slot: 'reference'
          }
          if (props.display && props.display.component) {
            display = props.display
            display.slot = 'reference'
          }

          return {
            component: action.params.config.component,
            props: {
              ...action.params.config.props,
            },
            children: [
              display
            ]
          }
        }

        if(action && action.action === 'inlineEdit'){
          let display = {
            component: 'span',
            children:props.display || '',
          }
          if (props.display && props.display.component) {
            display = props.display
          }
          return {
            ...action.params.config,
            children: [
              display
            ]
          }
        }


        if (props.display && props.display.component) {
          return Object.assign({}, props.display, {
            // onResponse: handleResponse,
            // onClick: handleAction,
          })
        }
        return false
      });


      const options = reactive({
        params: props.params, // 请求参数
        data: props.data, // post请求数据，
        beforeClose: function (action, data, done) {
          if (props.beforeClose) {
            props.beforeClose(action, data, done)
          } else {
            emit('response', action, data)
            done();
          }
        },
        callback: function (action, data) {
          emit('response', action, data)
        }

      })

      /*** 只能在setup生命周期中执行 ***/
      const {action,setData,setParams} = useAction(options);


      const handleAction = _.debounce(() => {
        if (!props.disabled && props.action) {
          setData(props.data)
          setParams(props.params)
          action(props.action)
        }
      },200)


      const handleResponse = (event, data) => {
        if (event === 'action') {
          action(data)
        } else {
          emit('response', action, data)
        }
      }

      provide('response', {
        emit: handleResponse
      })

      return {
        handleAction,
        renderComponent
      }
    }
  }
</script>

<style scoped>

</style>
