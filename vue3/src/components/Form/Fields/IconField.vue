<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <el-input v-model="value" placeholder="请输入内容">
      <template #prepend>
        <quick-icon :icon="value" />
      </template>
      <template #append>
        <select-icon @selected="selectIcon">选择</select-icon>
      </template>
    </el-input>

  </default-field>
</template>

<script>
  import {computed, ref, useAttrs, onMounted} from "vue";
  import {baseProps} from '../Composition/FormField';

export default {
  name: 'IconField',
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

    const selectIcon = (event) => {
      value.value = event
    }

    onMounted(() => {
      value.value = initialValue();
    })


    return {
      resetField,
      validate,
      labelProps,
      selectIcon,
      attrs,
      value,
      def,
    }
  }
}
</script>
<style lang="scss" scoped >

</style>
