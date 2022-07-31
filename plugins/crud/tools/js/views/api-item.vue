<template>
    <el-tabs
      v-model="activeName"
      type="card"
      class="demo-tabs"
      @tab-click="handleClick"
    >
        <el-tab-pane label="文档" name="base">
            <el-descriptions title="基础信息" border column="2">
                <el-descriptions-item label="标题">{{apiData.title}}</el-descriptions-item>
                <el-descriptions-item label="Method">{{apiData.method}}</el-descriptions-item>
                <el-descriptions-item label="url">{{apiData.url}}</el-descriptions-item>
                <el-descriptions-item label="描述">{{apiData.summary}}</el-descriptions-item>
                <el-descriptions-item label="分组">{{apiData.Sector}}</el-descriptions-item>
            </el-descriptions>
            <el-divider content-position="left">请求头参数 Headers</el-divider>
            <el-table border size="small" :data="headers" style="width: 100%">
                <el-table-column prop="name" label="参数名" width="180" />
                <el-table-column prop="type" label="类型" width="180" />
                <el-table-column prop="required" label="必填" width="180" >
                    <template #default="scope">
                       <el-tag type="success" v-if="scope.row['required'] == true || scope.row['required'] == 'true'">是</el-tag>
                       <el-tag v-else type="info">否</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="description" label="说明" >
                    <template #default="scope">
                        <div v-if="scope.row['description']">说明：{{scope.row['description']}}</div>
                        <div v-if="scope.row['sample']">示例：{{scope.row['sample']}}</div>
                    </template>
                </el-table-column>
            </el-table>
            <el-divider content-position="left">请求参数</el-divider>
            <el-table border size="small" :data="params" style="width: 100%">
                <el-table-column prop="name" label="参数名" width="180" />
                <el-table-column prop="type" label="类型" width="180" />
                <el-table-column prop="required" label="必填" width="180" >
                    <template #default="scope">
                        <el-tag type="success" v-if="scope.row['required'] == true || scope.row['required'] == 'true'">是</el-tag>
                        <el-tag v-else type="info">否</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="description" label="说明" >
                    <template #default="scope">
                        <div v-if="scope.row['description']">说明：{{scope.row['description']}}</div>
                        <div v-if="scope.row['sample']">示例：{{scope.row['sample']}}</div>
                    </template>
                </el-table-column>
            </el-table>

            <el-divider content-position="left">返回参数</el-divider>
            <el-table border size="small" :data="returnParams" style="width: 100%">
                <el-table-column prop="name" label="参数名" width="180" />
                <el-table-column prop="type" label="类型" width="180" />
                <el-table-column prop="required" label="必填" width="180" >
                    <template #default="scope">
                        <el-tag type="success" v-if="scope.row['required'] == true || scope.row['required'] == 'true'">是</el-tag>
                        <el-tag v-else type="info">否</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="description" label="说明" >
                    <template #default="scope">
                        <div v-if="scope.row['description']">说明：{{scope.row['description']}}</div>
                        <div v-if="scope.row['sample']">示例：{{scope.row['sample']}}</div>
                    </template>
                </el-table-column>
            </el-table>
            <el-divider content-position="left">返回响应</el-divider>
            <qk-code :data="apiData.apiReturn"></qk-code>
        </el-tab-pane>
        <el-tab-pane label="修改" name="edit">
            <api-edit :api-data="apiData" @success="handleSuccess"></api-edit>
        </el-tab-pane>
        <el-tab-pane label="调试" name="run">
            <api-run :api-data="apiData" @success="handleSuccess"></api-run>
        </el-tab-pane>
    </el-tabs>
</template>

<script>
    import {computed, onBeforeMount, reactive, ref, watch,getCurrentInstance} from "vue";
    import ApiEdit from './api-edit'
    import ApiRun from './api-run'
    export default {
        name: "create-service",
        components:{
            ApiEdit,
            ApiRun
        },
        props:{
            apiData:{
                type:Object,
                default:() => {}
            }
        },
        setup(props,{emit}){

            const activeName = ref('base')
            const handleClick = (tab, event) => {
                console.log(tab, event)
            }
            const headers = computed(() => {
                return props.apiData.headers || []
            })

            const params = computed(() => {
                return props.apiData.params || []
            })

            const returnParams = computed(() => {
                return props.apiData.returnParams || []
            })
            const handleSuccess = function () {
                emit('success')
            }
            return {
                handleClick,
                handleSuccess,
                activeName,
                headers,
                params,
                returnParams,
            }

        },
    }
</script>

<style scoped>
    .m-l-10{
        margin-left:10px;
    }
</style>
