<template>
  <div @click="openDialog" class="quick-attachment">
    <el-dialog
      title="选择文件"
      width="70%"
      v-model="dialogVisible"
      @closed="handleClose"
      class="attachment-dialog"
    >
      <attachment
        v-if="dialogVisible"
        @select="handleSelect"
        v-bind="attachmentProps"></attachment>
      <template #footer>
          <span class="dialog-footer">
            <el-button @click="dialogVisible = false">取消</el-button>
            <el-button type="primary" @click="confirm"
            >确定</el-button
            >
          </span>
      </template>

    </el-dialog>
    <slot></slot>
  </div>
</template>

<script>
  import attachment from "./attachment"
  import {ref, watch,computed} from 'vue'

  export default {
    components: {
      attachment
    },
    props: {
      action: {
        type: String,
        required: true
      },
      headers: {
        type: Object,
        default: () => {
        }
      },
      data: {
        type: Object,
        default: () => {
        }
      },
      name: {
        type: String,
        default: 'file'
      },
      multiple: {
        type: Boolean,
        default: true,
      },
      size: {
        type: Number,
        default: 2
      },
      ext: {
        type: Array,
        default: () => ['jpg', 'png', 'gif', 'bmp']
      },
      max: {
        type: Number,
        default: 10
      },
      moduleName:{
        type: String,
        default: 'admin',
      },
    },
    setup(props, {emit}) {

      const dialogVisible = ref(false)
      const openDialog = function () {
        dialogVisible.value = true;
      }
      const confirm = function () {
        dialogVisible.value = false;
        emit('select', selectAttachment.value)
      }
      const selectAttachment = ref([])
      const handleSelect = function (data) {
        selectAttachment.value = data
      }
      const handleClose = function () {
        selectAttachment.value = []
      }

      const attachmentProps = computed(() => {
        return {
          ...props
        }
      })
      return {
        dialogVisible,
        selectAttachment,
        attachmentProps,
        handleSelect,
        handleClose,
        openDialog,
        confirm,
      }
    }
  }
</script>
<style lang="scss" scoped>


  .quick-attachment {
    text-align:left;
    :deep .el-dialog__body {
      padding-bottom: 5px;
    }
  }


</style>
