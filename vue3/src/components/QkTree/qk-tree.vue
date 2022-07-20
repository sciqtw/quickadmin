<template>
  <div class="qk-tree">
    <slot name="header">
      <div class="header">
        <div>
          {{title}}
        </div>
        <div style="display: flex">
          <json-render
            v-if="headerAction && headerAction.component"
            :render-data="headerAction"
            @response="refresh"
          />
        </div>
      </div>

    </slot>
    <el-input
      style="padding:6px;"
      v-if="search"
      v-model="nodeQuery"
      :placeholder="searchPlaceholder"
      @input="onQueryChanged"
    ></el-input>
    <div class="tree-header">
      <div>
        <el-checkbox v-model="selectAll" v-if="showCheckAll">选中全部</el-checkbox>
        <el-checkbox v-model="openAll" v-if="showExpandAll">展开全部</el-checkbox>
      </div>
    </div>

    <el-scrollbar :height="height" v-loading="loading">
      <el-tree-v2
        :data="treeData"
        :filter-method="filterMethod"
        @current-change="handleChange"
        :expand-on-click-node="expandOnClickNode"
        ref="v2Tree"
        highlight-current
        :current-node-key="currentKey"
        :show-checkbox="showCheckbox"
        :key="key"
        :props="propsKeys"
        :default-expanded-keys="defaultExpandedKeys"
        :default-checked-keys="defaultCheckedKeys"
      >
        <template #default="{ node,data }">
          <slot :node="node" :data="data">
            <template v-if="data.display">
              <json-render
                :render-data="data.display"
                :value-key="valueKey"
                :label-key="labelKey"
                :children-key="childrenKey"
                :disabled-key="disabledKey"></json-render>
            </template>
            <template v-else-if="display?.component">
              <json-render
                :render-data="display" @refresh="refresh"
                :data="data"
                :value-key="valueKey"
                :label-key="labelKey"
                :children-key="childrenKey"
                :disabled-key="disabledKey"
                :node="node"></json-render>
            </template>
            <template v-else>
              {{node.data[labelKey]}}
            </template>
          </slot>
        </template>
      </el-tree-v2>
    </el-scrollbar>
    <slot name="footer"></slot>
  </div>
</template>

<script>

  import {ref, computed, onBeforeMount,onMounted, watch, getCurrentInstance} from 'vue'

  export default {
    name: "qk-tree",
    props: {
      height: {
        type: [Number, String],
        default: ''
      },
      title: {
        type: [String],
        default: ''
      },
      data: {
        type: Array,
        default: () => []
      },
      searchPlaceholder: {
        type: String,
        default: ''
      },
      display: {
        type: Object,
        default: () => {
        }
      },
      search: {
        type: Boolean,
        default: false
      },
      showCheckAll: {
        type: Boolean,
        default: false
      },
      showExpandAll: {
        type: Boolean,
        default: false
      },
      showCheckbox: {
        type: Boolean,
        default: false
      },
      isFilter: {
        type: Boolean,
        default: false
      },
      tabKey: {
        type: String,
        default: 'tab_key'
      },
      refreshKey: {
        type: String,
        default: 'repage'
      },
      default: {
        type: [Number, String],
        default: ''
      },
      lazy: {
        type: String,
        default: ''
      },
      headerAction: {
        type: Object,
        default: () => {
        }
      },
      defaultAllOpen: {
        type: Boolean,
        default: false
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
      defaultKeys: {
        type: Array,
        default: () => []
      },
      expandOnClickNode:{
        type: Boolean,
        default: false
      },
    },
    setup(props) {

      const {proxy} = getCurrentInstance()
      let allKeys = [];
      let parentKeys = [];
      const key = ref(1)
      const loading = ref(false)
      const treeData = ref([])
      const currentKey = ref(null)
      const v2Tree = ref(null)

      const propsKeys = computed(() => {
        return {
          value: props.valueKey,
          label: props.labelKey,
          children: props.childrenKey,
          disabled: props.disabledKey,
        }
      })

      const defaultCheckedKeys = ref([]);
      const defaultExpandedKeys = ref([]);

      const selectAll = ref(true)
      const openAll = ref(true)

      const getAllKeys = function (data, keys, parentKeys) {
        data.forEach((item) => {
          if (item[props.childrenKey] && item[props.childrenKey].length) {
            parentKeys.push(item[props.valueKey])
            getAllKeys(item[props.childrenKey], keys, parentKeys)
          }
          if (!item[props.disabledKey]) {
            keys.push(item[props.valueKey])
          }
        })

      }

      const getDataKeys = function () {
        let allKeys = [];
        let parentKeys = [];
        getAllKeys(treeData.value, allKeys, parentKeys)
        return {
          'allKeys': allKeys,
          'parentKeys': parentKeys,
        };
      }

      const initKeys = function () {
        let keys = getDataKeys()
        allKeys = keys['allKeys']
        parentKeys = keys['parentKeys']

        if (openAll.value) {

          defaultExpandedKeys.value = parentKeys
        }

        if (props.isFilter && proxy.$route.query[props.tabKey]) {
          currentKey.value = +proxy.$route.query[props.tabKey]
        } else {
          currentKey.value = props.default
        }
      }

      onBeforeMount(() => {

        treeData.value = props.data
        openAll.value = props.defaultAllOpen
        if (props.lazy) {
          getLazyData()
        }

        initKeys()

        defaultCheckedKeys.value = props.defaultKeys


      })


      const getLazyData = function () {
        loading.value = true
        Quick.request({
          url: props.lazy,
          param: props.lazy,
        }).then((res) => {
          if (res.code === 0) {
            treeData.value = res.data
            initKeys()
            key.value++
          }
        }).finally(() => {
          loading.value = true
        })
      }

      const refresh = function () {
        getLazyData()
      }

      watch(openAll, (val) => {

        if (val) {
          defaultExpandedKeys.value = parentKeys
        } else {
          defaultExpandedKeys.value = []
        }
        defaultCheckedKeys.value = v2Tree.value?.getCheckedKeys()
        key.value++
      })

      watch(selectAll, (val) => {

        if (val) {
          v2Tree.value?.setCheckedKeys(allKeys)
        } else {
          v2Tree.value?.setCheckedKeys([])
        }
      })

      const getCheckedKeys = function () {
        return v2Tree.value?.getCheckedKeys()
      }



      const nodeQuery = ref('')
      const onQueryChanged = () => {
        v2Tree.value?.filter(nodeQuery.value)
      }
      const filterMethod = (query, node) => {
        return node.label?.indexOf(query) !== -1
      }


      const updateQueryString = (value) => {
        proxy.$router.push({query: value})
      }
      const handleChange = function (e) {
        if (props.isFilter) {
          updateQueryString({
            [props.tabKey]: e[props.valueKey],
            [props.refreshKey]: Math.random()
          })
        }
      }

      return {
        onQueryChanged,
        filterMethod,
        handleChange,
        refresh,
        getCheckedKeys,
        currentKey,
        treeData,
        defaultCheckedKeys,
        defaultExpandedKeys,
        key,
        nodeQuery,
        v2Tree,
        openAll,
        selectAll,
        propsKeys
      }
    }
  }
</script>

<style lang="scss" scoped>
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px;
  }

  /*:deep .el-tree-node__content{*/
  /*    height: 40px;*/
  /*}*/
  /*:deep .el-tree-node{*/
  /*    height: 40px;*/
  /*}*/
  .qk-tree {
    background-color: #FFFFFF;
  }

  .tree-header {
    padding: 6px;
    display: flex;
    justify-content: space-between;
  }
</style>
