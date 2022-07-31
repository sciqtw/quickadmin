<template>

<div>
    <el-tabs :tab-position="'top'"  v-model="activeName">
        <el-tab-pane label="CRUD" name="crud"> </el-tab-pane>
        <el-tab-pane label="创建Service" name="service"></el-tab-pane>
        <el-tab-pane label="创建API" name="api"></el-tab-pane>
    </el-tabs>
    <div v-if="activeName === 'crud'" >
        <create-crud></create-crud>
    </div>

    <div v-else-if="activeName === 'service'">
        <create-service></create-service>
    </div>
    <div v-else-if="activeName === 'api'">
        <create-api></create-api>
    </div>
</div>
</template>

<script>
  import QkCode from "../field/qk-code"
  import {ref} from 'vue'
  import CreateService from './create-service'
  import CreateCrud from './create-crud'
  import CreateApi from './create-api'
  export default {
    name: "code",
    components: {
      QkCode,
      CreateService,
      CreateCrud, CreateApi,
    },
    setup() {


      const activeName = ref('crud')


      return {
        activeName
      }
    }
  }
</script>

<style scoped>
  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .text {
    font-size: 14px;
  }

  .item {
    margin-bottom: 18px;
  }

  .box-card {
    width: 480px;
  }
</style>
