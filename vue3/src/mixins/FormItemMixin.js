import { baseProps } from '../components/Form/Composition/FormField';
export default {
  props: {
   ...baseProps
  },
  mounted() {

  },
  data(){
    return {
      value:''
    }
  },
  computed: {
    labelProps:function () {
      return {
        ...this.$props,
        ...this.$attrs
      };
    }
  },
  methods: {

  }

}
