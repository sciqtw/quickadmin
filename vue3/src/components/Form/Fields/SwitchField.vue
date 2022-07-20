<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
  >
    <el-switch
      v-model="value"
      v-bind="fieldAttrs"
    />
  </default-field>
</template>

<script>

  import {computed, ref, useAttrs,onMounted} from "vue";
  import { baseProps } from '../Composition/FormField';
export default {
  name: 'SwitchField',
  props: {
    ...baseProps
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

    const fieldAttrs = computed(() => {
      return {
        ...attrs,
        disabled: props.disabled,
      };
    });

    onMounted(() => {
      value.value = initialValue();
    })



    return {
      resetField,
      validate,
      labelProps,
      fieldAttrs,
      value
    }
  }
}
</script>
<style lang="scss" scoped >
  .q-switch{
    :deep .el-switch__label--left {
      position: relative;
      left: 57px;
      color: #fff;
      z-index: -1111;
    }
    :deep .el-switch__label--right {
      position: relative;
      right: 57px;
      color: #fff;
      z-index: -1111;
    }
    :deep .el-switch__label--right.is-active {
      z-index: 1111;
      color: #fff !important;
    }
    :deep .el-switch__label--left.is-active {
      z-index: 1111;
      color:  #9c9c9c !important;
    }
  }

</style>
