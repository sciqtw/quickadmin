<template>
  <div>
    <el-form :model="fields"  :rules="rules" size="mall"  ref="ruleFormRef" label-width="100px">
      <el-form-item label="接口名称" prop="title">
        <el-input v-model="fields.title"/>
      </el-form-item>
      <el-form-item label="Method">
        <el-select v-model="fields.method" class="m-2" placeholder="Select">
          <el-option key="POST" label="POST" value="POST"/>
          <el-option key="GET" label="GET" value="GET"/>
          <el-option key="DELETE" label="DELETE" value="DELETE"/>
          <el-option key="PUT" label="PUT" value="PUT"/>
        </el-select>
      </el-form-item>
      <el-form-item label="描述">
        <el-input v-model="fields.summary"/>
      </el-form-item>
      <el-form-item label="分组">
        <el-input v-model="fields.sector"/>
      </el-form-item>

      <tempate v-for="(item,i) in [
          {name:'Headers参数',key:'headers'},
          {name:'请求参数',key:'params'},
          {name:'返回参数',key:'returnParams'}
          ]">
        <el-divider content-position="left">{{item.name}}</el-divider>

        <el-table border size="small" :data="fields[item.key]" ref="attrTable">
          <el-table-column label="名称" width="180">
            <template #default="scope" property="name">
              <el-input v-model.tirm="scope.row['name']" type="text"></el-input>
            </template>
          </el-table-column>

          <el-table-column label="类型" property="type" width="130">
            <template #default="scope">

              <el-select size="mini" v-model="scope.row['type']"
                         filterable placeholder="选择数据类型">
                <el-option label="string" value="string"></el-option>
                <el-option label="boolean" value="boolean"></el-option>
                <el-option label="float" value="float"></el-option>
                <el-option label="integer" value="integer"></el-option>
                <el-option label="array" value="array"></el-option>

              </el-select>
            </template>
          </el-table-column>
          <el-table-column label="必须" property="required" width="60">
            <template #default="scope">
              <el-checkbox v-model="scope.row['required']" size="large"/>
            </template>
          </el-table-column>
          <el-table-column label="说明" property="description" width="250">
            <template #default="scope">
              <el-input v-model.tirm="scope.row['description']" type="text"></el-input>
            </template>
          </el-table-column>

          <el-table-column label="示例" property="sample">
            <template #default="scope">
              <el-input v-model.tirm="scope.row['sample']" type="text"></el-input>
            </template>
          </el-table-column>

          <el-table-column width="100" label="操作">
            <template #header>
              <el-button
                type="primary" size="small"
                @click="addItem(item.key)">添加
              </el-button>
            </template>
            <template #default="scope">
              <el-button
                type="danger" size="small" icon="el-icon-Delete"
                @click="delItem(item.key,scope)" circle></el-button>
            </template>

          </el-table-column>
        </el-table>
      </tempate>
      <el-divider content-position="left">响应示例</el-divider>
      <el-form-item label="" label-width="5">
        <el-input v-model="fields.apiReturn"
                  :rows="6"
                  type="textarea"/>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="onSubmit">保存</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import {computed, onBeforeMount, reactive, ref, watch, getCurrentInstance} from "vue";

  export default {
    name: "api-edit",
    props: {
      apiData: {
        type: Object,
        default: () => {
        }
      }
    },
    setup(props,{emit}) {

      const activeName = ref('base')
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
          { required: true, message: '请输入接口名称', trigger: 'blur' },
          { min: 3, max: 25, message: 'Length should be 3 to 5', trigger: 'blur' },
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
        fields.apiReturn = props.apiData.apiReturn || '';
        fields.headers = props.apiData.headers || [];
        fields.params = props.apiData.params || [];
        fields.returnParams = props.apiData.returnParams || [];
        fields.title = props.apiData.title || '';
        fields.method = props.apiData.method || 'GET';
        fields.summary = props.apiData.summary || '';
        fields.sector = props.apiData.sector || '';
        fields.class = props.apiData.class || '';
        fields.methodName = props.apiData.methodName || '';
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
      const onSubmit = function () {
        if (!ruleFormRef) return
        ruleFormRef.value.validate((valid, error) => {
          if (valid) {
            Quick.request({
              url: 'crud/api/update',
              method: 'POST',
              data: {
                formData: {
                  ...fields
                }
              }
            }).then((res) => {

                if(!res.code){
                  emit('success')
                  Quick.success(res.msg)
                }else{
                  Quick.error(res.msg)
                }
            })
          } else {
            console.log('error submit!', error)
          }
        })
      }





      return {
        addItem,
        delItem,
        onSubmit,
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

<style scoped>
  .m-l-10 {
    margin-left: 10px;
  }
</style>
