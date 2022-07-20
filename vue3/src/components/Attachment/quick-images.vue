<template>
  <div class="upload-container">
    <div v-for="(item, index) in showImages" :key="index" class="images">
      <el-image v-if="index < max" :src="item" :style="`width:${width}px;height:${height}px;`" fit="cover"/>
      <div class="mask">
        <div class="actions">
                    <span title="预览" @click="preview(index)">
                        <el-icon><el-icon-zoom-in/></el-icon>
                    </span>
          <span title="移除" @click="remove(index)">
                        <el-icon><el-icon-delete/></el-icon>
                    </span>
          <span v-show="showImages.length > 1" title="左移" :class="{'disabled': index == 0}" @click="move(index, 'left')">
                        <el-icon><el-icon-back/></el-icon>
                    </span>
          <span v-show="showImages.length > 1" title="右移" :class="{'disabled': index == showImages.length - 1}"
                @click="move(index, 'right')">
                        <el-icon><el-icon-right/></el-icon>
                    </span>
        </div>
      </div>
    </div>
    <dialog-attachment
      v-show="showImages.length < imageMax"
      :headers="headers"
      :action="action"
      :moduleName="moduleName"
      :data="data"
      :name="name"
      :size="size"
      :ext="ext"
      :max="imageMax"
      @select="handleSelect"
      class="images-upload"
    >
      <div class="image-slot" :style="`width:${width}px;height:${height}px;`">
        <svg-icon icon="el-icon-plus"/>
      </div>
    </dialog-attachment>
    <div v-if="!notip" class="el-upload__tip">
      <div style="display: inline-block;">
        <el-alert
          :title="`上传图片支持 ${ ext.join(' / ') } 格式，单张图片大小不超过 ${ size }MB，建议图片尺寸为 ${width}*${height}，且图片数量不超过 ${ max } 张`"
          type="info" show-icon :closable="false"/>
      </div>
    </div>
    <el-image-viewer v-if="uploadData.imageViewerVisible" :url-list="showImages" :initial-index="uploadData.dialogImageIndex"
                     @close="previewClose"/>
  </div>
</template>

<script setup>
  import DialogAttachment from "./dialog-attachment"
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
    url: {
      type: [Array,String],
      default: () => []
    },
    max: {
      type: Number,
      default: 10
    },
    size: {
      type: Number,
      default: 2
    },
    width: {
      type: Number,
      default: 100
    },
    height: {
      type: Number,
      default: 100
    },
    placeholder: {
      type: String,
      default: ''
    },
    notip: {
      type: Boolean,
      default: true
    },
    ext: {
      type: Array,
      default: () => ['jpg', 'png', 'gif', 'bmp']
    },
    moduleName:{
      type: String,
      default: 'admin',
    },
  })

  const emit = defineEmits(['update:url', 'on-success'])


  const imageMax = computed(() => {
    if(typeof props.url === 'string'){
      return 1
    }
    return props.max - props.url.length
  })

  const showImages = computed(() => {
    if(typeof props.url === 'string'){
      if(props.url){
        return [
          props.url
        ]
      }
      return []
    }
    return props.url
  })

  const uploadData = ref({
    dialogImageIndex: 0,
    imageViewerVisible: false,
    progress: {
      preview: '',
      percent: 0
    }
  })

  // 预览
  function preview(index) {
    uploadData.value.dialogImageIndex = index
    uploadData.value.imageViewerVisible = true
  }

  // 关闭预览
  function previewClose() {
    uploadData.value.imageViewerVisible = false
  }

  // 移除
  function remove(index) {
    let url = props.url
    if(typeof  url === 'string'){
      url = ''
    }else{
      url.splice(index, 1)
    }

    emit('update:url', url)
  }

  // 移动
  function move(index, type) {
    let url = props.url
    if (type == 'left' && index != 0) {
      url[index] = url.splice(index - 1, 1, url[index])[0]
    }
    if (type == 'right' && index != url.length - 1) {
      url[index] = url.splice(index + 1, 1, url[index])[0]
    }
    emit('update:url', url)
  }
  function handleSelect(images) {
    let url = props.url

      images.forEach((img) => {
        if(typeof  url === 'string'){
          url = img.image
        }else{
          url.push(img.image)
        }
      })
    console.log('-----update:url---',url)
    emit('update:url', url)
  }
</script>

<style lang="scss" scoped>
  .upload-container {
    line-height: initial;
    display: flex;
    flex-wrap: wrap;
  }

  .images {
    position: relative;
    display: inline-block;
    margin-right: 10px;
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    overflow: hidden;

    .el-image {
      display: block;
    }

    .mask {
      opacity: 0;
      position: absolute;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgb(0 0 0 / 50%);
      transition: all 0.3s;

      .actions {
        width: 100px;
        height: 100px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;

        span {
          width: 50%;
          text-align: center;
          color: #fff;
          cursor: pointer;
          transition: all 0.1s;

          &.disabled {
            color: #999;
            cursor: not-allowed;
          }

          &:hover:not(.disabled) {
            transform: scale(1.5);
          }

          .el-icon {
            font-size: 24px;
          }
        }
      }
    }

    &:hover .mask {
      opacity: 1;
    }
  }


  .images-upload {
    display: inline-block;
    background-color: #fff;
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    box-sizing: border-box;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }

  .image-slot {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    color: #909399;
    background-color: transparent;

    i {
      font-size: 30px;
    }
  }

</style>
