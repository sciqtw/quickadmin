<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <qk-tree
      :data="data"
      :value-key="valueKey"
      :label-key="labelKey"
      :children-key="childrenKey"
      :disabled-key="disabledKey"
      :show-checkbox="true"
      :show-check-all="true"
      :show-expand-all="true"
      :expand-on-click-node="expandOnClickNode"
      :default-keys="value"
      :key="key"
      ref="tree"
      style="width:60%;"
    ></qk-tree>
  </default-field>
</template>

<script>
  import {computed, ref, useAttrs, onBeforeMount, getCurrentInstance} from "vue";
  import {baseProps} from '../Composition/FormField';

  export default {
    name: 'Tree2Field',
    props: {
      ...baseProps,
      data: {
        type: Array
      },
      expandAll: {
        type: Boolean,
        default: false
      },
      minWidth: {
        type: String,
        default: '90px'
      },
      valueKey: {
        type: String,
        default: 'id'
      },
      labelKey: {
        type: String,
        default: 'label'
      },
      childrenKey: {
        type: String,
        default: 'children'
      },
      disabledKey: {
        type: String,
        default: 'disabled'
      },
      expandOnClickNode:{
        type: Boolean,
        default: true
      },
    },
    data() {
      return {
        selectAll: false,
        openAll: false
      }
    },
    setup(props) {

      const attrs = useAttrs();
      const value = ref('');
      const def = ref(null);
      const tree = ref(null)
      const {proxy} = getCurrentInstance()
      const key = ref(1)

      /**
       * label
       */
      const labelProps = computed(() => {
        return {
          ...props,
          ...attrs
        };
      });


      /** 处理表单重置 **/
      const resetField = (data) => {

        if (data) {
          value.value = data;
          return;
        }
        value.value = initialValue();
        key.value++
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
       * 填充formArr
       * @param formData
       */
      const fill = function (formData) {

        formData[props.column] = JSON.stringify(proxy.$refs.tree.getCheckedKeys())
        return formData
      }

      onBeforeMount(() => {
        value.value = initialValue();
      })


      return {
        resetField,
        fill,
        labelProps,
        tree,
        attrs,
        value,
        key,
        def,
      }
    },
    computed: {},
    watch: {
      openAll() {
        // this.setExpandNodes()
      },
      selectAll() {
        this.checkAll()
      }
    },
    mounted() {
      this.openAll = this.expandAll
      // this.handleExpand()
    },
    methods: {}
  }
</script>
<style lang="scss" scoped>
  .tree-header {
    padding-left: 15px;
  }


</style>
