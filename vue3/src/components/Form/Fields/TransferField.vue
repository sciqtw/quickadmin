<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <el-transfer
      v-model="value"
      v-bind="attrs"
      :data="data"
    >

      <template #left-footer>
        <slot name="left-footer"/>
      </template>

      <template #right-footer>
        <slot name="right-footer"/>
      </template>
    </el-transfer>

  </default-field>
</template>

<script>

  import {computed, ref, useAttrs, onMounted} from "vue";
  import {baseProps} from '../Composition/FormField';

  export default {
    name: 'TransferField',
    props: {
      ...baseProps,
      data: {
        type: Array,
        default: () => []
      }
    },
    setup(props) {

      const attrs = useAttrs();
      const value = ref([]);
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
          : [];
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
        attrs,
        value,
        def,
      }
    },
  }
</script>
<style scoped>

</style>
