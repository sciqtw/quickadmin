<template>
  <slot></slot>
  <div class="modal-teleport" v-if="clientMounted">
    <teleport to="body">
      <transition-group name="modal-fade">
        <template   v-for="(modal) in modals">
          <template v-if="modal.component == 'modal-drawer'">
            <modal-drawer
              :is="modal.component"
              :key="modal.id"
              :id="modal.id"
              v-if="modal.show"
              :content="modal.content"
              :params="modal.props"
              :config="modal.config"
              :beforeClose="modal.beforeClose"
            />
          </template>
          <template v-else>
            <modal-dialog
              :is="modal.component"
              :key="modal.id"
              :id="modal.id"
              v-if="modal.show"
              :content="modal.content"
              :params="modal.props"
              :config="modal.config"
              :beforeClose="modal.beforeClose"
            />
          </template>

        </template>

      </transition-group>
    </teleport>
  </div>
</template>

<script setup>
  import ModalDialog from "./modal-dialog"
  import ModalDrawer from "./modal-drawer"
  import {
    defineAsyncComponent,
    triggerRef,
    provide,
    shallowRef,
    defineProps,
    ref,
    onMounted,
  } from 'vue';


  const modals = shallowRef([]);
  const loading = ref(false);
  const clientMounted = ref(false);

  const parseContent = function(data) {
    /**  异步请求内容  **/
    let request = null
    if (typeof data === "string") {
      request = {
        method: 'POST',
        url: data
      }
    } else if(data.url) {
      request = data
    }

    if(request){
      return {
        type:'request',
        data:request,
      }
    }

    /** json渲染   **/

    if(typeof data.component === 'string' && typeof data.render !== 'function'){
      return {
        type:'json',
        data:{
          component:"json-render",
          props:{
            renderData:data
          },
        },
      }
    }

    /*****  component   *****/
    let component = {}
    let props = {}
    if(data.component){
      component = data.component
      if(data.props){
        props = data.props
      }
    }else{
      component = data;
    }


    return {
      type:'component',
      data:{
        component: component,
        props: props
      },
    }
  }

  /**
   * 打开Modal
   */
  async function openModal(content,config) {


    /**
     *  content 类型：
     *  1. 组件
     *  2. json 组件数据
     *  3. 异步请求
     */

    let component = {};
    let props = {}

    const data = parseContent(content);


    return new Promise((resolve,reject) => {

      const pushModal = function(content,props,config,resolve) {

        let component = config?.component || 'modal-dialog';
        if(['quick-dialog','dialog','el-dialog'].includes(component)){
          component = 'modal-dialog'
        }
        if(['quick-drawer','drawer','el-drawer'].includes(component)){
          component = 'modal-drawer'
          if(config.width){
            config.size = config.width
          }
        }
        modals.value.push({
          id: "id_"+ Math.random().toString(20).slice(2),
          show: true,
          component: component,
          content: content,
          props: props,
          beforeClose: config?.beforeClose,
          config: config,
          resolve,
          reject,
        });
        triggerRef(modals);
      }


      let tempComponent = null
      if(data.type === 'component'){
        tempComponent = defineAsyncComponent(() =>
          Promise.resolve(data.data.component,{ttt:'dd'})
        )
        props = data.data.props;
      }else if(data.type === 'json'){
        component = data.data.component;
        props = data.data.props;
      }else{
        loading.value = true
        Quick.api.loading()
         window.Quick.request(data.data).then(response => {
           Quick.api.closeLoading()
          loading.value = false;
          let dialogObj = {}
          if (!response.data.component) {
            dialogObj = { component: 'div', children: response.data }
          } else {
            dialogObj = response.data
          }

          component = "json-render";
          props = {
            renderData:dialogObj
          };
          pushModal(component,props,config,resolve);
        }).catch((error) => {
           loading.value = false;
           reject(error)
        }).finally(() => {
           Quick.api.closeLoading()
         })
        return
      }

      const com = tempComponent ? tempComponent:component;

      pushModal(com,props,config,resolve);


    });
  }

  /**
   * 关闭Modal
   */
  function closeModal(id, data) {

    const index = modals.value.findIndex((x) => x.id === id);
    if (index < 0) return;
    const modal = modals.value[index];
    setTimeout(() => {
      modals.value[index].show = false;
      // modals.value.splice(index, 1);
      triggerRef(modals);
    },3000)
    Quick.api._close(id)

    if(typeof modal.beforeClose !== 'function'){
      modal?.resolve(data?.data);
    }



  }

  function closeAllModal() {
    modals.value.forEach((modal) => {
      modal.resolve();
    });

    modals.value = [];
    triggerRef(modals);
  }

  provide('parentModal',{})
  provide('modal', {
    open: openModal,
    close: closeModal,
    loading,
    closeAll: closeAllModal,
  });

  Quick.api.modalService = {
    open: openModal,
    close: closeModal,
    loading,
    closeAll: closeAllModal,
  }

  defineProps({
    minWidth: {
      type: Number,
    },
  });

  onMounted(() => {
    clientMounted.value = true;
  });
</script>
