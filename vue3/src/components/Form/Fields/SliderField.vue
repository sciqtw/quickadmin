<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <el-slider
      v-model="value"
      class="qk-slider"
      :marks="marks"
      v-bind="attrs"
    />
  </default-field>
</template>

<script>

  import {computed, ref, useAttrs, onMounted} from "vue";
  import {baseProps} from '../Composition/FormField';

  export default {
    name: 'SliderField',
    props: {
      ...baseProps,
      marks: {
        type: Object,
        default: () => {
        }
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


      onMounted(() => {
        value.value = initialValue();
      })


      return {
        resetField,
        validate,
        labelProps,
        attrs,
        value,
        def,
      }
    },
  }
</script>
<style scoped lang="scss">
  .qk-slider {
    :deep .el-slider__button-wrapper {
      z-index: 1;
    }
  }
</style>
