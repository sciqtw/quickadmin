<template>
  <el-dialog
    ref="dialog"
    custom-class="quick-dialog"
    :beforeClose="beforeClose"
    :key="key"
    :width="width"
    :draggable="true"
    :fullscreen="fullscreen"
  >


    <template #title>
      <el-row justify="space-between">
        <el-col :span="12">{{ title ? title:'title' }}</el-col>
        <el-col :span="12" style="text-align: right;" class="right-tools">
          <svg-icon v-if="!fullscreen" icon="icon-fullscreen" @click="fullScreen"></svg-icon>
          <svg-icon v-else icon="icon-fullscreen-exit" @click="fullScreen"></svg-icon>
          <!--          <svg-icon icon="el-icon-Close" @click="closes"/>-->

        </el-col>
      </el-row>

    </template>
    <el-scrollbar :max-height="maxHeight">
      <div class="dialog-body" :style="bodyStyles">
        <slot/>
      </div>
    </el-scrollbar>
    <template v-slot:footer>
      <slot name="footer"/>
    </template>
  </el-dialog>
</template>

<script>
  // import elDragDialog from '@/directive/el-drag-dialog'; // base on element-ui
  import {ref, onMounted,} from 'vue';
  import ElDialog from "element-plus"
  import {useDraggable} from '@vueuse/core'

  export default {
    name: 'QuickDialog',
    extends: {
      ElDialog
    },
    props: {
      beforeClose: {
        type: Function,
      },
      height: {
        type: [String, Number],
        default: ''
      },
      maxHeight: {
        type: [String, Number],
        default: '600px'
      },
      title: {
        type: String,
        default: ''
      },
      width: {
        type: String,
        default: '700px'
      },
    },
    data() {
      return {
        fullscreen: false,
        dialogVisible: true,
        callback: '',
        id: 0,
        key: 1
      };
    },
    computed: {
      bodyStyles() {
        const style = {};
        if (this.height) {
          style['height'] = `${this.height}`;
        }

        if (this.maxHeight) {
          style['max-height'] = `${this.maxHeight}`;
        }
        return style;
      },


    },
    setup(props, {slots}) {
      const el = ref(null)
      const dialog = ref(null)
      const innerWidth = window.innerWidth

      onMounted(() => {

      })

      return {
        el,
        dialog,
      }
    },
    methods: {
      closes() {
        this.modelValue = false
      },
      /**
       * 全屏展示
       */
      fullScreen() {
        this.fullscreen = !this.fullscreen;
      }
    }
  };

</script>

<style lang="scss" scoped>
  .right-tools {
    padding-right: 35px;
  }

  .right-tools .svg-icon:hover {
    color: #409EFF;
    cursor: pointer;
  }


  .dialog-body {
    overflow-y: auto;
  }

</style>
