<template>
    <div>
        <el-card shadow="never" style="margin-bottom:20px;">

            <el-form :inline="true" label-position="top">
                <el-row >
                    <el-form-item label="快捷设置命名空间">
                        <div style="display:flex;">
                            <div style="width:400px;">
                                <el-radio-group v-model="module.mode">
                                    <el-radio label="app">app</el-radio>
                                    <el-radio label="plugins">plugins</el-radio>
                                </el-radio-group>
                            </div>
                            <el-input v-model="module.module"></el-input>
                        </div>

                    </el-form-item>
                    <el-form-item label="resource命名空间">
                        <el-input v-model="modelForm.resource_namespace"></el-input>
                    </el-form-item>
                    <el-form-item label="自定义resource名称">
                        <el-input v-model="modelForm.resource_name"></el-input>
                    </el-form-item>

                </el-row>
                <el-divider content-position="left">主表设置</el-divider>
                <el-row>
                    <el-form-item label="选择表">

                        <el-select v-model="modelForm.table" @change="selectTable($event,-1)" filterable placeholder="Select">
                            <el-option
                              v-for="(item,key) in tableList"
                              :key="key"
                              :label="item"
                              :value="item"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="model命名空间">
                        <el-input v-model="modelForm.namespace"></el-input>
                    </el-form-item>

                    <el-form-item label="自定义model名称">
                        <el-input v-model="modelForm.model_name"></el-input>
                    </el-form-item>

                </el-row>
                <el-divider content-position="left">关联表设置</el-divider>
                <el-row style="width:100%;" v-for="(item,index) in relations" :key="index">

                    <el-form-item label="关联表">

                        <el-select v-model="item.table"
                                   filterable placeholder="Select"
                                   @change="selectTable($event,index)">
                            <el-option
                              v-for="(item,key) in tableList"
                              :key="key"
                              :label="item"
                              :value="item"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="自定义关联model">
                        <el-input v-model="modelForm.model"></el-input>
                    </el-form-item>
                    <el-form-item label="关联表类型">

                        <el-select v-model="item.type"
                                   placeholder="关联表类型"
                                   @click="selectField($event)" >
                            <el-option  label="belongsTo"  value="belongsTo" ></el-option>
                            <el-option  label="hasOne"  value="hasOne" >  </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="关联外键">

                        <el-select v-model="item.relationForeignKey"
                                   filterable
                                   placeholder="选择字段"
                                   @click="selectField($event)" >
                            <el-option
                              v-for="(item,key) in  (item.type === 'hasOne' ? item.fields:fields)"
                              :key="key"
                              :label="item.name"
                              :value="item.name"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="关联主键">

                        <el-select v-model="item.relationPrimaryKey"
                                   filterable
                                   placeholder="选择字段"
                                   @click="selectField($event)" >
                            <el-option
                              v-for="(item,key) in (item.type === 'hasOne' ? fields:item.fields)"
                              :key="key"
                              :label="item.name"
                              :value="item.name"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>


                    <el-form-item label="显示字段">

                        <el-select v-model="item.shows"
                                   filterable
                                   multiple
                                   collapse-tags
                                   placeholder="选择字段"
                                   @click="selectField($event)" >
                            <el-option
                              v-for="(item,key) in item.fields"
                              :key="key"
                              :label="item.name"
                              :value="item.name"
                            >
                            </el-option>
                        </el-select>
                        <el-button @click="delRelation(index)" class="m-l-10"   type="danger">删除</el-button>
                    </el-form-item>
                </el-row>

            </el-form>



            <el-button size="mini" class="m-b-20" @click="addRelation">添加关联</el-button>
            <el-row >
                <el-col :span="12">
                   <div style="display: flex;">

                       <el-checkbox v-model="modelForm.force" value="delete">强制覆盖</el-checkbox>
                       <el-checkbox v-model="modelForm.create_model">创建model</el-checkbox>
                       <el-checkbox v-model="modelForm.create_lang">创建Lang</el-checkbox>
                       <el-checkbox v-model="modelForm.create_resource">创建resource</el-checkbox>
                   </div>



                </el-col>
                <el-col :span="12">
                    <quick-action :action="previewForm" :data="fields" v-if="modelForm.create_resource">
                        <el-button>预览form</el-button>
                    </quick-action>
                    <quick-action class="m-l-10"  :action="previewTable" :data="fields" v-if="modelForm.create_resource">
                        <el-button>预览List</el-button>
                    </quick-action>
                    <quick-action class="m-l-10"  :action="previewModel" :data="fields" v-if="modelForm.create_model">
                        <el-button>预览model</el-button>
                    </quick-action>
                    <quick-action class="m-l-10"  :action="previewLang" :data="fields" v-if="modelForm.create_lang">
                        <el-button>预览Lang</el-button>
                    </quick-action>
                    <quick-action class="m-l-10" :action="previewCode" v-if="modelForm.create_resource" >
                        <el-button>预览resource</el-button>
                    </quick-action>
                    <el-button  class="m-l-10" @click="createCrud"  type="danger" v-if="modelForm.create_model || modelForm.create_resource || modelForm.create_lang">创建</el-button>
                </el-col>
            </el-row>

        </el-card>

        <el-table border size="mini" :data="fieldList" style="width: 100%" ref="attrTable">
            <!--          <el-table-column type="selection" width="55"></el-table-column>-->
            <el-table-column label="字段" width="200">
                <template #default="scope">
                    <div>
                        {{scope.row['relation'] ? scope.row['relation']+'.'+scope.row['name'] :scope.row['name']}}
                    </div>
                </template>
            </el-table-column>
            <el-table-column :label="item.label" v-for="(item,index) in itemLists" :key="index" :width="item.width">
                <template #default="scope">
                    <el-input size="mini" v-model.tirm="scope.row[item.name]" v-if="!item.type || item.type === 'text'"></el-input>
                    <el-select size="mini" v-model="scope.row[item.name]" filterable placeholder="Select" v-else-if="item.type === 'select'">
                        <el-option
                          v-for="(item,key) in fieldTypes"
                          :key="key"
                          :label="item"
                          :value="key"
                        >
                        </el-option>
                    </el-select>
                    <el-input size="mini" v-model.tirm="scope.row[item.name]" type="number"
                              v-else-if="item.type === 'number'"></el-input>
                </template>
            </el-table-column>
            <el-table-column >
                <template #default="scope">
                    <el-checkbox v-model="scope.row['is_form']">form</el-checkbox>
                    <el-checkbox v-model="scope.row['is_table']">table</el-checkbox>
                    <el-checkbox v-model="scope.row['require']">form验空</el-checkbox>
                </template>

            </el-table-column>
        </el-table>

    </div>
