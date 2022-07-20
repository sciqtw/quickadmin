<template>
  <default-field
    v-bind="labelProps"
  >
    <form-group v-bind="labelProps" ref="pGroup" @change="handleChange">

      <el-table :key="key" size="mini" :data="lists">
        <template v-for="(column,index) in table.props.columns">
          <el-table-column
            v-if="column.name !== 'action'"
            :key="index"
            v-bind="column.props"
            :prop="column.name"
            :label="column.title"
          >
            <template #default="scope">
              <json-render
                :render-data="setItem(scope.row[column.name].value,column.name,scope.$index)"
              />
            </template>
          </el-table-column>
        </template>


        <el-table-column
          align="center"
          width="100"
        >
          <template #header>
            <el-button size="mini" :disabled="(max > 0 && max <= lists.length)" @click="addFieldItem">{{ __('Add') }}</el-button>
          </template>
          <template #default="scope">
            <el-button
              size="mini"
              type="danger"
              @click="delField(scope.$index)"
            >{{ __('Delete') }}
            </el-button>
            <!--          <quick-icon  @click="delField(scope.index)" icon="el-icon-delete-solid" color="red" size="16" />-->
          </template>
        </el-table-column>
      </el-table>
    </form-group>
  </default-field>

</template>

<script>

import {
  ref,
  onMounted,
  getCurrentInstance, computed, useAttrs,
} from "vue";
import {baseProps} from "../Composition/FormField";
import FormGroup from '../FormGroup';

export default {
  name: 'JsonField',
  componentType: 'form',
  components: {
    FormGroup
  },
  props: {
    ...baseProps,
    fieldJson: {
      type: [Object, Array],
      default: () => {
      }
    },
    table: {
      type: Object,
      default: () => {
      }
    },
    keyValueField: {
      type: Array,
      default: () => []
    },
    fieldType: {
      type: String,
      default: 'table'
    },
    min: {
      type: Number,
      default: 0
    },
    max: {
      type: Number,
      default: 0
    },
  },

  setup(props) {


    const lists = ref([])
    const key = ref(1)
    const pGroup = ref(null)
    const fields = [];
    const {proxy} = getCurrentInstance();

    const attrs = useAttrs();
    /**
     * label
     */
    const labelProps = computed(() => {
      return {
        ...props,
        ...attrs
      };
    });


    /**
     * 验证form
     * @param callback
     */
    const validate = function (callback) {
      // console.log('-------------')
      // return true;
      let errorArr = [];
      _.each(fields, field => {
        const errors = field.validate('');
        if (errors && errors.length) {
          errorArr = errorArr.concat(errors);
        }
      });

      callback && callback(errorArr);
      if(!errorArr.length){
        return true;
      }
      return errorArr
    };


    /**
     * 获取当前值
     */
    const getFieldValue = () => {

      let fields = proxy.$refs.pGroup.getFields();
      const data = {};
      return _.tap(data, formData => {
        _.each(fields, field => {
          field.fill(formData);
        });
      });
    };

    /**
     * 填充formArr
     * @param formData
     */
    const fill = (formData) => {
      let data = getFieldValue();
      const keyValues = {}
      for (var key in data) {
        let keys = key.split("@");
        if (keyValues[keys[0]]) {
          keyValues[keys[0]].push(data[key])
        } else {
          keyValues[keys[0]] = [data[key]]
        }
      }

      formData[props.column] = JSON.stringify(keyValues);
      return formData[props.column];
    };


    onMounted(() => {
      lists.value = props.table.props.lists
      if (props.min > 0 && props.min > lists.value.length) {
        // eslint-disable-next-line for-direction
        for (var i = 0; i <= props.min - lists.value.length; i++) {
          addFieldItem()
        }
      }
    });


    const addFieldItem = () => {
      if(props.max > 0 && lists.value.length >= props.max){
        return
      }
      lists.value = lists.value.concat(JSON.parse(JSON.stringify(props.fieldJson)))
    }


    const delField = (index) => {
      lists.value.splice(index, 1)
      key.value++
    }

    const setItem = (row, name, index) => {
      let item = Object.assign({}, row)
      console.log('---------reo',row,item)
      item.props.column = name + '@' + index
      return item;
    }

    const handleChange = function (data) {
      let temp = data.column.split("@")
      const key = temp[0].replace(props.column+'.','')
      lists.value[temp[1]][key].value.props.default = data.value
    }


    return {
      validate,
      setItem,
      addFieldItem,
      delField,
      handleChange,
      fill,
      labelProps,
      lists,
      pGroup,
      key
    }
  }

}
</script>
<style lang="scss" scoped>

.json-field {
  /*:deep .el-form-item{*/
  /*  margin-bottom: 0 !important;*/
  /*}*/
}

.field-box {
  padding-right: 40px;
  position: relative;

  .field-box-del {
    color: red;
    position: absolute;
    right: 0;
    top: 10px;
  }
}
</style>
