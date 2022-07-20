<template>
  <default-field
    v-model="value"
    v-bind="labelProps"
    @reset="resetField"
    ref="def"
  >
    <div class="routeList-box">
      <div class="tree-header">
        <el-checkbox v-model="selectAll">选中全部</el-checkbox>
        <el-checkbox v-model="openAll">展开全部</el-checkbox>
      </div>
      <el-tree
        ref="tree"
        class="el-tree"
        v-bind="attrs"
        :data="data"
        show-checkbox
        :node-key="nodeKey"
        highlight-current
        :props="defaultProps"
        :default-expand-all="openAll"
        :default-checked-keys="value"
        :render-content="renderContent"
        @node-expand="handleExpand"
      />
    </div>
  </default-field>
</template>

<script>
  import {computed, ref, useAttrs, onMounted,getCurrentInstance} from "vue";
  import {baseProps} from '../Composition/FormField';

export default {
  name: 'TreeField',
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
    nodeKey:{
      type: String,
      default: 'id'
    }
  },
  data() {
    return {
      defaultProps: {
        children: 'children',
        label: 'title'
      },
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
    const fill = function(formData) {
      // console.log('------------fill',value.value,)
      formData[props.column] = JSON.stringify(proxy.$refs.tree.getCheckedKeys())
      return formData
    }

    onMounted(() => {
      value.value = initialValue();
    })



    return {
      resetField,
      labelProps,
      tree,
      attrs,
      value,
      def,
      fill,
    }
  },
  computed: {
    fieldValue() {
      return this.$refs.tree.getCheckedKeys()
    },
    isReadonly() {
      return this.readonlyField
    },

  },
  watch: {
    openAll() {
      this.setExpandNodes()
    },
    selectAll() {
      this.checkAll()
    }
  },
  mounted() {
    this.openAll = this.expandAll
    this.handleExpand()
  },
  methods: {

    checkAll() {
      if (this.selectAll) {
        this.$nextTick(() => {
          this.$refs.tree.setCheckedNodes(this.data)
        })
      } else {
        this.$nextTick(() => {
          this.$refs.tree.setCheckedKeys([])
        })
      }
    },
    setExpandNodes() {
      this.defaultExpand = true // 展开所有节点
      for (var i = 0; i < this.$refs.tree.store._getAllNodes().length; i++) {
        this.$refs.tree.store._getAllNodes()[i].expanded = this.openAll
      }
    },
    /**
     * 设置字段的初始值
     */
    setInitialValue() {
      this.value = !(this.default === undefined || this.default === null)
        ? this.default
        : []
    },
    handleExpand() { // 节点被展开时触发的事件
      // 因为该函数执行在renderContent函数之前，所以得加this.$nextTick()
      this.$nextTick(() => {
        this.changeCss()
      })
    },
    renderContent(h, { node, data, store }) { // 树节点的内容区的渲染 Function
      let classname = ''
      if (node.parent.data.inline) {
        classname = 'foo'
      }
      return h(
        'p',
        {
          class: classname
        },
        node.label
      )
    },
    changeCss() {
      var levelName = document.getElementsByClassName('foo') // levelname是上面的最底层节点的名字
      for (var i = 0; i < levelName.length; i++) {
        // cssFloat 兼容 ie6-8  styleFloat 兼容ie9及标准浏览器
        levelName[i].parentNode.style.cssFloat = 'left' // 最底层的节点，包括多选框和名字都让他左浮动
        levelName[i].parentNode.style.styleFloat = 'left'
        levelName[i].style.minWidth = this.minWidth
        levelName[i].parentNode.onmouseover = function() {
          this.style.backgroundColor = '#fff'
        }
      }
    }
  }
}
</script>
<style lang="scss" scoped >
  .tree-header{
    padding-left:15px;
  }
  .routeList-box {
    border: 1px solid #eee;
    margin-bottom: 20px;
    .el-tree {
      margin: 12px 0;
    }

    :deep .el-collapse-item__header {
      height: 40px;
      background-color: #f4f4f4;
      padding: 0 10px;
    }
    :deep .el-icon-arrow-right:before {
      color: #409eff;
    }
  }
  :deep  .el-tree-node__content::before {
    content: "";
    padding-left: 10px;
  }
  :deep .el-checkbox__input {
    margin-right: 6px;
  }

  .tree-line{
    :deep .el-tree-node {
      position: relative;
      padding-left: 16px; // 缩进量
    }
    :deep .el-tree-node__children {
      padding-left: 16px; // 缩进量
    }

    // 竖线
    :deep .el-tree-node::before {
      content: "";
      height: 100%;
      width: 1px;
      position: absolute;
      left: -3px;
      top: -26px;
      border-width: 1px;
      border-left: 1px dashed #52627C;
    }
    // 当前层最后一个节点的竖线高度固定
    :deep .el-tree-node:last-child::before {
      height: 38px; // 可以自己调节到合适数值
    }

    // 横线
    :deep .el-tree-node::after {
      content: "";
      width: 24px;
      height: 20px;
      position: absolute;
      left: -3px;
      top: 12px;
      border-width: 1px;
      border-top: 1px dashed #52627C;
    }

    // 去掉最顶层的虚线，放最下面样式才不会被上面的覆盖了
    :deep & > .el-tree-node::after {
      border-top: none;
    }
    :deep & > .el-tree-node::before {
      border-left: none;
    }

    // 展开关闭的icon
    .el-tree-node__expand-icon{
      font-size: 16px;
      // 叶子节点（无子节点）
      &.is-leaf{
        color: transparent;
        // display: none; // 也可以去掉
      }
    }
  }
</style>
