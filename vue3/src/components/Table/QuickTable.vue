<template>
  <div class="quick-table">
    <div class="table-header">
      <slot name="header" v-bind="exportData"/>
    </div>

    <slot name="top-tool">
      <el-row type="flex" justify="space-between" class="table-tools">
        <!--   左边工具 -->
        <el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
          <slot name="left-tool">

            <action-group
              v-if="batchActions && batchActions.actions.length > 0"
              :action-list="batchActions.actions"
              :param-data="{_keyValues_:selectedResources}"
              :params="{}"
              v-bind="batchActions.props"
              :more-name="'已选择' + selectedResources.length + '项'"
              :disabled="selectedResources.length == 0"
              @refresh="refresh"
              style="margin-right:10px;"
            />
            <action-group
              v-if="tableTools && tableTools.actions.length > 0"
              :action-list="tableTools.actions"
              v-bind="tableTools.props"
              :param-data="[]"
              :params="{}"
              @refresh="refresh"
              style="margin-right:10px;"
            />
          </slot>

        </el-col>
        <!--    右边工具  -->
        <el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">

          <el-row type="flex" justify="end" class="">
            <slot name="right-tool"/>

            <el-dropdown v-if="showColumnSelector" :hide-on-click="false" class="margin-s-l">


              <span class="el-dropdown-link">
                    <el-button icon="el-icon-setting"/>
                 </span>
              <template #dropdown>
                <el-dropdown-menu>
                  <el-checkbox-group
                    v-model="ableColumns"
                    :min="1"
                  >
                    <el-dropdown-item v-for="(column ,index) in columns" :key="column.name + index">
                      <el-checkbox

                        v-model="column.name"
                        :label="column.name"
                      >{{ column.title }}
                      </el-checkbox>
                    </el-dropdown-item>
                  </el-checkbox-group>
                </el-dropdown-menu>
              </template>

            </el-dropdown>
            <el-dropdown v-if="showSize" :hide-on-click="false" trigger="click" class="margin-s-l"
                         @command="handleSizeCommand">

                <span class="el-dropdown-link">
                    <el-button icon="el-icon-Operation"/></span>

              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item command="large"  :class="{'dropdown-active':size === 'large'}">{{__('Large')}}</el-dropdown-item>
                  <el-dropdown-item command="default" :class="{'dropdown-active':size === 'default' || size === ''}">{{__('Default')}}</el-dropdown-item>
                  <el-dropdown-item command="small" :class="{'dropdown-active':size === 'small'}">{{__('Small')}}</el-dropdown-item>
                </el-dropdown-menu>
              </template>

            </el-dropdown>
            <el-button
              v-if="showRefresh"
              class="margin-s-l"
              type="info"
              icon="el-icon-refresh-right"
              @click="refresh"
            />
          </el-row>

        </el-col>
      </el-row>

    </slot>

    <el-table
      v-if="columns.length"
      :key="key"
      ref="_table"
      v-loading="loading"
      :data="tableLists"
      v-bind="attrs"
      :highlight-current-row="showSelection && selectionType === 'radio'"
      @selection-change="handleSelectionChange"
      @current-change="handleTableCurrentChange"
    >
      <el-table-column
        v-if="showSelection && selectionType === 'radio'"
        label=""
        width="100"
      >
        <template #default="scope">
          <el-radio-group v-model="radioSelection" @change="handleSelectionChange(scope.row)">
            <el-radio :label="scope.row._key.value"></el-radio>
          </el-radio-group>

        </template>
      </el-table-column>
      <!--    id-->
      <el-table-column
        v-if="showSelection  && selectionType === 'checkbox'"
        type="selection"
        width="55"
      />
      <!--    内容栏-->
      <template v-for="(col ,index ) in showColumns" :key="index">
        <quick-column
          :column="col"
          @refresh="refresh"
          @actionExecuted="refresh"
        />
      </template>

    </el-table>
    <div class="table-footer">
      <slot name="footer" v-bind="exportData"/>
      <el-row v-if="showPaginate" type="flex" justify="space-between" style="padding-top:15px;">
        <el-pagination
          :current-page="currentPage"
          :total="total"
          v-bind="paginateProp"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
        <el-button  @click="handleConfirm" v-if="showConfirmBtn" :size="size" type="primary">{{__('Confirm')}}</el-button>
      </el-row>

    </div>
  </div>
</template>

