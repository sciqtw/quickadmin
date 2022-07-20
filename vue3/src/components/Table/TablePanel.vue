<template>
  <div class="table-panel">
    <slot name="header"></slot>
    <slot name="chart"/>

    <el-card v-if="showFilter" shadow="never" class="box-card box-card-bottom-0 ">
      <filter-form
        :data="filter"
        :key="filter"
        @submit="handleFilterSubmit"
      />
    </el-card>
    <slot name="filter"/>

    <slot name="cardInfo"/>

    <el-card shadow="never" class="box-card table-card">

      <quick-table
        v-loading="showLoading"
        v-bind="elTableAttrs"
        :lists="tableLists"
        :total="tableTotal"
        :current-page="currentPage"
        :default-sort="sortData"
        @refresh="refresh"
        @sort-change="handleSortChange"
        @filter-change="filterChange"
        @expand-change="expandChange"
        @size-change="handleSizeChange"
        @page-change="handleCurrentChange"
      />

    </el-card>
    <slot name="footer"/>
  </div>

</template>

<script>
  import InteractsWithQueryString from '@/mixins/InteractsWithQueryString'

  export default {
    name: 'TablePanel',
    mixins: [InteractsWithQueryString],
    props: {
      tableData: {
        type: Object,
        default: () => {
        }
      },
      loadUrl: {
        type: String,
        default: ''
      },
      showFilter: {
        type: Boolean,
        default: false
      },
      filter: {
        type: Object,
        default: () => {
        }
      },
      refreshKey: {
        type: String,
        default: 'repage'
      },
    },
    data() {
      return {
        listLoading: 0,
        perPage: 0,
        page: 1,
        search: '',
        lists: [],
        total: 0,
        sortData: {
          prop: '',
          order: ''
        },
        urlKey: '',

      }
    },
    computed: {
      getDataUrl() {
        if (!this.loadUrl) {
          return `${this.resolveModeluName}/resource/${this.resource}/index`
        }
        return this.loadUrl
      },
      resource() {
        return this.$route.params.resourceName || ''
      },
      moduleName() {
        return this.$route.params.moduleName || ''
      },
      showLoading() {
        return this.listLoading === 1
      },
      tableTotal() {
        return this.total
      },
      tableLists() {
        return this.lists
      },
      /**
       * el-table 属性
       */
      elTableAttrs() {
        const attrs = {
          ...this.tableData.props,
        }
        return {
          ...attrs
        }
      },
      resolveModeluName() {
        return this.moduleName.replace('.', '/')
      },

      /**
       * Build the resource request query string.
       */
      resourceRequestQueryString() {
        const param = {
          per_page: this.currentPerPage,
          page: this.currentPage,
        }
        return param
      },

      /**
       * 当前页
       */
      currentPage() {
        return parseInt(this.filterData.page || this.page)
      },

      /**
       * 从查询字符串中获取当前每页的数值
       */
      currentPerPage() {
        return parseInt(this.filterData.per_page || this.perPage)
      },

      /**
       * 刷新
       */
      refreshPage() {
        return this.filterData.retable || ''
      }

    },
    watch: {
      tableData: {
        handler: function (val) {
          this.lists = val.props.lists
          this.total = val.props.total
        },
        immediate: true
      },
      filterData: function (val) {
        this.refresh()
      },
      immediate: true

    },
    mounted() {
      this.$watch(
        () => {
          return (
            this.refreshPage
          )
        },
        (e) => {
          const urlKey = this.$route.params.moduleName + this.$route.params.resourceName;
          // 防止路由跳转后当前页面重新执行一遍请求加载
          if (urlKey === this.urlKey) {
            this.refresh()
          }
        },
        {
          immediate: false
        }
      )
      this.urlKey = this.$route.params.moduleName + this.$route.params.resourceName

      this.filterData = Object.assign(this.filterData,this.$route.query);
      if(this.loadUrl){
        this.getInitData()
      }
    },
    activated() {
      this.getInitData(true)
    },
    methods: {

      /**
       * 刷新
       */
      refresh() {
        this.getInitData()
      },
      /**
       * 处理快捷搜索
       */
      performSearch(event) {
        this.$lodash.debounce(() => {
          // Only search if we're not tabbing into the field
          if (event.which !== 9) {
            this.updateFilter({
              page: 1,
              search: this.search
            })
          }
        })
      },

      /**
       * 设置每页显示数量
       */
      handleSizeChange(val) {
        this.updateFilter({
          per_page: val,
          page: 1
        })
      },
      handleFilterSubmit(e) {
        this.updateFilter(e)
      },
      /**
       * 显示第几页
       * */
      handleCurrentChange(val) {
        console.log('--page-', val)
        this.updateFilter({
          page: val
        })
      },
      handleSortChange(column) {
        this.sortData = column
        this.updateFilter({
          _sort: column.prop,
          _order: column.order
        })
        console.log('column, prop, order', column)
      },
      filterChange(filter) {
        console.log('filterChange----->', filter)
      },
      expandChange(row, expandedRows, index) {
        // row = {};
        // console.log('expandChange--row--->', row, index)
        // console.log('expandChange---expandedRows-->', expandedRows)
      },
      runLoading() {
        this.listLoading = 2
        // keep-alive 页面服务器加载速度够快时减少loading闪屏
        setTimeout(() => {
          if (this.listLoading === 2) {
            this.listLoading = 1
            setTimeout(() => {
              this.listLoading = 0
            }, 8000)
          }
        }, 500)
      },
      /**
       * 获取数据
       */
      getInitData(loadStyle) {
        if (loadStyle) {
          this.runLoading()
        } else {
          this.listLoading = 1
        }
        const queryParam = Object.assign({},
          this.filterData,
          this.resourceRequestQueryString
        )
        Quick.request().post(
          this.getDataUrl,
          queryParam,
        ).then((data) => {
          this.lists = data.data.data
          this.total = data.data.total
          this.$nextTick(() => {
            this.listLoading = 0
          })
        }).catch(() => {
          this.lists = []
          this.total = 0
          this.listLoading = 0
        })
      }
    }
  }
</script>

<style scoped lang="scss">

  .table-panel {
    /*background-color: #FFFFFF;*/
    .table-panel-heard {
      margin-left: 15px;
      margin-right: 15px;
    }
  }

  .table-tools {
    margin-bottom: 15px;
  }

  .margin-s-l {
    margin-left: 5px;
  }

  .box-card {
    border: 1px solid #FFFFFF;
  }

  .no-padding {
    :deep .el-card__body {
      padding: 0px;
    }
  }

  .box-card-bottom-0 {
    :deep .el-card__body {
      padding-bottom: 0px !important;
    }
  }

  .table-card {
    :deep .el-card__body {
      padding-top: 0px;
      padding-bottom: 10px;
    }
  }
</style>
