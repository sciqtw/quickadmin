<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
  >
    <editor-quill  v-bind="attrs" v-model="value" @upload-open="handleUploadOpen" ref="quill"></editor-quill>
    <dialog-attachment ref="attachment" @select="handleSelectImages"></dialog-attachment>
  </default-field>
</template>

<script>
  import {computed, ref, useAttrs, onMounted} from "vue";
  import {baseProps} from '../Composition/FormField';
  import DialogAttachment from "../../Attachment/dialog-attachment"
  export default {
    name: 'QuillField',
    props: {
      ...baseProps
    },
    components:{
      DialogAttachment
    },
    setup(props) {

      const attrs = useAttrs();
      const value = ref('');

      /**
       * label
       */
      const labelProps = computed(() => {
        return {
          ...props,
          ...attrs
        };
      });


      /** 接管验证 start ******/
      const validate = () => {
        return false;
      };

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

      onMounted(() => {
        value.value = initialValue();
      })


      const attachment = ref(null)
      const quill = ref(null)

      const handleSelectImages = function(images){
        let url = []
        images.forEach((img) => {
          url.push({
            type:'image',
            url:img.image
          })
        })
        quill.value.setUploadFiles(url)
      }

      const handleUploadOpen = function(){
        attachment.value.openDialog()
      }

      return {
        resetField,
        validate,
        handleUploadOpen,
        handleSelectImages,
        attachment,
        quill,
        labelProps,
        attrs,
        value
      }
    },
  }
</script>
<style scoped>

</style>
