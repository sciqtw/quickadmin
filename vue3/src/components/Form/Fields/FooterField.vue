<template>
  <teleport :to="'#' + modalId" :disabled="!fixed">
    <component :is="footerComponent">
      <el-form-item
        ref="def"
        style="height: 100%;"
      >
        <div :style="formItemStyle">
          <json-render v-if="submitBtn" :render-data="submitBtn" @click="submit"/>
          <el-button v-else type="primary" @click="submit">{{__('Submit') }}</el-button>

          <template v-if="showReset">
            <json-render v-if="resetBtn" :render-data="resetBtn" @click="reset"/>
            <el-button v-else @click="reset">{{ __('Reset') }}</el-button>
          </template>

          <template v-if="showCancel">
            <json-render v-if="cancelBtn" :render-data="cancelBtn" @click="cancel"/>
            <el-button v-else @click="cancel">{{ __('Cancel')}}</el-button>
          </template>
        </div>

        <slot></slot>
      </el-form-item>
    </component>
  </teleport>
</template>

<script>
  import {computed, ref, useAttrs, onMounted, inject} from "vue";
  import {baseProps} from '../Composition/FormField';
  import FixedActionBar from "../../FixedActionBar"
  import {useModal} from "../../Modal";

  export default {
    name: 'FooterField',
    components: {
      FixedActionBar
    },
    props: {
      ...baseProps,
      submitBtn: {
        type: [Object, Boolean],
        default: false
      },
      resetBtn: {
        type: [Object, Boolean],
        default: false
      },
      cancelBtn: {
        type: [Object, Boolean],
        default: false
      },
      showCancel: {
        type: Boolean,
        default: false
      },
      showReset: {
        type: Boolean,
        default: true
      },
      fixed:{
        type: Boolean,
        default: false
      },
      width:{
        type: String,
        default: '100%'
      }
    },
    setup(props) {
      const formItemStyle = computed(() => {
        const style = {}
        if(props.width){
          style.width = props.width
        }
        return style
      })
      const attrs = useAttrs();
      const value = ref('');
      const def = ref(null);
      const form = inject('form')


      const {currentModal, close} = useModal();
      const modalId = computed(() => {
        if (currentModal && currentModal.modalId  && props.fixed) {
          return currentModal.modalId;
        }
        return "app";
      });


      const footerComponent = computed(() => {
        if(modalId.value === 'app' && props.fixed ){
          return 'fixed-action-bar'
        }
        return 'div'
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
      const resetField = () => {
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

      const submit = () => {
        form.emit('submit')
      };
      const reset = () => {
        form.emit('reset')
      };
      const cancel = () => {
        form.emit('cancel')
      };
      return {
        handleBlur,
        resetField,
        validate,
        cancel,
        reset,
        submit,
        labelProps,
        attrs,
        value,
        def,
        modalId,
        footerComponent,
        formItemStyle
      }
    },
  }
</script>
<style lang="scss" scoped>

</style>
