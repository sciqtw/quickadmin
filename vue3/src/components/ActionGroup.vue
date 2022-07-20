<template>
  <span>
    <!--    折叠起来的动作-->
    <template v-if="moreActions.length">
      <el-dropdown
        split-button
        type="primary"
        class="margin-s-r"
        :disabled="disabled"
      >
        {{ moreName }}
        <template #dropdown>
        <el-dropdown-menu >
          <el-dropdown-item
            v-for="(action,index) in moreActions"
            :key="index"
            :disabled="disabled"
          >
            <json-render
              :render-data="action"
              :data="postData"
              :params="params"
              :disabled="disabled"
              @response="actionExecuted"
            />
          </el-dropdown-item>
        </el-dropdown-menu>
        </template>
      </el-dropdown>
    </template>
    <!--    直接展示动作-->
    <template v-if="actions.length > 0">
      <json-render
        v-for="(action,index) in actions"
        :key="'left-tools' + action.uriKey + index"
        :render-data="action"
        :data="postData"
        :params="params"
        :disabled="disabled"
        @response="actionExecuted"
      />
    </template>

  </span>
</template>

<script>

import JsonRender from "./JsonRender";
export default {
  name: 'BatchAction',
  components: {JsonRender },
  props: {

    actionList: {
      type: Array,
      default: () => []
    },
    paramData: {
      type: [Array,Object],
      default: () => []
    },
    params: {
      type: Object,
      default: () => {}
    },
    disabled: {
      type: Boolean,
      default: false
    },
    moreName: {
      type: String,
      default: '更多'
    }
  },
  data() {
    return { }
  },
  computed: {
    resource(){
      return this.$route.params.resourceName || ''
    },
    moduleName(){
      return this.$route.params.moduleName || ''
    },
    postData(){
      return this.paramData
    },
    moreActions() {
      return _.filter(this.actionList, action => action.more === true)
    },
    actions() {
      return _.filter(this.actionList, action => action.more !== true)
    }
  },
  watch: {

  },
  async created() {

  },
  methods: {
    actionExecuted(event) {
      if (event !== 'cancel' && event !== 'close') {
        this.$emit('refresh')
      }
    }
  }
}
</script>

<style scoped lang="scss">
  .margin-s-r{
    margin-right:10px;
  }
</style>
