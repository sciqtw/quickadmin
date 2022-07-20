<template>
  <div class="quick-tab" :class="{'tab-mb-0':removeBottom}">
    <slot name="header"></slot>
    <el-tabs v-model="activeName" v-bind="attrs" @tab-change="tabChange">
      <el-tab-pane
        v-for="(pane,index) in panes" :key="index"
        v-bind="pane.props"
      >
        <template
          v-for="(child , index ) in pane.children"
          v-slot:[child.props.slot]
          :key="index"
        >
          <json-render
            :key="index"
            :render-data="child"
          />
        </template>
      </el-tab-pane>
    </el-tabs>
    <slot name="footer"></slot>
  </div>
</template>
<script>
import InteractsWithQueryString from '@/mixins/InteractsWithQueryString'
export default {
  name: 'QuickTabs',
  mixins: [InteractsWithQueryString],
  props: {
    removeBottom: {
      type: Boolean,
      default: false
    },
    default: {
      type: String,
      default: ''
    },
    tabKey: {
      type: String,
      default: 'tab_key'
    },
    refreshKey: {
      type: String,
      default: 'repage'
    },
    isFilter: {
      type: Boolean,
      default: false
    },
    panes: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      activeName: ''
    }
  },
  computed: {
    defaultAttrs() {
      return {
        ...this.$attrs
      }
    },

    attrs() {
      return {
        ...this.defaultAttrs
      }
    }
  },
  watch: {
    $route() {
      this.initTab()
    },
    default(val){
      this.activeName = this.default
    },
  },
  created() {
    this.activeName = this.default
  },
  mounted() {
    this.initTab()
  },
  methods: {
    initTab() {
      if (this.isFilter) {
        if (this.$route.query[this.tabKey]) {
          this.activeName = this.$route.query[this.tabKey]
        }
      }
    },
    activeTab(i) {
      this.activeName = i
    },
    /**
     * 处理filter
     * */
    tabChange(e) {
      // console.log('---------e',e,this.activeName)
      if (this.isFilter) {
        this.updateQueryString({
          [this.tabKey]: this.activeName,
          [this.refreshKey]: Math.random()
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>

  .quick-style-1{
    :deep(.el-tabs) {
      .el-tabs__header .el-tabs__nav {
        .el-tabs__active-bar {
          z-index: 0;
          width: 100%;
          background-color: #e1f0ff;
          border-right: 2px solid #409eff;
        }
        .el-tabs__item {
          text-align: left;
          /*padding-right: 100px;*/
        }
      }
      .el-tab-pane {
        padding: 0 20px 0 30px;
      }
    }
  }
  .tab-mb-0{
    :deep .el-tabs__header{
      margin-bottom: 0px;
    }
    :deep .el-tabs__header{
      width: 100%;
      margin-right:0px;
      margin-left:0px;
    }
    :deep.el-tabs__active-bar.is-left{
      width: 3px;
    }
  }
</style>
