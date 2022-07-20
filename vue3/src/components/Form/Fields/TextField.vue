<script>

  import {
    useAttrs,
    resolveComponent,
    ref,
    h,
    onMounted,
    computed,
    getCurrentInstance,
    watch,
  } from 'vue';

  import {baseProps} from '../Composition/FormField';

  export default {
    props: {
      ...baseProps,
      autocomplete: {
        type: [Array, Boolean, String],
        default: false
      },
      type: {
        type: String,
        default: 'text'
      },
      completeComponent: {
        default: () => false
      },
      hasSelect: {
        type: [Boolean],
        default: true
      }
    },
    render() {
      const Component = resolveComponent('default-field')
      const InputItem = resolveComponent('input-item')
      const input = h(resolveComponent(this.inputType), {
        modelValue: this.value,
        'onUpdate:modelValue': (value) => {
          if (this.type === 'number' && typeof parseFloat(value) !== 'NaN') {
            if(Number(value).toString() !== value){
              this.value = value;
            }else{
              this.value = Number(value);
            }
          } else {
            this.value = value
          }
        },
        fetchSuggestions: this.querySearch,
        ...this.inputAttrs,
        onSelect: this.handleSelect,
        onBlur: this.handleBlur,
      }, {...this.$slots, default: (props) => {
          return h(InputItem,props)
        }});


      return h(Component, {
        data: this.value,
        modelValue: this.value,
        'onUpdate:modelValue': (value) => {
          this.value = value
        },
        ref: 'def',
        ...this.labelProps,
        onReset: this.resetField
      }, () => input)
    },
    setup(props, {slots}) {
      const attrs = useAttrs();
      const value = ref('');
      // const def = ref(null);
      const readonlyField = ref(false);
      const {proxy} = getCurrentInstance()
      // const slots = useSlots();

      onMounted(() => {
        readonlyField.value = props.disabled;
      });

      /** 处理表单重置 **/
      const resetField = (data) => {

        if (data) {
          value.value = data;
          return;
        }
        value.value = initialValue();
      };

      /** 接管验证 start ******/
      const validate = () => {
        return false;
      };

      /**
       * 设置仅读字段
       */
      const setReadonly = () => {
        readonlyField.value = true;
      };

      /**
       * 设置为可读写字段
       */
      const writable = () => {
        readonlyField.value = false;
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


      /**
       * label
       */
      const labelProps = computed(() => {
        return {
          ...props,
          ...attrs
        };
      });

      /** inputType */
      const inputType = computed(() => {
        return props.autocomplete ? 'el-autocomplete' : 'el-input';
      });

      const inputAttrs = computed(() => {
        return {
          type: props.type || 'text',
          class: {
            'input-with-select': props.hasSelect
          },
          ...attrs,
          disabled: readonlyField.value,
          autocomplete: ''
        };
      });


      const restaurants = ref([])
      onMounted(() => {
        restaurants.value = props.autocomplete
      })
      const querySearch = (queryString, cb) => {

        // call callback function to return suggestions
        if (Array.isArray(restaurants.value)) {
          const results = queryString
            ? restaurants.value.filter(createFilter(queryString))
            : restaurants.value
          cb(results);
        } else {
          Quick.request()
            .get(
              restaurants.value,
              {
                params: {
                  keyword: queryString
                }
              }
            )
            .then((data) => {
              cb(data.data);
            })
            .catch(() => {

            });
        }
      }
      const createFilter = (queryString) => {

        return (restaurant) => {
          return (
            restaurant.value.toLowerCase().indexOf(queryString.toLowerCase()) !== -1)
        }
      }

      const handleSelect = (item) => {
        if(inputType.value !== 'el-autocomplete'){
          return false;
        }
        value.value = item.value;
      };

      /**
       * blur 事件验证数据
       */
      const handleBlur = () => {
        // if (props.type === 'number' && typeof parseFloat(value.value) !== 'NaN') {
        //   value.value = Number(value.value);
        // }
        proxy.$refs.def.validate('blur');
      };

      return {
        value,
        ref,
        labelProps,
        resetField,
        validate,
        inputType,
        querySearch,
        inputAttrs,
        handleBlur,
        handleSelect,
      }
    }
  }
</script>
<style lang="scss" scoped>

  :deep .input-with-select {
    .el-input-group__prepend {
      background-color: #fff;
      padding-right: 0px;
      padding-left: 0px;
    }

    .el-form-item__label {
      display: none;
    }

    .el-input-group__prepend .el-select {
      margin: -10px 0px;
    }

    .el-form-item {
      margin-bottom: 0;
    }

    .el-form-item__content {
      margin: -1px;
    }
  }

  :deep .el-autocomplete {
    width: 100%;
  }

  .help-text {
    color: #909399;
  }
</style>
