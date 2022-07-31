<template>
    <div>
        <el-form :model="fields" :rules="rules" size="mall" ref="ruleFormRef" label-width="80px">
            <el-form-item>
                <template #label>
                    <el-tag :type="tagType">{{fields.method}}</el-tag>
                </template>
                <el-input v-model="fields.route">
                    <template #append>
                        <el-button type="success" @click="send">发送</el-button>
                    </template>
                </el-input>
            </el-form-item>
        </el-form>

        <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick">
            <el-tab-pane v-for="(item,i) in [
          {name:'请求参数',key:'params'},
          {name:'Headers参数',key:'headers'},
          ]"  :label="item.name" :name="item.key" :key="i">

                <el-table border size="small" :data="fields[item.key]" ref="attrTable">
                    <el-table-column label="启用" property="required" width="60">
                        <template #default="scope">
                            <el-checkbox v-model="scope.row['required']" size="large"/>
                        </template>
                    </el-table-column>
                    <el-table-column label="字段" width="180">
                        <template #default="scope" property="name">
                            {{scope.row['name']}}
                        </template>
                    </el-table-column>

                    <el-table-column label="值" width="180">
                        <template #default="scope" property="name">
                            <el-input v-model.tirm="scope.row['value']" type="text"></el-input>
                        </template>
                    </el-table-column>

                    <el-table-column label="类型" property="type" width="130">
                        <template #default="scope">
                            {{scope.row['type']}}
                        </template>
                    </el-table-column>

                    <el-table-column label="说明" property="description" width="250">
                        <template #default="scope">
                            {{scope.row['description']}}
                        </template>
                    </el-table-column>

                    <el-table-column label="示例" property="sample">
                        <template #default="scope">
                            {{scope.row['sample']}}
                        </template>
                    </el-table-column>

                    <el-table-column width="100" label="操作">

                        <template #default="scope">
                            <el-button
                                    type="danger" size="small" icon="el-icon-Delete"
                                    @click="delItem(item.key,scope)" circle></el-button>
                        </template>

                    </el-table-column>
                </el-table>
            </el-tab-pane>

        </el-tabs>


        <el-divider content-position="left">响应示例</el-divider>
        <div class="return-box">
         <pre>
           <qk-code :data="fields.apiReturn"></qk-code>
         </pre>
        </div>

    </div>
</template>

