<template>
  <div
    ref="col-panel"
    class="index-action-column"
  >
    <!--    直接展示动作-->
    <template v-for="(action,index) in actions" :key="index">
      <el-divider v-if="index" :key="index" direction="vertical" />

      <json-render
        :render-data="action"
        :row-data="rowData"
        :params="{
                  ...rowData
                }"
        @response="actionExecuted"
      />
    </template>
    <!--    折叠起来的动作-->
    <el-divider v-if="actions.length && moreActions.length" direction="vertical" />
    <el-dropdown v-if="moreActions.length" :hide-on-click="false" class="margin-s-l">
        <span class="el-dropdown-link">
          {{__('More')}}<i class="el-icon-arrow-down el-icon--right" />
        </span>
      <template #dropdown>
        <el-dropdown-menu >
          <el-dropdown-item v-for="(action,index) in moreActions" :key="index">
            <json-render
              :render-data="action"
              :row-data="rowData"
              :params="{
                  ...rowData
                }"
              @response="actionExecuted"
            />
          </el-dropdown-item>
        </el-dropdown-menu>
      </template>

    </el-dropdown>
  </div>
</template>

<script>
export default {
  props: {
    rowData: {
      type: Object,
      default: () => {}
    },
    field: {
      type: Object,
      default: () => {}
    },
    actionList: {
      type: Object,
      default: () => {}
    }
  },
  data() {
    return {
      keyField: '_key'
    }
  },
  computed: {
    moreActions() {
      return _.filter(this.actionList, action => action.more === true)
    },
    actions() {
      return _.filter(this.actionList, action => action.more !== true)
    }
  },
  mounted() {},
  methods: {
    actionExecuted(event, params) {
      if (event !== 'cancel' && event !== 'close') {
        this.$emit('refresh')
      }
    }
  }
}
</script>
<style lang="scss" scoped>
  .index-action-column{
    :deep .el-divider--vertical{
      margin:0 2px;
    }
  }
  .el-dropdown-link {
    cursor: pointer;
    color: #409EFF;
  }
</style>
