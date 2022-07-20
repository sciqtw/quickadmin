<template>
  <div class="tree-item">
    <div>{{data.label}}</div>
    <div class="tree-btn" v-if="actionList.length && data[valueKey]">
      <el-dropdown>
                <span class="el-dropdown-link tree-btn-icon">
                    <quick-icon rotate="90" icon="el-icon-MoreFilled"></quick-icon>
                </span>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item v-for="(action,index) in actionList" :key="index" @click.stop="">
              <json-render
                :render-data="action"
                :data="{ id:data[valueKey] }"
                :params="{ id:data[valueKey] }"
                :node="node"
                @response="actionExecuted"
              />
            </el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    </div>
  </div>
</template>

<script>
  import {computed} from 'vue'

  export default {
    name: "qk-tree-item",
    props: {
      data: {
        type: Object,
        default: () => {
        }
      },
      params: {
        type: Object,
        default: () => {
        }
      },
      node: {
        type: Object,
        default: () => {
        }
      },
      actions: {
        type: Array,
        default: () => []
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
    },
    setup(props, {emit}) {

      const actionExecuted = function (action, data) {
        console.log('----actionExecuted--', action, data)
        emit(action, data)
      }

      const actionList = computed(() => {

        let list = []
        props.actions.map((item) => {
          if (!item.level || item.level > props.node.level) {
            list.push(item)
          }
        })
        return list
      })

      return {
        actionExecuted,
        actionList
      }
    }

  }
</script>

<style lang="scss" scoped>

  .is-current {
    .tree-item .tree-btn {
      display: block;
    }
  }

  .tree-item:hover {
    .tree-btn {
      display: block;
    }
  }

  .tree-item {
    color: #8f8f8f;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-right: 10px;


    .tree-btn {
      /*margin-left: 60px;*/
      display: none;

      .tree-btn-icon {
        padding-left: 15px;
      }

    }


  }
</style>
