<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
  >
    <el-time-picker
      v-model="value"
      :disabled-hours="disabled_hours"
      :disabled-minutes="disabled_minutes"
      v-bind="attrs"
    >
    </el-time-picker>
  </default-field>
</template>

<script>
  import {computed, ref, useAttrs, onMounted} from "vue";
  import {baseProps} from '../Composition/FormField';
  const makeRange = (start, end) => {
    const result = []
    for (let i = start; i <= end; i++) {
      result.push(i)
    }
    return result
  }
  export default {
    name: 'TimeField',
    props: {
      ...baseProps,
      isRange: {
        type: Boolean,
        default: false
      },
      disabledHours:{
        type:[Array],
        default:() =>[]
      },
      disabledMinutes:{
        type:[Array,Object],
        default:() =>[]
      },
      disabledSeconds:{
        type:[Array,Object],
        default:() =>[]
      }
    },
    setup(props) {

      const attrs = useAttrs();
      const value = ref(new Date());
      console.log('----------teime',value.value)
      const def = ref(null);

      const disabled_hours = () => {
        return props.disabledHours
      }

      const disabled_minutes = (hour) => {
        if(!props.disabledMinutes.length){
          return []
        }
        if(props.disabledMinutes[hour]){
          return makeRange(props.disabledMinutes[hour][0],props.disabledMinutes[hour][1])
        }else if(props.disabledMinutes['all']){
          return makeRange(props.disabledMinutes['all'][0],props.disabledMinutes['all'][1])
        }
        return []
      }


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
          ? new Date(props.default)
          : new Date();
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

      /**
       * 填充formArr
       * @param formData
       */
      const fill = (formData) => {
        formData[props.column] = value.value.getTime();
        return formData;
      };



      return {
        handleBlur,
        resetField,
        validate,
        disabled_hours,
        disabled_minutes,
        fill,
        labelProps,
        attrs,
        value,
        def,
      }
    }
  }
</script>
<style scoped lang="scss">
  .qk-slider {
    :deep .el-slider__button-wrapper {
      z-index: 1;
    }
  }
</style>
