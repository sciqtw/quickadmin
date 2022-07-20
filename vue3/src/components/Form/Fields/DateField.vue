<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <el-date-picker
      ref="dPicker"
      v-model="value"
      v-bind="fieldAttrs"
      @blur="handleBlur"

    />
  </default-field>
</template>

<script>

  import {computed, ref, useAttrs,onMounted,watch} from "vue";
  import { baseProps } from '../Composition/FormField';

export default {
  name: 'DateField',
  props: {
    ...baseProps,
    pickerOptions: {
      type: [Object, Array]
    }

  },
  setup(props) {

    const attrs = useAttrs();
    const value = ref('');
    const def = ref(null);
    const dPicker = ref(null);

    const pickerEvent = (picker, start, end) => {
      start = new Date(start)
      end = new Date(end)
      picker.$emit('pick', [start, end])
    }

    const opts = computed(() => {
      const options = Object.assign({}, props.pickerOptions)
      if (options.shortcuts && options.shortcuts.length > 0) {
        options.shortcuts = options.shortcuts.map((item) => {

          return {
            text: item.text,
            value: () => {
              const start = new Date(parseInt(item.start))
              const end = new Date(parseInt(item.end))
              return [start, end]
            },
          }
        })
      }
      return options.shortcuts
    })

    const fieldAttrs = computed(() => {
      return {
        'value-format':"x",
        ...attrs,
        shortcuts:opts.value,
        disabled: props.disabled,
      };
    });


    const fieldValue = computed(() => {
      return value.value
    })

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
      // console.log('-------------dPicker-',dPicker.value)
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
      fieldValue,
      value,
      labelProps,
      fieldAttrs,
      def,
      dPicker,
    }
  },
  computed: {

  },
  mounted() {
  },
  methods: {
    // pickerEvent(picker, start, end) {
    //   start = new Date(start)
    //   end = new Date(end)
    //   picker.$emit('pick', [start, end])
    // }
  }
}
</script>
<style scoped lang="scss">
  .qk-slider{
    :deep .el-slider__button-wrapper{
      z-index: 1;
    }
  }
</style>
