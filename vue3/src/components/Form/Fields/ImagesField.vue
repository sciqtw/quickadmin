<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <quick-attachment
      v-model:url="value"
      :action="action"
      v-bind="uProps"
      @select="handleSelect"
    />
  </default-field>
</template>

<script>
  import {computed, ref,nextTick, useAttrs, onBeforeMount} from "vue";
  import {baseProps} from '../Composition/FormField';
  // import QuickImages from "@/components/Attachment/quick-images";
  export default {
    name: 'UploadField',
    components:{},
    props: {
      ...baseProps,
      action: {
        type: String,
        default: '/admin/attachment/upload'
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
      moduleName:{
        type: String,
        default: 'admin',
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
          moduleName:props.moduleName
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
      const handleSelect = (res) => {
        value.value = res
      }

      return {
        resetField,
        validate,
        handleSelect,
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

</style>
