<template>
  <div
    class="when-field"
    :class="emitKey"
    v-if="show"
  >
    <slot></slot>
  </div>
</template>

<script>
  import {onMounted, ref, inject, getCurrentInstance} from "vue";
  export default {
    name: 'WhenField',
    props: {
      emitKey:{
        type:String,
        default:'when',
      },

    },
    setup(props) {

      const groupForm = inject('groupForm', {});
      const bus = inject('formBus');
      const show = ref(true);
      const {proxy} = getCurrentInstance();



      /**
       * 对外提供事件驱动
       */
      const onEvent = () => {

        if (props.emitKey) {
          groupForm.bus.on(props.emitKey, (data) => {
            const { action } = data;
            if(action === "show"){
              show.value = true
            }else if(action === "hidden"){
              show.value = false
            }
          });
        }
      };

      onMounted(() => {
        onEvent();
      });


      return {
        show
      }
    },
  }
</script>
<style scoped>

</style>
