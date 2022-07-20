<template>
  <default-field
    v-model="fieldValue"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <el-select
      v-model="value"
      v-bind="attrs"
      :remote-method="remoteMethod"
      :loading="loading"
      @blur="handleBlur"
    >
      <template v-for="(option,index ) in optionList">
        <template v-if="option.options">
          <el-option-group :key="index" :label="option.label">
            <el-option
              v-for="item in option.options"
              :key="item.value"
              v-bind="item"
            >
              <component
                :is="optionDisplay.component"
                v-if="optionDisplay"
                :option="item"
                v-bind="optionDisplay.attribute"
              />
            </el-option>

          </el-option-group>
        </template>
        <el-option
          v-else
          :key="index"
          v-bind="option"
        >
          <component
            :is="optionDisplay.component"
            v-if="optionDisplay"
            :option="option"
            v-bind="optionDisplay.attribute"
          />
        </el-option>
      </template>

    </el-select>
  </default-field>
</template>

<script setup>
  import DefaultField from './DefaultField';
  import {
    useAttrs,
    defineProps,
    ref,
    onMounted,
    computed, defineExpose,
  } from 'vue';

  import { baseProps } from '../Composition/FormField';

  const props = defineProps({
    ...baseProps,
    options: {
      type: [Array],
      default: () => []
    },
    optionDisplay: {
      type: [Object, Boolean],
      default: false
    },
    url: {
      type: String,
      default: ''
    }
  });

  const attrs = useAttrs();
  const value = ref('');
  const def = ref(null);


  const loading = ref(false);
  const optionList = ref([]);

  const fieldValue = computed(() => {
    // console.log('-----fieldValue------select---',value.value)
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

  onMounted(() => {
    value.value = initialValue();
    optionList.value = props.options
  })


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

  /**
   * blur 事件验证数据
   */
  const handleBlur = () => {
    def.value.validate('blur');
  };


  /** 接管验证  ******/
  const validate = () => {
    return false;
  };


  const remoteMethod = (query) =>  {
    if (query !== '') {
      loading.value = true
      Quick.request({
        method: 'get',
        url: props.url,
        params: { query: query }
      }).then(res => {
        loading.value = false
        optionList.value = res.data
      }).catch(() => {
        loading.value = false
      })
    } else {
      optionList.value = []
    }
  }



</script>
<style scoped lang="scss">
  :deep .el-select{
    width:99.5%;
  }
</style>
