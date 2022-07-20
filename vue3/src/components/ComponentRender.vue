<template>
  <component
    v-if="renderData.component"
    :is="renderData.component"
    v-bind="comProps"
  >
    <template v-for="(slot,name) in children" v-slot:[name] :key="name">
      <template v-for="(_config,k) in slot" :key="k">
        <template  v-if="!_config.component">
          {{_config}}
        </template>
        <component-render v-else :render-data="_config" ></component-render>
      </template>
    </template>
  </component>
  <span v-else>{{renderData}}</span>
</template>

<script setup>
  import {useAttrs,  onMounted, computed, defineProps,toRefs} from "vue";
  const props = defineProps({
    renderData: {
      type: Object,
      default: () => {},
    },
  })
  // const attrs = useAttrs()

  const comProps = computed(() => {
    return {
      ...props.renderData.props,
      // ...attrs
    }
  })
  const slotName = function (config) {
    return config.slot ? config.slot:'default'
  }
  const { renderData } = toRefs(props)


  const children = computed(() => {
    if(!props.renderData.children){
      return []
    }
    if (Array.isArray(props.renderData.children) && props.renderData.children.length > 0) {
      const slots = {};
      props.renderData.children.forEach((item) => {

        const slot = item.slot ? item.slot:'default';
        if (slots[slot]) {
          slots[slot].push(item);
        } else {
          slots[slot] = [item];
        }
      });
      return slots


    }else if(typeof props.renderData.children === 'string'){
      return {
        default:[
          props.renderData.children
        ]
      }
    }
    return props.renderData.children

  });

  onMounted(() => {
    // console.log('----------',renderData.value)
  })
</script>

<style scoped>

</style>
