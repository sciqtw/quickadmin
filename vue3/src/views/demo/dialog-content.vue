<template>
  <div class="notfound">

    <el-button @click="test">close</el-button>
    <el-button @click="test1">test1</el-button>
    <el-button @click="open">open</el-button>
    <el-button @click="close1">提交后端数据后响应事件</el-button>

  </div>
</template>

<script>

  export default {
    props:{
      ttt:String
    },
    data() {
      return {
        inter: null,
        countdown: 5,
        show: false,

      }
    },

    inject:[
      'parentModal'
    ],
    methods: {
      test() {
        console.log('-parentModal',this.parentModal)
        Quick.api.close('close',{d:'fdfdf'},this.parentModal.id)
      },
      test1() {
        console.log('-parentModal',this.parentModal)
        Quick.api.close('action1',{
          action:'message',
          params:{
            message:'fdfdf'
          }
        },this.parentModal.id)
      },
      open() {
        const component = {
          // "component": "quick-dialog",
          "width": "900px",
          "size": "800px",
          "title": "标题1",
          'beforeClose': function (res, data, done) {
            done()
            console.log('---------fdfddfdfd', res, data)
          }
        }

        const content1 = {
          "component": "div",
          "children": "div",
        }
        const content2 = {
          url: 'demo/test/test',
          method: 'post',
        }
        Quick.api.open(test4, component)
      },
      handleClick() {

        this.show = true
      },
      goBack() {
        this.$router.push('/')
      },
      close1(){
        Quick.request({
          url:'demo/index/testResponse'
        }).then((res) => {
          if(res.action){
            Quick.api.close('action',res.action)
          }else{
            Quick.api.close('close',res.data)
          }
        })
      }
    }
  }
</script>

<style lang="scss" scoped>

</style>