<script>
    import {computed, onBeforeMount, reactive, ref, watch, getCurrentInstance} from "vue";
    // import axios from 'axios'
    import QkCode from "../field/qk-code";
    //引入axios
    export default {
        name: "api-edit",
        components: {QkCode},
        props: {
            apiData: {
                type: Object,
                default: () => {
                }
            }
        },
        setup(props, {emit}) {

            const activeName = ref('params')
            const ruleFormRef = ref(null)
            const fields = reactive({
                headers: [],
                params: [],
                returnParams: [],
                method: 'GET',
                title: '',
                summary: '',
                sector: '',
                apiReturn: '',
            })
            const rules = reactive({
                title: [
                    {required: true, message: '请输入接口名称', trigger: 'blur'},
                    {min: 3, max: 25, message: 'Length should be 3 to 5', trigger: 'blur'},
                ],
                // type: [
                //   {
                //     type: 'array',
                //     required: true,
                //     message: 'Please select at least one activity type',
                //     trigger: 'change',
                //   },
                // ],
            })


            const headers = ref([])
            const params = ref([])
            const returnParams = ref([])
            onBeforeMount(() => {
                fields.headers = props.apiData.headers || [];
                fields.params = props.apiData.params || [];
                fields.returnParams = props.apiData.returnParams || [];
                fields.title = props.apiData.title || '';
                fields.method = props.apiData.method || 'GET';
                fields.summary = props.apiData.summary || '';
                fields.sector = props.apiData.sector || '';
                fields.class = props.apiData.class || '';
                fields.route = props.apiData.route || '';
                fields.methodName = props.apiData.methodName || '';
                fields.apiReturn = props.apiData.apiReturn || '';
            })

            const addItem = function (action) {

                fields[action].push({
                    name: '',
                    type: 'string',
                    required: true,
                    description: '',
                    sample: '',
                })
            }


            const delItem = function (action, row) {
                fields[action].splice(row.$index, 1)
            }


            const tagType = computed(() => {
                // success/info/warning/danger
                let type = '';
                if (fields.method.toLowerCase() === 'get') {
                    type = '';
                } else if (fields.method.toLowerCase() === 'post') {
                    type = 'success';
                } else if (fields.method.toLowerCase() === 'delete') {
                    type = 'danger';
                } else if (fields.method.toLowerCase() === 'put') {
                    type = 'warning';
                }
                return type
            })


            const send = () => {
                let params = {};
                fields.params.forEach((param) => {
                    if (param.value && param.required) {
                        params[param.name] = param.value
                    }
                })
                let headers = {};
                fields.headers.forEach((val) => {
                    if (val.value && val.required) {
                        headers[val.name] = val.value
                    }
                })
                let data = {}

                if (fields.method.toLowerCase() == 'post') {
                    data = params
                    params = {}
                }


                const syntaxHighlight = function (json) {
                    if (typeof json != 'string') {
                        json = JSON.stringify(json, undefined, 2);
                    }
                    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                        var cls = 'number';
                        if (/^"/.test(match)) {
                            if (/:$/.test(match)) {
                                cls = 'key';
                            } else {
                                cls = 'string';
                            }
                        } else if (/true|false/.test(match)) {
                            cls = 'boolean';
                        } else if (/null/.test(match)) {
                            cls = 'null';
                        }
                        return '<span class="' + cls + '">' + match + '</span>';
                    });
                }

                const prepareStr = function (str) {
                    try {
                        return syntaxHighlight(JSON.stringify(JSON.parse(str.replace(/'/g, '"')), null, 2));
                    } catch (e) {
                        return str;
                    }
                }


                Quick.request({
                    url: fields.route,
                    method: fields.method,
                    data: data,
                    headers: headers,
                    params: params,
                }).then((res, ss) => {
                    console.log('-------requestrdd', res, ss)
                    fields.apiReturn = res.data
                }).catch(err => {
                    console.log('-------err', err.response)
                    fields.apiReturn =  err.response.data
                })
            }

            return {
                addItem,
                delItem,
                send,
                rules,
                ruleFormRef,
                fields,
                activeName,
                headers,
                params,
                returnParams,
            }

        },
    }
</script>

<style scoped lang="scss">

    //
    // Code (inline and block)
    // --------------------------------------------------


    // Inline and block code styles
    code,
    kbd,
    pre,


    // Inline code
    code {
        padding: 2px 4px;
        font-size: 90%;
    }

    // User input typically entered via keyboard
    kbd {
        padding: 2px 4px;
        font-size: 90%;
        box-shadow: inset 0 -1px 0 rgba(0,0,0,.25);

        kbd {
            padding: 0;
            font-size: 100%;
            font-weight: bold;
            box-shadow: none;
        }
    }

    // Blocks of code
    pre {
        display: block;
        padding: 4px;
        margin: 0 0 3px;
        font-size: 14px; // 14px to 13px
        line-height: 30px;
        word-break: break-all;
        word-wrap: break-word;

        // Account for some code outputs that place code tags in pre tags
        code {
            padding: 0;
            font-size: inherit;
            color: inherit;
            white-space: pre-wrap;
            background-color: transparent;
            border-radius: 0;
        }
    }

    // Enable scrollable blocks of code
    .pre-scrollable {
        /*max-height: @pre-scrollable-max-height;*/
        overflow-y: scroll;
    }


    .m-l-10 {
        margin-left: 10px;
    }

    .return-box {
        overflow: auto;
        max-height: 400px;
    }

</style>
