<template>
  <div class="icons-container">
    <el-tabs>
      <el-tab-pane label="QuickIcons" v-if="false">
        <el-row :gutter="10">
          <el-col :span="24">
            <el-form label-width="80px">
              <el-form-item label="图标名称">
                <el-input v-model="searchKey" style="width:350px;" />
                <el-button type="primary" @click="search">搜索</el-button>
              </el-form-item>
            </el-form>

          </el-col>
          <el-col
            v-for="item of quickIcons"
            :key="item.name"
            :span="3"
            @click="handleClipboard(generateIconCode('qa-icon-' +item.font_class),$event)"
          >
            <el-card shadow="hover">
              <svg-icon :icon="'qa-icon-' + item.font_class" :size="size" />
            </el-card>
            <div class="icon-text"> {{ item.font_class }}</div>
          </el-col>
        </el-row>
        <el-pagination
          :current-page="page"
          :page-sizes="[100, 200, 300, 400]"
          :page-size="pageSize"
          layout="total, sizes, prev, pager, next, jumper"
          :total="this.iconList.length"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </el-tab-pane>
      <el-tab-pane label="Element-UI Icons">

        <el-row :gutter="10">
          <el-col
            v-for="item of elementIcon"
            :key="item"
            :span="3"
            @click="handleClipboard(generateIconCode('el-icon-' + item),$event)"
          >
            <el-card shadow="hover">
              <quick-icon :icon="'el-icon-' + item" :size="size" />
            </el-card>
            <div class="icon-text"> {{ item }}</div>
          </el-col>
        </el-row>
      </el-tab-pane>
      <el-tab-pane label="SvgIcons" >

        <el-row :gutter="10">
          <el-col
            v-for="item of svgIcons"
            :key="item"
            :span="3"
            @click="handleClipboard(generateIconCode('icon-'+item),$event)"
          >
            <el-card shadow="hover" class="icon-item">
              <quick-icon :icon="'icon-'+item" :size="size" />
            </el-card>
            <div class="icon-text"> {{ item }}</div>
          </el-col>
        </el-row>
      </el-tab-pane>
      <el-tab-pane label="ColorfulSvgIcons">

        <el-row :gutter="10">
          <el-col
            v-for="item of ColorfulSvgIcons"
            :key="item.name"
            :span="3"
            @click="handleClipboard(generateColofulIconCode(item.font_class),$event)"
          >
            <el-card shadow="hover" class="icon-item">
              <quick-icon :icon="item.font_class" icon-type="colorful" :size="size" />
            </el-card>
            <div class="icon-text"> {{ item.name }}</div>
          </el-col>
        </el-row>
      </el-tab-pane>
    </el-tabs>
  </div>

</template>

<script>

  import svgIcons from './svg-icons'
  import * as ElementIcons from '@element-plus/icons'
  // import { css_prefix_text, glyphs } from '@/assets/iconfont/iconfont.json'

  export default {
    name: 'Icons',
    data() {
      return {
        svgIcons,
        elementIcon:[],
        ColorfulSvgIcons: [
          {
            name: 'approval',
            font_class: 'https://cdn.jsdelivr.net/gh/chuzhixin/zx-colorful-icon@master/approval.svg'
          }
        ],
        // iconList: glyphs,
        // css_prefix_text,
        page: 1,
        pageSize: 40,
        searchKey: '',
        size: 23,
      }
    },
    computed: {
      // quickIcons() {
      //   const list = this.$lodash.chunk(this.iconList, this.pageSize)
      //   return list[this.page - 1]
      // }
    },
    mounted() {
      console.log('ElementIcons', ElementIcons)

      const icons = [];
      for (var key in ElementIcons) {
        icons.push(ElementIcons[key].name)
      }
      this.elementIcon = icons
      // console.log('glyphs', glyphs)
      // console.log('svgIcons', svgIcons)
    },
    methods: {

      search() {
        if (this.searchKey) {
          this.iconList = this.$lodash.filter(glyphs, (item) => {
            return (item.font_class.toLowerCase().indexOf(this.searchKey.toLowerCase()) !== -1)
          })
        } else {
          this.iconList = glyphs
        }
      },
      generateIconCode(symbol) {
        return symbol
      },
      generateColofulIconCode(symbol) {
        return symbol
      },
      handleClipboard(text, event) {

        this.$emit('selected', text)
      },
      handleSizeChange(val) {
        this.pageSize = val
      },
      handleCurrentChange(val) {
        this.page = val
      }
    }
  }
</script>

<style lang="scss" scoped>
  .icons-dialog{

  }
  .icons-container {
    background-color: #FFFFFF;
    padding:0 20px;
    overflow: hidden;

    :deep .el-card {
      margin-bottom: 20px;

      .el-card__body {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }
    }

    :deep .el-tabs__content{
      height:400px;
      overflow-y: auto;
    }

    .icon-text {
      height: 30px;
      margin-top: -15px;
      overflow: hidden;
      font-size: 12px;
      line-height: 30px;
      text-align: center;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .icon-item {
      font-size: 23px;
      color: #24292e;
    }

    span {
      display: block;
      font-size: 16px;
      margin-top: 10px;
    }

    .disabled {
      pointer-events: none;
    }
  }
</style>
