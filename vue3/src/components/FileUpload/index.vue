<template>
<!--  <el-upload-->
<!--    :show-file-list="false"-->
<!--    :action="action"-->
<!--    :data="data"-->
<!--    :name="name"-->
<!--    :before-upload="beforeUpload"-->
<!--    :on-exceed="onExceed"-->
<!--    :on-success="onSuccess"-->
<!--    :on-remove="handleRemove"-->
<!--    :file-list="url"-->
<!--    :limit="max"-->
<!--    drag-->
<!--  >-->
<!--    <div class="slot">-->
<!--      <div style="width: 200px;">-->
<!--        <el-icon class="el-icon&#45;&#45;upload">-->
<!--          <el-icon-upload-filled/>-->
<!--        </el-icon>-->
<!--        <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>-->
<!--      </div>-->

<!--    </div>-->
<!--    <template #tip>-->
<!--      <div v-if="!notip" class="el-upload__tip">-->
<!--        <div style="display: inline-block;">-->
<!--          <el-alert :title="`上传文件支持 ${ ext.join(' / ') } 格式，单个文件大小不超过 ${ size }MB，且文件数量不超过 ${ max } 个`" type="info"-->
<!--                    show-icon :closable="false"/>-->
<!--        </div>-->
<!--      </div>-->
<!--    </template>-->
<!--  </el-upload>-->

  <el-upload
    :file-list="url"
    class="upload-demo"
    :limit="max"
    :headers="headers"
    :data="data"
    :action="action"
    :name="name"
    :auto-upload="true"
    :before-upload="beforeUpload"
    :on-exceed="onExceed"
    :on-success="onSuccess"
    :on-remove="handleRemove"
    :on-change="handleChange"
  >
    <el-button type="primary">点击上传</el-button>
    <template #tip>
      <div class="el-upload__tip">
        上传文件支持 {{ext.join(' / ')}} 格式，单个文件大小不超过 {{size}}MB，且文件数量不超过 {{max}} 个
      </div>
    </template>
  </el-upload>
</template>

<script setup>

  import {computed, getCurrentInstance, defineEmits, ref} from 'vue'

  const {proxy} = getCurrentInstance()

  const props = defineProps({
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
    size: {
      type: Number,
      default: 2
    },
    max: {
      type: Number,
      default: 1
    },
    url: {
      type: Array,
      default: () => []
    },
    notip: {
      type: Boolean,
      default: false
    },
    ext: {
      type: Array,
      default: () => ['zip', 'rar']
    }
  })

  const emit = defineEmits(['update:url','on-success'])

  function beforeUpload(file) {
    const fileName = file.name.split('.')
    const fileExt = fileName[fileName.length - 1]
    const isTypeOk = props.ext.indexOf(fileExt) >= 0
    const isSizeOk = file.size / 1024 / 1024 < props.size
    if (!isTypeOk) {
      proxy.$message.error(`上传文件只支持 ${props.ext.join(' / ')} 格式！`)
    }
    if (!isSizeOk) {
      proxy.$message.error(`上传文件大小不能超过 ${props.size}MB！`)
    }
    return isTypeOk && isSizeOk
  }

  function onExceed() {
    proxy.$message.warning('文件上传超过限制')
  }

  function onSuccess(res, file) {
    file.url = res.data.image
    emit('on-success', res, file)
  }
  function handleRemove(file, fileList){
    emit('update:url', fileList)
    // console.log('------handleRemove-------',file, fileList)
  }
  function handleChange(uploadFile, uploadFiles){
    // console.log('------handleChange-------',uploadFile, uploadFiles)
  }
</script>

<style lang="scss" scoped>
  :deep(.el-upload-dragger) {
    width: auto;
    height: auto;
    overflow: unset;

    &.is-dragover {
      border-width: 1px;
    }
  }

  .slot {
    width: 300px;
    height: 160px;
  }
</style>
