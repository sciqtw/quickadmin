<template>
  <div class="modal-container">
    <div class="modal-content" :data-id="id">
      <quick-drawer
        @closed="handleClosed"
        v-model="visible"
        v-bind="modalConfig"
      >
        <component :is="content" v-bind="params" v-if="showContent"></component>
        <template #footer>
          <div :id="id"></div>
        </template>

      </quick-drawer>

    </div>
  </div>

</template>

<script setup >
  import {defineProps, computed, ref, inject, getCurrentInstance, reactive, provide} from 'vue';

  const visible = ref(true);
  const showContent = ref(true);
  const {proxy} = getCurrentInstance()
  const modal = inject('modal');
  const props = defineProps({
    content: {
      type: Object,
      require: true,
    },
    params: {
      type: Object,
      default: () => {
      },
    },
    config: {
      type: Object,
      default: () => {
      },
    },
    beforeClose: {
      type: Function,
    },
    id: {
      type: String,
      required: true,
    },
  });


  const modalConfig = computed(() => {

    return Object.assign({},{
      'lock-scroll':false,
      'close-on-click-modal':false,
    }, props.config, {
      beforeClose: handleBeforeClose
    })
  })

  const result = reactive({type: 'close', data: {}});

  /**
   * 关闭按钮
   */
  const handleBeforeClose = (done) => {

    handleClose("close", {}, done)
  }


  /**
   * 关闭后移除dom
   */
  function handleClosed() {
    showContent.value = false
    modal.close(props.id, result);
  }


  const handleClose = (type, data, done) => {
    if (props.beforeClose) {
      props.beforeClose(type, data, done)
      return true;
    }

    result.type = type;
    result.data = data;
    done()
  }


  /**
   * 关闭
   * @param type
   * @param data
   */
  const close = (type, data) => {

    handleClose(type, data, () => {
      visible.value = false;
    })

  }



  provide("parentModal", {
    modalId: props.id,
    id: props.id,
    close: close

  })

  Quick.api.pushDialog({
    modalId: props.id,
    id: props.id,
    close: close
  })

</script>

<style lang="scss" scoped>

</style>