</template>

<script>
    import QkCode from "../field/qk-code"
    import {ref, reactive,onBeforeMount,watch,computed,getCurrentInstance} from 'vue'
    import CreateService from "./create-service";
    export default {
        name: "create-crud",
        components: {
            QkCode
        },
        setup(props){
            const table = ref('')
            const fields = ref([])
            const fieldTypes = ref({})
            const {proxy} = getCurrentInstance()
            const module = reactive({
                module:'admin',
                mode:'app'
            });

            const itemLists = ref([
                {
                    label: '字段别名',
                    type: 'text',
                    name: 'label',
                    width: '160',
                },
                {
                    label: '使用组件',
                    type: 'select',
                    name: 'form',
                    width: '160',
                    options:{
                        text:'text',
                        image:'image',
                        tag:'tag',
                    }
                },
                // {
                //     width: '250',
                //     label: '验证规则',
                //     type: 'text',
                //     name: 'rule',
                // }
            ])



            const modelForm = reactive({
                force: false,
                create_resource:true,
                create_model:true,
                create_lang:true,
                table: '',
                namespace: 'app/common/model',
                model_name: '',
                resource_name: '',
                resource_namespace: 'app/admin/quick/resource',
                relation:[],
            })


            const initNamespace = function(){
                modelForm.resource_namespace = module.mode+"/"+module.module + '/quick/resource';
                modelForm.namespace =  module.mode+"/"+module.module +  '/model';
            }
            watch(module,() => {
                localStorage.setItem('module_mode',module.mode)
                localStorage.setItem('module_module',module.module)
                initNamespace()
            },{deep:true})
            onBeforeMount(() => {
                const data = {
                    mode:localStorage.getItem('module_mode') || 'app',
                    module:localStorage.getItem('module_module') || 'admin',
                };
                module.mode = data.mode
                module.module = data.module
                initNamespace()
            })





            const relations = ref([]);
            const addRelation = function () {
                relations.value.push({
                    table:'',
                    model:'',
                    type:'hasone',
                    relationPrimaryKey:'',
                    relationForeignKey:'',
                    fields:[],
                    shows:[]
                })
            }
            const delRelation = function (index) {
                relations.value.splice(index,1)
            }

            const fieldList = ref([])
            const initFieldList = () => {
                let list = JSON.parse(JSON.stringify(fields.value));
                relations.value.map((relation) => {
                    relation.fields.map((field) => {
                        if(relation.shows.indexOf(field.name) !== -1){
                            list.push(field)
                        }
                    })
                })

                return list
            }

            watch(relations,() => {
                fieldList.value = initFieldList()
            },{deep:true})


            const selectTable = function (tableName,index) {
                console.log('-------------selectTable',tableName,index)
                Quick.request({
                    url: 'crud/index/tableFields',
                    method: 'POST',
                    data: {
                        table: tableName,
                        relation: index !== -1,
                    }
                }).then((res) => {
                    if (!res.code) {
                        if(index === -1){
                            fields.value = res.data
                            fieldTypes.value = res.type
                        }else{
                            relations.value[index].fields = res.data
                        }

                        fieldList.value = initFieldList()
                    }
                })
            }

            const selectField = function (e) {
                console.log('-----------------',e)
            }

            const tableList = ref([])
            const getTableList = function () {
                Quick.request({
                    url: 'crud/index/tableList',
                    method: 'POST',
                    data: {
                        table: modelForm.table
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


            const createCrud = function () {

                if(!modelForm.table){
                    proxy.$message({
                        type:'error',
                        message:'主表不能为空'
                    })
                    return
                }
                Quick.request( {
                    url:"crud/index/createCrud",
                    method:"POST",
                    data:{
                        fields:JSON.stringify(fieldList.value),
                        namespace:modelForm.namespace,
                        force:modelForm.force,
                        name:modelForm.model_name,
                        table:modelForm.table,
                        resource_name: modelForm.resource_name,
                        resource_namespace: modelForm.resource_namespace,
                        create_model:modelForm.create_model,
                        create_lang:modelForm.create_lang,
                        create_resource:modelForm.create_resource,
                        relations:JSON.stringify(relations.value),
                    }
                }).then((res) => {
                    console.log('res----------->',res)

                    proxy.$message({
                        type:'success',
                        message:'创建成功'
                    });
                })
            }

            const previewLang = computed(() => {
                return {
                    action: 'dialog',
                    params: {
                        config: {
                            props: {
                                title: 'dialog',
                                size:'70%',
                            }
                        },
                        content: {
                            url:"crud/index/previewModel",
                            method:"POST",
                            data:{
                                fields:JSON.stringify(fieldList.value),
                                namespace:modelForm.namespace,
                                force:modelForm.force,
                                show_type:'lang',
                                name:modelForm.model_name,
                                table:modelForm.table,
                                resource_name: modelForm.resource_name,
                                create_model:modelForm.create_model,
                                create_resource:modelForm.create_resource,
                                relation:JSON.stringify(relations.value),
                            }
                        }
                    }
                }
            })

            const previewModel = computed(() => {
                return {
                    action: 'dialog',
                    params: {
                        config: {
                            props: {
                                title: 'dialog',
                                size:'70%',
                            }
                        },
                        content: {
                            url:"crud/index/previewModel",
                            method:"POST",
                            data:{
                                fields:JSON.stringify(fieldList.value),
                                namespace:modelForm.namespace,
                                force:modelForm.force,
                                name:modelForm.model_name,
                                table:modelForm.table,
                                resource_name: modelForm.resource_name,
                                create_model:modelForm.create_model,
                                create_resource:modelForm.create_resource,
                                relation:JSON.stringify(relations.value),
                            }
                        }
                    }
                }
            })
            const previewForm  = computed(() => {
                return {
                    action: 'dialog',
                    params: {
                        config: {
                            props: {
                                title: 'dialog',
                                size:'70%',
                            }
                        },
                        content: {
                            url:"crud/index/previewForm",
                            method:"POST",
                            data:{
                                fields:JSON.stringify(fieldList.value),
                            }
                        }
                    }
                }
            })

            const previewTable = computed(() => {
                return {
                    action: 'dialog',
                    params: {
                        config: {
                            props: {
                                title: 'dialog',
                                size:'70%',
                            }
                        },
                        content: {
                            url:"crud/index/previewTable",
                            method:"POST",
                            data:{

                                fields:JSON.stringify(fieldList.value)
                            }
                        }
                    }
                }
            })

            const previewCode  = computed(() => {
                return {
                    action: 'dialog',
                    params: {
                        config: {
                            props: {
                                title: 'dialog',
                                size:'70%',
                            }
                        },
                        content: {
                            url:"crud/index/previewResource",
                            method:"POST",
                            data:{
                                table:modelForm.table,
                                namespace:modelForm.namespace,
                                force:modelForm.force,
                                name:modelForm.model_name,
                                resource_name: modelForm.resource_name,
                                resource_namespace: modelForm.resource_namespace,
                                fields:JSON.stringify(fieldList.value)
                            }
                        }
                    }
                }
            })



            return {
                addRelation,
                selectTable,
                selectField,
                createCrud,
                module,
                tableList,
                modelForm,
                previewCode,
                previewTable,
                previewForm,
                previewModel,
                previewLang,
                fieldList,
                fields,
                itemLists,
                relations,
                fieldTypes,
                delRelation,
            }

        },
    }
</script>

<style scoped>
    .m-l-10{
        margin-left:10px;
    }
    .m-b-20{
        margin-bottom: 20px;
    }
</style>
