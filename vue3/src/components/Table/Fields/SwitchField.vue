<template>
  <div
    class="index-text"
  >
    <!--    直接展示-->
    <el-switch
      class="q-switch"
      v-model="fieldValue"
      v-bind="attributes"
      @change="changeValue"
    >
  </el-switch>

  </div>
</template>

<script>
export default {
  name: 'SwitchField',
  props: {
    rowData: {
      type: Object,
      default: () => {}
    },
    value: {
      type: [String,Number,Boolean],
      default: 0
    },
    switch:{
      type: Object,
      default: () => {}
    },
    submitUrl:{
      type:String,
      default:''
    },
    loadUrl:{
      type:String,
      default:''
    },
    authorizedToEdit:{
      type:Boolean,
      default:false
    },
    keyName:{
      type:String,
      default:''
    },
    colName:{
      type:String,
      default:''
    },
    keyValue:{
      type:String,
      default:''
    },
  },
  data() {
    return {
      isEditIng: false,
      formData: {
        colValue: ''
      },
      fieldValue:0,
      loading: false,
      extendContent: false,
      lock: false,
      keyField: '_key'
    }
  },
  computed: {

    attributes() {
      // const attrs = this.display.extraAttrs
      return {
        ...this.switch
      }
    }
  },
  created() {
    this.fieldValue = this.value
  },
  methods: {

    changeValue() {
      this.submitRequest()
    },
    submitRequest() {
      window.Quick.request({
        method: 'post',
        url: this.submitUrl,
        params: {
          _action: 'handle'
        },
        data: {
          [this.keyName]: this.keyValue,
          [this.colName]: this.fieldValue
        }
      }).then(response => {
        if (!response.code) {
          this.$emit('refresh')
        }
      }).catch(error => {
        this.$message({ message: error, type: 'error' })
      })
    }
  }
}
</script>
<style lang="scss" scoped>
  .q-switch{

    :deep .el-switch__label--left {
      position: absolute;
      min-width:calc(100% - 21px);
      text-align:left;
      left: 21px;
      color: #fff;
      z-index: -1111;
    }
    :deep .el-switch__core {
      flex-shrink:0;
    }
    :deep .el-switch__label--right {
      position: absolute;
      min-width:calc(100% - 21px);
      color: #fff;
      text-align:right;
      right:21px;
      z-index: -1111;
    }
    :deep .el-switch__label--right.is-active {
      z-index: 1;
      display:block;
      color: #fff !important;
    }
    :deep .el-switch__label--left.is-active {
      z-index: 1;
      display:block;
      color:  #fff !important;
    }
  }


</style>
