<template>
  <el-table-column
    :label="column?.title"
    :prop="column?.name"
    v-bind="attrs"
  >

    <template v-for="(slots,name) in children" :key="name" v-slot:[name]="scope">
      <template v-if="!slots.length && name === 'default'">
        <json-render
          v-if="scope.row[column.name].display && scope.row[column.name].display.component"
          :render-data="scope.row[column.name].display"
          @refresh="refresh"
          @submit="refresh"
        />
        <component
          v-else-if="scope.row[column.name].value && scope.row[column.name].value.component"
          :is="scope.row[column.name].value.component"
          v-bind="scope.row[column.name].value.props"
          @refresh="refresh"
          @submit="refresh"
        />
        <span v-else>{{ scope.row[column.name].value }}</span>
      </template>
      <json-render v-else :is="slot.component" v-for="(slot,k) in slots" :key="k"
                   :render-data="slot" v-bind="{column:slot}"></json-render>
    </template>


  </el-table-column>
</template>

<script>

export default {
  name: 'QuickColumn',
  props: {
    column: {
      type: Object,
      required: true
    }
  },
  mounted() {
  },
  computed: {
    attrs:function () {
      return this.column.props
    },
    children:function () {
      let slots = {};
      if(this.column.children && this.column.children.length){
        const list = {
          default:[]
        }
        this.column.children.forEach((item) => {
          const slot = item.slot ? item.slot:'default';
          if (list[slot]) {
            list[slot].push(item);
          } else {
            list[slot] = [item];
          }
        })
        slots =  list

      }else{
        slots.default = []
      }
      return slots
    }
  },
  methods: {
    /**
     * 刷新数据
     */
    refresh() {
      this.$emit('refresh')
    }
  }
}
</script>
<style scoped>

</style>
