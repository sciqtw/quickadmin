<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <el-input-number
      v-model="value"
      v-bind="fieldAttrs"
      @blur="handleBlur"
    />
  </default-field>
</template>

<script>

  import {computed, ref, useAttrs,onMounted} from "vue";
  import { baseProps } from '../Composition/FormField';

  export default {
    name: 'InputNumberField',
    props: {
      ...baseProps
    },
    setup(props) {

      const attrs = useAttrs();
      const value = ref('');
      const def = ref(null);

      const fieldAttrs = computed(() => {
        return {
          ...attrs,
          disabled: props.disabled,
        };
      });

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

      /**
       * blur 事件验证数据
       */
      const handleBlur = () => {
        def.value.validate('blur');
      };


      return {
        handleBlur,
        resetField,
        validate,
        labelProps,
        fieldAttrs,
        value,
        def,
      }
    },
  }

</script>
<style scoped lang="scss">
  :deep .el-select {
    width: 100%;
  }
</style>
