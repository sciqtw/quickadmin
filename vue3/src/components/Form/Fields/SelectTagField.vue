<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >

     <div class="dir-left-wrap">
       <el-tag
         v-for="(tag,index) in value"
         :key="index"
         style="margin-right:8px;margin-bottom:8px;"
         closable
         :type="tag.type"
         @close="handleTagClose(index,tag)"
       >
         {{ tag.name }}
       </el-tag>
       <el-button type="primary" size="small" @click="openDialog" >选择</el-button>
     </div>

  </default-field>
</template>

<script>

  import {computed, ref, useAttrs, onMounted} from "vue";
  import {baseProps} from '../Composition/FormField';

  export default {
    name: 'SelectTagField',
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
      load: {
        type: [String,Object],
        default: ''
      },
    },
    setup(props) {

      const attrs = useAttrs();
      const value = ref('');
      const def = ref(null);

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

      onMounted(() => {
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
        labelProps,
        fieldAttrs,
        value,
        def,
      }
    },
    computed: {

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
      handleTagClose(index, tag) {
        this.value.splice(index, 1)
      },
      openDialog(){
        let that = this

        let openContent = this.load
        Quick.api.open(openContent, {
          component: 'dialog',
          title:'选择'+this.title,
          beforeClose: function (event, params, done) {
            done()
            if (event === 'submit' && Array.isArray(params)) {
              that.value = params
            } else if (event === 'push') {
              that.value = that.value.concat(params)
            }
          }
        })
      },
      /**
       * 设置字段的初始值
       */
      setInitialValue() {
        this.value = !(this.default === undefined || this.default === null)
          ? this.default
          : []
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