<script>
  import ActionGroup from '@/components/ActionGroup'

  export default {
    name: 'QuickTable',
    components: {
      ActionGroup
    },
    props: {
      columns: {
        type: Array,
        default: () => []
      },
      visibleColumns: {
        type: [Array, Boolean],
        default: false
      },
      lists: {
        type: Array,
        default: () => []
      },
      tableSize: {
        type: String,
        default: ''
      },
      showSelection: {
        type: Boolean,
        default: false
      },
      paginate: {
        type: [Object, Boolean],
        default: false
      },
      batchActions: {
        type: [Object, Boolean],
        default: false
      },
      tableTools: {
        type: [Object, Boolean],
        default: false
      },
      showColumnSelector: {
        type: Boolean,
        default: false
      },
      showSize: {
        type: Boolean,
        default: false
      },
      showRefresh: {
        type: Boolean,
        default: false
      },
      showPaginate: {
        type: Boolean,
        default: false
      },
      total: {
        type: Number,
        default: 0
      },
      currentPage: {
        type: Number,
        default: 1
      },
      pageSize: {
        type: Number,
        default: 15
      },
      /**
       * 延迟加载数据长度
       * */
      maxLength: {
        type: Number,
        default: 20
      },
      /**
       * checkbox,radio
       **/
      selectionType:{
        type: String,
        default: 'checkbox'
      },
      showConfirmBtn: {
        type: Boolean,
        default: false
      },
    },
    data() {
      return {
        key: 1,
        checkedColumns: [],
        ableColumns: [],
        multipleSelection: [],
        size: '',
        loading: false,
        tableLists: [],
        height: '700',
        radioSelection:0,
      }
    },
    computed: {
      tableHeight() {
        return this.height
      },
      paginateProp() {
        let defaultProps = {
          'page-size': this.pageSize,
        }
        const paginate = this.paginate
        if (paginate) {
          defaultProps = Object.assign(
            {},
            defaultProps,
            paginate.props)
        }
        return defaultProps
      },
      selectedResources() {
        const ids = []
        _.each(this.multipleSelection, row => {
          ids.push(row._key.value)
        })
        // console.log('--selectedResources--',this.multipleSelection,ids)
        return ids
      },
      exportData() {
        return {
          'columns': this.columns,
          'multipleSelection': this.multipleSelection
        }
      },
      showColumns() {
        // return this.columns
        if (this.ableColumns === false || (Array.isArray(this.ableColumns) && !this.ableColumns.length)) {
          return this.columns
        }

        return this.columns.filter(i => this.ableColumns.indexOf(i.name) >= 0 || (i.name == null && i.children.length > 0))
      },
      /**
       *
       */
      defaultAttrs() {
        return {
          'element-loading-text': 'Loading',
          'size': this.size
          // 'height': this.tableHeight
        }
      },

      attrs() {
        const attrs = this.$attrs
        return {
          ...attrs,
          ...this.defaultAttrs
        }
      }
    },
    watch: {
      lists: {
        handler: function (lists) {
          this.tableLists = lists
          this.key++
        }
      }
    },
    mounted() {
      if (this.lists.length > this.maxLength) {
        this.loading = true
        setTimeout(() => {
          this.tableLists = this.lists
          this.$nextTick(() => {
            this.loading = false
          })
        }, 50)
      } else {
        this.tableLists = this.lists
      }
    },
    created() {
      this.$watch('visibleColumns', function (visibleColumns) {
        if (Array.isArray(visibleColumns) && visibleColumns.length) {
          this.ableColumns = visibleColumns
        } else {
          const arr = []
          _.each(this.columns, column => {
            arr.push(column.name)
          })
          this.ableColumns = arr
        }
      }, {
        immediate: true
      })

      this.size = this.tableSize
    },
    methods: {
      /**
       * 设置表格大小
       */
      handleSizeCommand(size) {
        this.size = size
      },
      /**
       * 设置每页显示数量
       */
      handleSizeChange(val) {
        this.$emit('size-change', val)
      },

      /**
       * 显示第几页
       * */
      handleCurrentChange(val) {
        this.$emit('page-change', val)
      },
      toggleRowExpansion() {
        // console.log('toggleRowExpansion--->', this.$refs._table)
        // this.$refs._table.toggleRowExpansion('149', true)
      },

      cellClick(row, column, cell, event) {
        // row.number = {}
        // this.$refs._table.toggleRowExpansion(row,true)
        // console.log('单元格被点击触发',row, column, cell, event)
      },
      /**
       * 刷新事件
       */
      refresh() {
        this.multipleSelection = []
        this.$emit('refresh')
      },

      /**
       * 处理表格多选
       * @param val
       */
      handleSelectionChange(val) {
        this.multipleSelection = val
        console.log('----multipleSelection--------',val)
        // this.$emit('update:multipleSelection', val)
      },
      handleTableCurrentChange(currentRow,oldCurrentRow){
        if(this.showSelection && this.selectionType === 'radio'){
          this.multipleSelection = [currentRow]
          this.radioSelection = currentRow._key.value
        }

        // console.log('----multipleSelection--------',currentRow,oldCurrentRow)
      },
      handleConfirm(){
        let values = [];
        this.multipleSelection.forEach((item) => {
          values.push({
            id:item._key.value,
            name:item.name.value,
          })
        })
        Quick.api.close('submit',values);
      }
    }
  }
</script>

<style lang="scss" scoped >



  .table-header {
    margin-bottom: 15px;
  }

  .table-tools {
    margin-bottom: 15px;
  }

  .margin-s-l {
    margin-left: 5px;
  }
</style>
