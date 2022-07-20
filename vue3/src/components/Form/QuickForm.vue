<template>
  <form-render
    ref="gform"
    v-bind="attributes"
    :key="fieldList"
    class="quick-form"
    @submit="submit"
    @reset="reset"
    @cancel="cancel"
  >

    <template v-slot:footer v-if="showFooterItem">
      <teleport :to="'#' + modalId" :disabled="!fixedFooter">
        <component :is="(modalId === 'app' && fixedFooter)  ? 'fixed-action-bar' : 'div'">
          <el-form-item class="footer-button">
            <el-button type="primary" @click="submit"  :loading="requestLoading">{{__('Submit')}}</el-button>

            <template v-if="showReset">
              <!--              <json-render v-if="resetBtn" :render-data="resetBtn" @click="reset"/>-->
              <el-button type="info" @click="reset">{{__('Reset')}}</el-button>
            </template>

            <template v-if="showCancel">
              <el-button @click="cancel">{{ __('Cancel')}}</el-button>
            </template>

          </el-form-item>
        </component>

      </teleport>
    </template>


  </form-render>
</template>

<script>
  import {
    nextTick,
    ref,
    inject,
    useAttrs,
    computed, reactive, onMounted, onUnmounted,
  } from 'vue';

  import {useModal} from "../Modal/index";
  import FixedActionBar from "../FixedActionBar"
  import {useAction} from "./../../utils/handleAction";

  export default {
    components: {
      FixedActionBar
    },
    props: {
      submitUrl: {
        type: String,
        default: ''
      },
      extendData: {
        type: Object,
        default: () => {
        }
      },
      // 禁用所有表单
      disabled: {
        type: Boolean,
        default: false
      },
      showFooter: {
        type: Boolean,
        default: true
      },
      fieldList: {
        type: [Array, Object],
        default: () => []
      },
      fixedFooter: {
        type: Boolean,
        default: false,
      },
      showCancel: {
        type: Boolean,
        default: true
      },
      showReset: {
        type: Boolean,
        default: true
      },
    },
    setup(props, {emit}) {


      const attrs = useAttrs();

      const gform = ref(null);
      const requestLoading = ref(false);


      /**
       * 扩展属性
       */
      const attributes = computed(() => {

        return {
          ...attrs,
          disabled:props.disabled,
          fieldList: props.fieldList
        };
      });


      const resourceRequestQueryString = computed(() => {
        return Object.assign({}, _.get(this.$route, 'query', {}));
      });

      const setErrors = (errors) => {
        this.$refs.form.setErrors(errors);
      };


      const {action, setData, setParams} = useAction({
        params: {}, // 请求参数
        data: {}, // post请求数据，
      });
      const panel = inject('response');
      const {currentModal, close} = useModal();
      const modalId = computed(() => {
        if (currentModal && currentModal.modalId) {
          return currentModal.modalId;
        }
        return "app";
      });

      /**
       * 关闭modal
       */
      const closeModal = (event, data) => {
        if (modalId.value !== 'app' && modalId.value !== 'none') {
          close(event, data);
        } else if (panel) {
          panel.emit(event, data)
        } else {
          action(data)
        }
        emit(event, data)
      }


      const showFooter = ref(false);
      nextTick(() => {
        showFooter.value = true;
      })


      const cancel = () => {
        closeModal('cancel', {})
      };


      const loading = () => {
        requestLoading.value = true;
      };

      const finishLoading = () => {
        requestLoading.value = false;
      };
      /**
       * 重置表单
       */
      const reset = () => {
        gform.value.resetForm();
      };

      /**
       *
       * @param mode
       */
      const submit = (mode) => {
        // console.log('--------submit---data', gform.value.formData())
        gform.value.validateForm(function (e) {

          if (e && e.length) {
            // console.log('--------------------validateForm', e);
            Quick.message({
              message: e[0].message,
              type: 'error'
            });
            return false;
          }
          const fromInfo = Object.assign({}, props.extendData, gform.value.formData());
          if (props.submitUrl) {
            // form 管理数据提交
            submitData(fromInfo);
          } else {
            closeModal('submit', fromInfo);
            // form 不做数据提交只负责收集数据

          }

        });

      };



      const submitData = (param) => {
        if (requestLoading.value) {
          return;
        }
        loading();
        Quick.request({
          method: 'POST',
          url: props.submitUrl,
          params: {},
          data: param
        }).then(response => {
          finishLoading();
          if (!response.code) {
            if (response.action) {
              closeModal('action', response.action);
            } else {
              Quick.message({
                message: response.msg,
                type: 'success'
              });
              closeModal('submit', response);
            }
          }
        }).catch(error => {

          // closeModal('error', error);

          finishLoading();
          // if (error.response.status === 422) {
          //   setErrors(error.response.data.data.errors);
          //   Quick.message({
          //     message: this.__('There was a problem submitting the form.'),
          //     type: 'error'
          //   });
          // }
        });
      };


      const showFooterItem = ref(false)
      onMounted(() => {
        if (props.showFooter) {
          showFooterItem.value = true
        }


      })





      return {
        attributes,
        requestLoading,
        gform,
        modalId,
        showFooterItem,
        submit,
        reset,
        cancel
      }

    }
  }


</script>

<style lang="scss" scoped>

  .el-dialog__footer {
    .footer-button {
      margin-bottom: 0 !important;

      :deep .el-form-item__content {
        justify-content: flex-end !important;
      }
    }
  }

  .quick-form {
    .footer-button {
      margin-bottom: 0;
    }

    border-radius: 3px;
    padding: 30px 15px;
  }

</style>
