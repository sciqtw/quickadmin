<template>
  <slot></slot>
</template>

<script setup>
  import mitt from 'mitt';
  import { Errors } from 'form-backend-validation';
  import {
    getCurrentInstance,
    onBeforeMount,
    onBeforeUnmount,
    provide,
    reactive,
    defineExpose,
    computed,
    defineProps, inject, watch, defineEmits
  } from 'vue';

  import lodash from 'lodash'
  const props = defineProps({
    default: {
      default: ''
    },
    data: {
      type: Object,
      default: () => {
      }
    },
    column: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      default: ''
    },
    labelWidth: {
      type: String,
      default: ''
    },
    rules: {
      type: Array,
      default: () => []
    },
  });

  const fields = [];
  const { proxy } = getCurrentInstance();
  const parent = proxy.$parent;
  const emit = defineEmits(['update:data','change']);


  const getFields = function () {
      return fields;
  }

  /**
   * 验证form
   * @param callback
   */
  const validate = function (callback) {
    let errorArr = [];
    _.each(fields, field => {
      const errors = field.validate('');
      if (errors && errors.length) {
        errorArr = errorArr.concat(errors);
      }
    });
    callback && callback(errorArr);
    return errorArr
  };


  /**
   * 验证form
   * @param callback
   */
  const initData = function (data) {
    _.each(fields, field => {
      const val = lodash.get(data,props.column)
      console.log('---group-----',data,val,props.column)
      if(val !== undefined){
        field.initData(val);
      }else{
        field.initData(data);
      }
      console.log('---group-----',val,field.column)
    });
  };



  /**
   * 设置验证错误信息
   */
  const setErrors = (errors) => {
    fields.forEach(field => {
      field.setErrors(errors);
    });
  };

  /**
   * 清除验证
   */
  const clearValidate = () => {
    const errors = new Errors();
    fields.forEach(field => {
      field.setErrors(errors);
    });
  };

  /**
   * 重置表单
   */
  const resetField = function (data) {
    fields.forEach(field => {
      if(data && data[field.column]){
        field.resetField(data[field.column]);
      }else{
        field.resetField();
      }

    });
  };

  /**
   * 获取当前值
   */
  const getFieldValue = () => {
    const data = {};
    return _.tap(data, formData => {
      _.each(fields, field => {
        field.fill(formData);
      });
    });
  };

  /**
   * 填充formArr
   * @param formData
   */
  const fill = (formData) => {
    if (parent && parent.fill) {
      return parent.fill(formData)
    }
    formData[props.column] = getFieldValue();
    return formData[props.column];
  };

  /**
   *  注册field
   * @param field
   */
  const addField = (field) => {
    if (field) {
      fields.push(field);
    }
  };

  /**
   * 注销 field
   * @param field
   */
  const removeField = (field) => {
    fields.splice(fields.indexOf(field), 1);
  };

  /**
   * 给上级注册field
   * @type {{}}
   */
  const groupForm = inject('groupForm', {});



  /**
   * field value
   */
  const changeValue = (key, value) => {
    const val = { [key]: value };
    if (groupForm && 'changeValue' in groupForm) {
      groupForm.changeValue(props.column, val);
    } else {
      const data = Object.assign({}, props.data, val);
      proxy.$emit('update:data', data);
    }

  };

  const valuePath = computed(() => {
    if (groupForm && groupForm.column) {
      return groupForm.valuePath + "." + props.column;
    }
    return '';
  });

  const bus = mitt();

  bus.on('change', function (data) {
    if(props.column !== ''){
      data = {
        column:props.column + '.' + data.column,
        value:data.value
      }
    }
    if(groupForm?.bus){
      groupForm?.bus.emit('change',data);
    }
    emit('change',data)
  })

  const formItem = reactive({
    column: props.column,
    valuePath: valuePath.value,
    setErrors,
    validate,
    resetField,
    fill,
    addField,
    removeField,
    changeValue,
    initData,
    bus: bus,
    props: {
      labelWidth: props.labelWidth,
      inlineMessage: true,
      disabled: true,
      size: ''
    }
  });

  provide('groupForm', formItem);

  onBeforeMount(() => {
    if ('addField' in groupForm) {
      groupForm.addField(formItem);
    }

  });
  onBeforeUnmount(() => {
    if ('removeField' in groupForm) {
      groupForm?.removeField(formItem);
    }

  });

  const getGroupData = () => {
    return fill({});
  };

  const validateForm = (callback) => {
    validate(callback);
  };

  const resetFields = () => {
    resetField();
  };

  defineExpose({
    getGroupData,
    initData,
    resetFields,
    resetField,
    validateForm,
    getFields
  });

</script>

