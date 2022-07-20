<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
  >
    <el-row type="flex" align="middle" style="height:36px;">
      <el-rate
        v-model="value"
        v-bind="attrs"
        score-template="{value}"
      />
    </el-row>

  </default-field>
</template>

<script>
  import {computed, ref, useAttrs, onMounted} from "vue";
  import {baseProps} from '../Composition/FormField';

  export default {
    name: 'RateField',
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

      onMounted(() => {
        value.value = initialValue();
      })


      return {
        resetField,
        validate,
        labelProps,
        attrs,
        value
      }
    },
  }
</script>
<style scoped>

</style>
