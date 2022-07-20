<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >

    <el-radio-group v-model="value" v-bind="fieldAttrs">
      <component
        :is="radioType"
        v-for="(item,index ) in options"
        :key="index + item.key"
        v-bind="item.attrs"
        :label="item.key"
      >{{ item.label }}</component>
    </el-radio-group>
  </default-field>
</template>

<script>

  import {computed, ref, useAttrs,onMounted,onBeforeMount} from "vue";
  import { baseProps } from '../Composition/FormField';
export default {
  props: {
    ...baseProps,
    options: {
      type: Array,
      default: () => {}
    },
    radioButton: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {

    const attrs = useAttrs();
    const value = ref('');
    const def = ref(null);

    const radioType = computed(() => {
      return props.radioButton ? 'el-radio-button' : 'el-radio'
    })

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

    onBeforeMount(() => {
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
      radioType,
      labelProps,
      fieldAttrs,
      value,
      def,
    }
  }
}
</script>
<style scoped>

</style>
