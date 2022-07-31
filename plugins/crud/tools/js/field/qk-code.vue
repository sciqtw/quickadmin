<template>
  <pre class="qk-pre">
    <code class="language-php" spellcheck="false">{{data}}</code>
  </pre>
</template>

<script>
  import hljs from 'highlight.js';
  import 'highlight.js/styles/a11y-dark.css'
  export default {
    props:{
      data:{
        type:String,
        default:''
      }
    },
    watch:{
      data:function () {
        this.$nextTick(() => {
          hljs.initHighlightingOnLoad()
        })

        console.log('----------watch  code')
      }
    },
    mounted() {
      hljs.initHighlightingOnLoad()
    },

  }
</script>
<style scoped>
  .qk-pre{
    margin:0;
    padding:0;
  }
  .language-php{
    margin-top:-20px;
    margin-bottom:-40px;
  }
</style>
