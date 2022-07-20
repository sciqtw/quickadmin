<template>
  <div class="qk-image">
    <template  v-for="(image,index) in images">
      <el-image
        class="image-item"
        :key="index"
        :src="image"
        v-if="(index+1) <= max"
        :initial-index="index"
        :preview-src-list="images"
        :style="imageStyle"
        v-bind="attributes"
      >
      </el-image>
    </template>

    <div  class="image-more" v-if="images.length > max" :style="imageStyle">
      <div class="add-box" @click="handleClick">
        + {{images.length - max}}
      </div>
      <el-image
        ref="imageMore"
        class="image-item"
        :src="images[max]"
        :initial-index="max"
        :preview-src-list="images"
        :style="imageStyle"
        v-bind="attributes"
      >
      </el-image>
    </div>


  </div>
</template>

<script>
  export default {
    name: 'ImageField',
    props: {
      images: {
        type: Array,
        default: () => []
      },
      imageProps: {
        type: Object,
        default: () => {
        }
      },
      width: {
        type: String,
        default: '100'
      },
      height: {
        type: String,
        default: '100'
      },
      max:{
        type:Number,
        default:3
      },
      value: {
        type: String,
        default: ''
      },
    },
    methods:{
      handleClick(){
        this.$refs.imageMore.clickHandler()
      },
    },
    computed: {
      imageStyle() {
        return {
          width: this.width,
          height: this.height,
        }
      },
      attributes() {

        return {
          ...this.imageProps,
        }
      },
      /**
       * has value
       * @returns {boolean}
       */
      hasValue() {
        return this.field.value !== null
      }
    }
  }
</script>
<style scoped lang="scss">
  .qk-image {
    display: flex;
    flex-wrap: wrap;
    .image-item {
      margin-right: 5px;
    }
  }

  .image-more{
    position: relative;
    cursor: pointer;
    .add-box{
      position: absolute;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1;
      background-color: rgb(110 105 105 / 75%);
      width: 100%;
      height: 100%;
      text-align: center;
      color: #FFFFFF;
    }
  }

  .index-icon {
    /*text-align: center;*/
  }
</style>
