<script>
  import {h, resolveComponent, isVNode} from 'vue';

  export default {
    name: "AppLayout",
    props: {
      layout: {
        type: String,
        default: 'quick-layout',
      },
      lay_empty: {
        type: String
      }
    },
    mounted() {
      console.log('app---layout', this.query, this.layout)
    },
    render() {

      let layoutName = this.layout;
      if (this.lay_empty) {
        layoutName = 'router-view'
      }
      const layout = resolveComponent(layoutName);
      return h(layout)
    }
  }
</script>

