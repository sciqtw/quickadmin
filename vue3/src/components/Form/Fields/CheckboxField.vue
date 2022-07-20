<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <component
      :is="checkType"
      v-if="showCheckAll"
      v-model="checkAll"
      :indeterminate="isIndeterminate"
      @change="handleCheckAllChange"
    >全选
    </component>
    <div v-if="showCheckAll" style="margin: 15px 0;"></div>
    <el-checkbox-group
      v-model="value"
      v-bind="attrs"
      @change="handleCheckedChange"
    >
      <component
        :is="checkType"
        v-for="item in options"
        :key="item.key"
        v-bind="item.attrs"
        :label="item.key"
      >{{ item.name }}
      </component>
    </el-checkbox-group>
  </default-field>
</template>

<script>

  import {computed, ref, useAttrs, onMounted,onBeforeMount} from "vue";
  import {baseProps} from '../Composition/FormField';

  export default {
    name: 'CheckboxField',
    props: {
      ...baseProps,
      options: {
        type: Array,
        default: () => {
        }
      },
      showCheckAll: {
        type: Boolean,
        default: false
      },
      checkButton: {
        type: Boolean,
        default: false
      }
    },
    setup(props) {

      const attrs = useAttrs();
      const value = ref([]);
      const def = ref(null);

      const checkAll = ref(false)
      const isIndeterminate = ref(true)

      const checkType = computed(() => {
        return props.checkButton ? 'el-checkbox-button' : 'el-checkbox'
      });

      const handleCheckAllChange = (val) => {
        value.value = []
        if (val) {
          props.options.forEach((item) => {
            if(item.disabled === false){
              value.value.push(item.key)
            }
          })
        }
        isIndeterminate.value = false
      }
      const handleCheckedChange = (value) => {
        // console.log('value', value)
        const checkedCount = value.length
        const length = _.filter(props.options, ['disabled', false]).length
        checkAll.value = checkedCount === length
        isIndeterminate.value = checkedCount > 0 && checkedCount < length
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
          ? props.default
          : [];
      };

      onBeforeMount(() => {
        value.value = initialValue();
        handleCheckedChange(value.value)
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
        handleCheckedChange,
        handleCheckAllChange,
        labelProps,
        attrs,
        value,
        def,
        checkType,
        isIndeterminate,
        checkAll,
      }
    }
  }
</script>
<style scoped>

</style>
