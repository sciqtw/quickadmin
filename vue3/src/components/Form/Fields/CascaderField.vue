<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <div style="display: flex;">
      <component
        :is="cascaderType"
        v-model="value"
        :options="cascaderOptions"
        :props="cascaderProps"
        v-bind="attrs"
        @blur="handleBlur"
      >
        <template v-if="optionDisplay" #default="{ node, data }">
          <component
            :is="optionDisplay.component"
            :data="data"
            :node="node"
            v-bind="optionDisplay.attribute"
          />
        </template>
      </component>
    </div>

  </default-field>
</template>

<script>

  import {computed, ref, useAttrs, onMounted} from "vue";
  import {baseProps} from '../Composition/FormField';

  export default {
    name: 'CascaderField',
    props: {
      ...baseProps,
      options: {
        type: [Array],
        default: () => []
      },
      optionDisplay: {
        type: [Object, Boolean],
        default: false
      },
      props: {
        type: [Object, Array],
        default: () => {
        }
      },
      lazy: {
        type: Boolean,
        default: false
      },
      load: {
        type: String,
        default: ''
      },
      panel: {
        type: Boolean,
        default: false
      },
      url: {
        type: String,
        default: ''
      }
    },
    setup(props) {

      const attrs = useAttrs();
      const value = ref('');
      const def = ref(null);
      const cascaderOptions = ref([])

      const fieldAttrs = computed(() => {
        return {
          ...attrs,
          disabled: props.disabled,
        };
      });

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

      const loadData = () => {
        Quick.request({
          method: 'post',
          url: props.load,
          params: {},
        }).then(response => {
          if (!response.code) {
            cascaderOptions.value = response.data
          }
        }).catch((error) => {
          console.log('error lazyLoad', error)
        })
      }

      onMounted(() => {
        cascaderOptions.value = props.options
        if(props.load){
          loadData()
        }
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
        cascaderOptions,
        labelProps,
        fieldAttrs,
        value,
        def,
      }
    },
    computed: {
      cascaderType() {
        return this.panel ? 'el-cascader-panel' : 'el-cascader'
      },
      cascaderProps() {
        const attrs = {
          lazyLoad: this.lazyLoad
        }
        return {
          ...this.props,
          ...attrs
        }
      },
      defaultAttrs() {
        return {
          ...this.$attrs
        }
      },
      attrs() {
        const attrs = this.extraAttrs
        return {
          ...this.defaultAttrs,
          ...attrs
        }
      }
    },
    methods: {
      /**
       * 设置字段的初始值
       */
      setInitialValue() {
        this.value = !(this.default === undefined || this.default === null)
          ? this.default
          : []
      },
      lazyLoad(node, resolve) {
        console.log(node)
        const {level, value, label, path} = node
        Quick.request({
          method: 'post',
          url: this.url,
          params: {},
          data: {node: {level: level, value: value, label: label, path: path}}
        }).then(response => {
          if (!response.code) {
            resolve(response.data)
          }
        }).catch((error) => {
          console.log('error lazyLoad', error)
        })
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
