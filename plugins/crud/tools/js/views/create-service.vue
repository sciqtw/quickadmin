<template>
    <div>
        <el-form :inline="true">
            <el-form-item label="命名空间">
                <el-input v-model="modelForm.namespace"></el-input>
            </el-form-item>

            <el-form-item label="名称">
                <el-input v-model="modelForm.name"></el-input>
            </el-form-item>

            <el-form-item >
                <el-button @click="quickAdd">快捷添加字段</el-button>
                <el-button @click="addField">添加</el-button>

                <quick-action :action="action" class="m-l-10">
                    <el-button>预览Code</el-button>
                </quick-action>
                <el-button type="primary"  class="m-l-10" @click="createService">创建</el-button>
                <el-checkbox v-model="modelForm.force" class="m-l-10" >强制覆盖</el-checkbox>
            </el-form-item>
        </el-form>

        <el-table  border size="mini" :data="fields" ref="attrTable" >
            <el-table-column type="selection" width="55"></el-table-column>
            <el-table-column label="字段">
                <template #default="scope" property="name">
                    <el-input v-model.tirm="scope.row['name']" type="text" ></el-input>
                </template>
            </el-table-column>

            <el-table-column label="名称" property="label">
                <template #default="scope">
                    <el-input v-model.tirm="scope.row['label']" type="text" ></el-input>
                </template>
            </el-table-column>
            <el-table-column label="类型" property="label">
                <template #default="scope">

                    <el-select size="mini" v-model="scope.row['php_type']"
                               filterable placeholder="选择数据类型" >
                        <el-option  label="string" value="string" > </el-option>
                        <el-option  label="boolean" value="boolean" > </el-option>
                        <el-option  label="float" value="float" > </el-option>
                        <el-option  label="integer" value="integer" > </el-option>
                        <el-option  label="array" value="array" > </el-option>

                    </el-select>
                </template>
            </el-table-column>

            <el-table-column label="验证规则" property="rule">
                <template #default="scope">
                    <el-input v-model.tirm="scope.row['rule']" type="text" ></el-input>
                </template>
            </el-table-column>

            <el-table-column width="100" label="操作">
                <template #default="scope">
                    <el-button type="danger" size="mini" icon="el-icon-Delete"  @click="delField(scope)" circle></el-button>
                </template>

            </el-table-column>
        </el-table>



        <el-dialog
                v-model="dialogVisible"
                title="快捷添加字段"
                width="600px"
                :before-close="handleClose"
        >
            <el-select v-model="selectTable" filterable placeholder="选择数据表">
                <el-option
                        v-for="(item,key) in tableList"
                        :key="key"
                        :label="item"
                        :value="item"
                >
                </el-option>
            </el-select>


            <el-table height="350" border size="mini" :data="selectFields"
                      style="width: 100%" ref="attrTable"
                      @selection-change="handleSelectionChange">
                <el-table-column type="selection" width="55"></el-table-column>
                <el-table-column label="字段">
                    <template #default="scope">
                        <div>
                            {{scope.row['name'] }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="名称" property="label"> </el-table-column>
                <el-table-column label="类型" property="type"> </el-table-column>
                <el-table-column label="comment" property="comment"> </el-table-column>

            </el-table>

            <template #footer>
                  <span class="dialog-footer">
                    <el-button @click="dialogVisible = false">Cancel</el-button>
                    <el-button type="primary" @click="confirm"
                    >确定</el-button
                    >
                  </span>
            </template>
        </el-dialog>


    </div>
</template>

<script>
    import {computed, onBeforeMount, reactive, ref, watch,getCurrentInstance} from "vue";

    export default {
        name: "create-service",
        setup(props){

            const {proxy}  = getCurrentInstance()
            const namespaceStorageKey = 'crud_service_namespace';
            const modelForm = reactive({
                table: '',
                namespace: localStorage.getItem(namespaceStorageKey) || 'app/admin/service',
                name: '',
                force: false,
            })
            watch(modelForm,(value) => {
                localStorage.setItem(namespaceStorageKey,value.namespace)
            })

            const tableList = ref([])
            const selectTable = ref('')
            const fields = ref([])
            const selectFields = ref([])
            const checkedFields = ref([])


            watch(selectTable,() => {
                getTableField()
            })
            const getTableField = function () {
                Quick.request({
                    url: 'crud/index/tableFields',
                    method: 'POST',
                    data: {
                        table: selectTable.value
                    }
                }).then((res) => {
                    if (!res.code) {
                        selectFields.value = res.data
                    }
                })
            }

            const getTableList = function () {
                Quick.request({
                    url: 'crud/index/tableList',
                    method: 'POST',
                    data: {
                        table: selectTable.value
                    }
                }).then((res) => {
                    if (!res.code) {
                        tableList.value = res.data
                    }
                })
            }
            onBeforeMount(() =>{
                getTableList()
            })

            const dialogVisible = ref(false)
            const quickAdd = function () {
                dialogVisible.value = true
            }
            const handleSelectionChange = function (val) {
                checkedFields.value = val
            }
            const confirm = function () {

                if(!checkedFields.value.length){
                    Quick.error('请选择字段')
                    return
                }
                dialogVisible.value = false;
                let columns = [];
                fields.value.forEach((item) => {
                    columns.push(item.name)
                })
                checkedFields.value.forEach((field) => {
                    if(!columns.includes(field.name)){
                        fields.value.push(field)
                    }
                })
            }

            const delField = function (row) {
                console.log('-------delField---------',row)
                fields.value.splice(row.$index,1)
            }


            const action = computed(() => {
                return {
                    action: 'dialog',
                    params: {
                        config: {
                            props: {
                                title: 'dialog'
                            }
                        },
                        content:{
                            url: "crud/index/previewAction",
                            method:"POST",
                            data:{
                                fields:fields.value,
                                namespace:modelForm.namespace,
                                name:modelForm.name,
                            }
                        }
                    }
                }
            })

            const addField = function () {
                fields.value.unshift({
                    name:'',
                    label:'',
                    rule:'',
                    php_type:'string',
                })
            }
            const createService = function () {



                proxy.$confirm(
                  '确定要创建service吗?',
                  '提示',
                  {
                      confirmButtonText: '确定',
                      cancelButtonText: '取消',
                      type: 'warning',
                  }
                )
                  .then(() => {
                      Quick.request({
                          url: "crud/index/createAction",
                          method:"POST",
                          data:{
                              fields:fields.value,
                              namespace:modelForm.namespace,
                              name:modelForm.name,
                              force:modelForm.force,
                          }
                      }).then((res) => {
                          if(res.code === 0){
                              proxy.$message({
                                  type:'success',
                                  message:res.msg
                              });
                          }else{
                              proxy.$message({
                                  type:'error',
                                  message:res.msg
                              });
                          }

                      })
                  })
                  .catch(() => {

                  })

            }
            return {
                quickAdd,
                handleSelectionChange,
                confirm,
                delField,
                addField,
                createService,
                action,
                fields,
                selectFields,
                tableList,
                selectTable,
                modelForm,
                dialogVisible
            }

        },
    }
</script>

<style scoped>
    .m-l-10{
        margin-left:10px;
    }
</style>
