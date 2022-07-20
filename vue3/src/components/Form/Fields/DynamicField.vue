<template>
  <form-group
    ref="pGroup"
    v-bind="labelProps"
  >
    <default-field
      v-bind="labelProps"
      :isRegistered="false"
      ref="def"
    >
      <draggable v-model="lists" filter=".undraggable" handle=".dargBtn" @start="startDraggable" @end="endDraggable">
        <template #item="{element,index}">
          <div class="field-box" :key="key">
            <form-group
              :column="index"
            >
              <div class="field-box-name"> {{ index+1 }}</div>
              <json-render :render-data="element"></json-render>

              <div class="field-box-tool">
                <quick-icon class="field-box-del" icon="el-icon-delete" size="16" @click="delField(index)"/>
                <quick-icon class="field-box-romve dargBtn" icon="el-icon-rank" size="16"/>
              </div>
            </form-group>
          </div>


        </template>
        <template #header>
          <el-button plain type="primary" size="mini" @click="_addField">添加</el-button>
        </template>
      </draggable>

    </default-field>
  </form-group>
</template>

<script>
  import FormGroup from '../FormGroup';
  import draggable from 'vuedraggable'
  import {computed, ref, useAttrs, onMounted, getCurrentInstance,nextTick} from "vue";
  import {baseProps} from '../Composition/FormField';

  export default {
    name: 'DynamicField',
    componentType: 'form',
    components: {
      draggable,
      FormGroup
    },
    props: {
      ...baseProps,
      fieldJson: {
        type: Object,
        default: () => {
        }
      },
      fieldData: {
        type: [Array],
        default: () => []
      },
      min: {
        type: Number,
        default: 0
      },
      max: {
        type: Number,
        default: 0
      }
    },
    data() {
      return {
        tempData: {}
      }
    },
    setup(props) {

      const attrs = useAttrs();
      const value = ref('');
      const def = ref(null);
      const key = ref(1);

      const pGroup = ref(null)
      const tempData = [];
      const lists = ref([]);
      const {proxy} = getCurrentInstance();

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

      // /**
      //  * 填充formArr
      //  * @param formData
      //  */
      // const fill = (formData) => {
      //   let data = getFieldValue();
      //   const keyValues = {}
      //   for (var key in data) {
      //     let keys = key.split("@");
      //     if (keyValues[keys[0]]) {
      //       keyValues[keys[0]].push(data[key])
      //     } else {
      //       keyValues[keys[0]] = [data[key]]
      //     }
      //   }
      //
      //   formData[props.column] = JSON.stringify(keyValues);
      //   return formData[props.column];
      // };


      /** 处理表单重置 **/
      const resetField = () => {
        value.value = initialValue();
      };

      /**
       * 初始化值
       */
      const initialValue = () => {
        return !(props.default === undefined || props.default === null)
          ? props.default
          : '';
      };


      /** 接管验证 start ******/
      const validate = () => {
        return false;
      };


      onMounted(() => {
        value.value = initialValue();

        props.fieldData.map((item) => {
          item.index_key = Math.random()
          lists.value.push(item)
        })
        if (props.min > 0 && props.min > props.lists.length) {
          // eslint-disable-next-line for-direction
          for (var i = 0; i <= props.min - lists.value.length; i++) {
            _addField()
          }
        }
      })


      const _addField = function () {
        const obj = JSON.parse(JSON.stringify(props.fieldJson))
        obj.index_key = Math.random()
        lists.value = lists.value.concat(obj)
      }

      const delField = function (index) {
        startDraggable()
        lists.value.splice(index, 1)
        endDraggable()
      }


      const startDraggable = function () {
        const data = getFieldValue()
        for(var i in data){
          tempData[lists.value[i].index_key] = data[i]
        }
      }

      /**
       *
       */
      const endDraggable = function () {
        key.value++
        let fields = proxy.$refs.pGroup.getFields();
        nextTick(() => {
          fields.map((item, index) => {
            item.resetField(tempData[lists.value[index].index_key])
          })
        })
      }


      return {
        resetField,
        validate,
        delField,
        startDraggable,
        endDraggable,
        _addField,
        key,
        labelProps,
        lists,
        attrs,
        value,
        def,
        pGroup
      }
    }
  }
</script>
<style lang="scss" scoped>
  .field-box {
    padding-right: 60px;
    padding-left: 12px;
    position: relative;
    background-color: #FFFFFF;
    margin-left: -22px;

    :deep .el-form-item .el-form-item {
      margin-bottom: 10px;
    }

    .field-box-name {
      position: absolute;
      left: 0;
      top: 0;
    }

    .field-box-tool {
      position: absolute;
      right: 5px;
      top: 0;

      .field-box-del {
        color: red;
        margin-right: 6px;
      }

      .field-box-romve {
        /*color:#409EFF;*/
      }
    }
  }
</style>
