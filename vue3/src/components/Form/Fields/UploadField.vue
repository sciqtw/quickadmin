<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <component
      :is="uploadComponent"
      v-model:url="value"
      :action="action"
      v-bind="uProps"
      @on-success="handleSuccess"
    />
  </default-field>
</template>

<script>
  import {computed, ref,nextTick, useAttrs, onBeforeMount} from "vue";
  import {baseProps} from '../Composition/FormField';
  import ImagesUpload from "@/components/ImagesUpload";
  import ImageUpload from "@/components/ImageUpload";
  import FileUpload from "@/components/FileUpload";
  export default {
    name: 'UploadField',
    components:{
      ImagesUpload,
      ImageUpload,
      FileUpload
    },
    props: {
      ...baseProps,
      action: {
        type: String,
        default: '/admin/index/upload'
      },
      /**
       * 上传类型 image|images|file
       */
      type: {
        type: String,
        default: 'image'
      },
      uploadComponent: {
        type: String,
        default: 'image-upload'
      },
      uploadProps:{
        type:Object,
        default:() => {}
      }
    },
    setup(props) {

      const attrs = useAttrs();
      const value = ref('');
      const def = ref(null);

      /**
       * label
       */
      const labelProps = computed(() => {
        return {
          ...props,
          ...attrs
        };
      });


      const uProps = computed(() => {
        let attrs = {
          ...props.uploadProps,
        };
        return attrs
      })


      /** 处理表单重置 **/
      const resetField = (data) => {

        if (data) {
          value.value = data;
          return;
        }
        value.value = initialValue();
      };

      /**
       * 初始化值
       */
      const initialValue = () => {
        return !(props.default === undefined || props.default === null)
          ? props.default
          : '';
      };


      /** 接管验证 start ******/
      const validate = () => {
        return false;
      };


      onBeforeMount(() => {
        value.value = initialValue();
      })
      const handleSuccess = (res,file) => {
        if(res.code === 0){
          if(props.type === 'image'){
            value.value = res.data.image
          }else if(props.type === 'images'){
            value.value.push(res.data.image)
          }else if(props.type === 'file'){
            value.value.push({
              name: file.name,
              url:res.data.image
            })
          }
        }
      }

      return {
        resetField,
        validate,
        handleSuccess,
        labelProps,
        attrs,
        value,
        def,
        uProps,
      }
    }
  }
</script>
<style lang="scss" scoped >
  ::v-deep .el-upload-dragger{
    padding:0px;
  }

</style>
