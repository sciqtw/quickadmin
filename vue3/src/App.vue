<template>
  <div id="app">
    <modal-provider>
      <router-view></router-view>
    </modal-provider>
  </div>

</template>
<script>
export default {
  name: 'App',
  props: {
    msg: String,
  },
  data() {
    return {
      name: '',
    };
  },
  methods: {
    handleClick() {
      this.renderData.children.push({
        component: 'h3',
        children: 'h1你号啊',
      });
    },
  },
};
</script>

<style lang="scss" scoped>


</style>
