<template>
  <div class="page">
    <page-header  >
      <template #title>
        <div class="header-title">icons </div>
      </template>
      <template #content>
        <slot name="description">
          <div class="header-content">系统图标</div>
        </slot>

        <slot name="tools"> </slot>
      </template>

    </page-header>
    <page-main>
      <quick-icons></quick-icons>
    </page-main>
  </div>
</template>
<script>
  import icons from "../components/SelectIcon/icons";
  export default {
    name: 'Icons',
    components:{
      'quick-icons':icons
    },
    props: {
      title: String,
      description: String,
      showHeader: {
        type: Boolean,
        default: true
      }
    },
    data() {
      return {
        activeName: ''
      }
    },
    computed: {},
    watch: {},
    async created() {

    },
    methods: {}
  }
</script>
