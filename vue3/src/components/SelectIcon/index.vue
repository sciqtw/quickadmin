<template>
  <span class="icons-dialog">
    <el-dialog
      title="选择图标"
      append-to-body
      :lock-scroll="false"
      width="600px"
      class="icons-diglog"
      v-model="dialogVisible"
    >
     <quick-icons @selected="handleClipboard"></quick-icons>

    </el-dialog>
    <span @click="dialogVisible = true">
      <slot />
    </span>
  </span>

</template>

<script>

import icons from './icons'
import svgIcons from './svg-icons'
import * as ElementIcons from '@element-plus/icons'
// import { css_prefix_text, glyphs } from '@/assets/iconfont/iconfont.json'

export default {
  name: 'Icons',
  components:{
    'quick-icons':icons
  },
  data() {
    return {
      dialogVisible: false
    }
  },
  methods: {
    handleClipboard(text, event) {
      this.dialogVisible = false
      this.$emit('selected', text)
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
