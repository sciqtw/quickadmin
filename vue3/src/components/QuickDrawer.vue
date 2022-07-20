<template>
  <el-drawer
    :title="title"
    custom-class="quick-dialog"
    :beforeClose="beforeClose"
  >
    <div class="dialog-body" :style="bodyStyles">
      <slot/>
    </div>

  </el-drawer>
</template>

<script>

  import { onMounted} from 'vue';
  import ElDrawer from "element-plus"

  export default {
    name: 'QuickDrawer',
    extends: {
      ElDrawer
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
        default: '650px'
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


      onMounted(() => {

      })

      return {
      }
    },
    methods: {

    }
  };

</script>

<style lang="scss" scoped>
  .right-tools .svg-icon:hover {
    color: #409EFF;
    cursor: pointer;
  }

  .quick-dialog {
    :deep .el-dialog__body {
      padding: 0;
    }
  }

  .dialog-body {
    overflow-y: auto;
  }

</style>
