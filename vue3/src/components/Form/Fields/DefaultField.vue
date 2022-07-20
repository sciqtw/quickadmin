<template>
  <el-form-item
    v-show="show"
    :label="labelText"
    :labelWidth="labelWidth"
    :error="firstError"
    :class="{'is-required':hasRequired,}"
    v-bind="attrsData"
  >
   <div class="dir-left-nowrap" style="width:100%">
     <div :style="formItemStyle" class="box-grow-0" v-show="!fixedVal" >
       <slot/>
     </div>
     <div class="box-grow-0" v-if="fixedValue !== null">
       <el-checkbox style="margin-left: 5px;" v-model="fixedVal" @change="handleCheckbox">{{fixedText}}  </el-checkbox>
     </div>
   </div>

    <div v-if="helpText" class="help-text"><i class="el-icon-info"/> <span>{{ helpText }}</span></div>
    <template v-slot:label="label">
      <slot name="label" :label="label"/>
    </template>
    <template v-slot:error="error">
      <slot name="error" :error="error"/>
    </template>
  </el-form-item>
</template>

<script>
  import {
    computed,
    reactive,
    nextTick,
    onMounted,
    onBeforeUnmount,
    getCurrentInstance,
    ref,
    inject,
    watch,
  } from 'vue';
  import { Errors } from 'form-backend-validation';
  import useValidate from '../Composition/useValidate';
  import { contrasts } from '../Composition/FormField';

  import lodash from 'lodash'
  export default {
    props: {
      showField: {
        type: Boolean,
        default: true
      },
      data: {
        default: ''
      },
      modelValue:{
        default:''
      },
      label: {
        type: String,
        default: ''
      },
      errors: {
        default: () => new Errors()
      },
      helpText: {
        type: String,
        default: ''
      },
      rules: {
        type: Array,
        default: () => []
      },
      hiddenLabel: {
        type: Boolean,
        default: false
      },
      column: {
        type: String,
        default: ''
      },
      title: {
        type: String,
        default: ''
      },
      required: {
        type: Boolean,
        default: false
      },
      emit: {
        type: Array,
        default: () => {
        }
      },
      emitKey: {
        type: String,
        default: ''
      },
      isRegistered:{
        type: Boolean,
        default: true
      },
      width:{
        type: String,
        default: '100%'
      },
      fixedValue:{
        type: [String,Number,Object,Boolean],
        default: null
      },
      fixedDefault:{
        type: [String,Number,Object,Boolean],
        default: null
      },
      fixedText:{
        type: [String],
        default: '设置'
      },
    },
    setup(props, { attrs, emit, slots }) {

      const formItemStyle = computed(() => {
        const style = {}
        if(props.width){
          style.width = props.width
        }
        return style
      })
      const validateDisabled = ref(false);
      const groupForm = inject('groupForm', {});
      const form = inject('form');
      const bus = inject('formBus');
      const show = ref(true);
      const {proxy} = getCurrentInstance();
      const parent = proxy.$parent;
      const info = computed(() => {
        return props.modelValue;
      });
      const { errors, validateData, hasRequired, firstError } = useValidate(info, props.column, props.rules);

      watch(() => props.modelValue, (value, preValue) => {

        handleEmit('change');
        groupForm?.bus.emit('change', {
          column:props.column,
          value:value
        });


        if (!validateDisabled.value) {
          validate('change');
        }
        validateDisabled.value = false;



      },{deep:true});


      /**
       * 验证数据
       */
      const validate = (event) => {
        /** 父组件定义了验证方法，则使用父组件的验证规则 **********/
        if(!show.value){
          // 不显示字段不需要验证
          return true;
        }
        if (parent && typeof parent.validate === 'function') {
          // console.log('----validate------',parent,parent.validate)
          let validRes = parent.validate(event);

          if (validRes === true) {
            clearValidate()
            return [];
          }
          if(validRes !== false){
            if (validRes) {
              errors.value = new Errors({ [props.column]: validRes });
              if(typeof validRes === 'string'){
                return [{ message: validRes, fieldValue: '', field: props.column }];
              }else{
                return validRes
              }
            }else{
              clearValidate()
              return [];
            }

          }


        }
        /** 父组件验证方法没有定义 或者 返回false 使用默认验证 **/
        return validateData(event);

      };

      const labelText = computed(() => {
        if (props.hiddenLabel === true) {
          return '';
        }
        return props.title === '' ? props.column : props.title;
      });

      /**
       * labelWidth
       */
      const labelWidth = computed(() => {

        if(props.hiddenLabel === true){
          return '0px'
        }
        if (attrs.labelWidth) {
          return attrs.labelWidth;
        }
        if(groupForm.props.labelWidth){
          return groupForm.props.labelWidth;
        }
        return '';
      });

      /**
       * 扩展属性
       * */
      const attrsData = computed(() => {
        return {
          ...attrs,
        };
      });

      /**
       * 清除验证
       */
      const clearValidate = () => {
        errors.value = new Errors();
      };

      /**
       * 设置错误信息
       * @param errors
       */
      const setErrors = (errors) => {
        errors.value = new Errors(errors);
      };

      /**
       * 尺寸
       * @type {ComputedRef<unknown>}
       */
      const fieldSize = computed(() => {

        if (props.size) {
          return props.size;
        }
        if (groupForm.props.size) {
          return groupForm.props.size;
        }
        return form.props.size;
      });

      /**
       * 获取当前值
       */
      const getFieldValue = () => {
        return props.modelValue;
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
        return formData;
      };

      /**
       * 重置field
       */
      const resetField = (data) => {
        validateDisabled.value = true;
        errors.value = new Errors();
        emit('reset',data);
      };

      /**
       * 填充formArr
       * @param formData
       */
      const initData = (data) => {

        const val = lodash.get(data,props.column)
        console.log('------initData----',props.column,data,val)
        if(val !== undefined){

          emit('update:modelValue',val)
        }
      };


      const valuePath = computed(() => {
        if (!groupForm.valuePath) {
          return props.column;
        }
        return groupForm.valuePath + '.' + props.column;
      });

      const formItem = reactive({
        column: props.column,
        valuePath: valuePath,
        resetField,
        setErrors,
        validate,
        fill,
        initData,
      });


      const handleEmit = (type) => {

        // 触发emit事件
        if (props.emit && props.emit.length) {
          props.emit.forEach((data) => {
              const { name, condition, option, event, yes, no } = data;
              if (!event || event === type) {
                const res = contrasts(condition, option, getFieldValue());
                const action = res ? yes : no;
                console.log(type, res, data, option, getFieldValue());
                if (action) {
                  dispatchField(name, action, data);
                }
              }
            }
          );
        }
      };




      /**
       * 表单联动
       * @param column 联动的字段名称
       * @param action 事件名称
       * @param params 参数
       */
      const dispatchField = (column, action, params) => {
        // bus.emit(column + '-input-event' + this.gKey, { from: this.column, action: action, params: params })
        /** 表单全局注册 **/
        groupForm.bus.emit(column, {
          from: valuePath.value,
          action: action,
          params: params
        });

        // if (valuePath.value.indexOf('.') !== -1) {
        //   groupForm.bus.emit(props.column, {
        //     from: props.column,
        //     action: action,
        //     params: params
        //   });
        // }
      };

      /**
       * 对外提供事件驱动
       */
      const onEvent = () => {

        if (props.emitKey) {
          groupForm.bus.on(props.emitKey, (data) => {
            const { action } = data;
            if (parent[action] && typeof parent[action] === 'function') {
              parent[action](data);
            }else if(action === "show"){
              show.value = true
            }else if(action === "hidden"){
              show.value = false
            }
          });
        }
        /** 表单全局注册 **/
        // bus.on(valuePath.value, (data) => {
        //   const { action } = data;
        //   if (parent.exposed[action] && typeof parent.exposed[action] === 'function') {
        //     parent.exposed[action](data);
        //   }
        // });
        //
        // if (valuePath.value.indexOf('.') !== -1) {
        //
        //   /** 子表单域表单局部注册  ***/
        //   groupForm.bus.on(props.column, (data) => {
        //     const { action } = data;
        //     if (parent.exposed[action] && typeof parent.exposed[action] === 'function') {
        //       parent.exposed[action](data);
        //     }
        //   });
        // }
      };

      nextTick(() => {
        handleEmit('change');
        handleEmit('blur');
      })

      onMounted(() => {
        onEvent();
        show.value = props.showField;
        if(props.isRegistered){
          groupForm?.addField(formItem);
        }

      });

      onBeforeUnmount(() => {
        if(props.isRegistered){
          groupForm?.removeField(formItem);
        }
      });

      const fixedVal = ref(false)
      const handleCheckbox = (res) => {
        fixedVal.value = res
      }
      watch(() => props.modelValue, (value, preValue) => {
        if(props.fixedValue !== null){
          fixedVal.value = value === props.fixedValue
        }

      });
      watch(fixedVal,(value) => {
        if(value){
          emit('update:modelValue',props.fixedValue)
        }else{
          emit('update:modelValue',props.fixedDefault)
        }
      })

      return {
        show,
        hasRequired,
        validate,
        initData,
        labelWidth,
        attrsData,
        labelText,
        firstError,
        formItemStyle,
        fixedVal,
        handleCheckbox,
      };
    },
  };
</script>
<style lang="scss" scoped>
  .help-text {
    width: 100%;
    padding-top: 5px;
    color: #a9a9a9;
    font-size: 12px;
    line-height: 20px;
  }

</style>
