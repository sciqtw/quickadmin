<template>
  <form-group ref="gForm" v-model:data="data" @change="handleChange">
    <el-form label-width="120px" :model="ruleForm">
      <json-render :render-data="fields"></json-render>
      <slot name="footer"></slot>
    </el-form>
  </form-group>
</template>

<script setup>
  import mitt from 'mitt';
  import FormGroup from './FormGroup';
  import lodash from 'lodash'
  import {
    provide,
    computed,
    ref,
    defineProps,
    onMounted,
    useAttrs,
    defineEmits,
    defineExpose
  } from 'vue';

  const props = defineProps({
    fieldList: {
      type: [Array],
      default: () => []
    },
    modelValue:[Array,Object,Boolean]
  })

  const ruleForm = ref({})

  const attrs = useAttrs();


  const gForm = ref({});
  const root = ref({});
  const data = ref({});


  const fields = computed(() =>{
    return {
      component:'el-form',
      props:{
        ...attrs
      },
      extraAttrs:[],
      children:props.fieldList,
    };
  })


  /**
   * 验证表单
   */
  const validateForm = (callback) => {
    return gForm.value.validateForm(callback)
  };

  /**
   * 获取表单数据
   */
  const formData = () => {
    return gForm.value.getGroupData();
  };

  /**
   * 重置表单
   */
  const resetForm = () => {
    gForm.value.resetFields();
  };

  /**
   * 设置值
   */
  const initData = (data) => {
    gForm.value.initData(data);
  };

  onMounted(() => {
    if(props.modelValue){
      initData(props.modelValue)
    }
  })


  const emit = defineEmits(['submit', 'reset','cancel','change']);
  const handleChange = function(data){

    let modelValue = props.modelValue
    lodash.set(modelValue,data.column,data.value)
    emit('update:modelValue',modelValue)
    emit('change-column',data)
    emit('change',modelValue)
  }


  /**
   * 导出
   */
  defineExpose({
    resetForm,
    initData,
    formData,
    validateForm
  });


  const handleValidate = (e) => {
    console.log('-----handleValidate',e)
  }

  provide('form', {
    emit,
    props: {
      labelWidth: '40px',
      inlineMessage: true,
      disabled: true,
      size: ''
    }
  });

  const bus = mitt();
  bus.on('test', function (e) {
    // console.log('---------bus--test', e);
  });
  provide('formBus', bus);

</script>

