<template>
  <div class="notfound">
    <el-button @click="handleClick"> 按钮1{{currentSearch}}</el-button>
    <index-image src="https://fuss10.elemecdn.com/a/3f/3302e58f9a181d2509f3dc0fa68b0jpeg.jpeg"></index-image>
    <quick-drawer v-model="show">

    </quick-drawer>
    <quick-badge :count="3">
      <div>fdfdfdf</div>
    </quick-badge>
    <quick-badge dot>
      <div>fdfdfdf</div>
    </quick-badge>
    <quick-badge text="new">
      <a href="#" class="demo-badge">dddddddddddddddddddddd</a>
    </quick-badge>
    <br>
    <quick-badge status="success"/>
    <quick-badge status="error"/>
    <quick-badge status="default"/>
    <quick-badge status="processing"/>
    <quick-badge status="warning"/>
    <br>
    <quick-badge status="success" text="Success"/>
    <br>
    <quick-badge status="error" text="Error"/>
    <br>
    <quick-badge status="default" text="Default"/>
    <br>
    <quick-badge status="processing" text="Processing"/>
    <br>
    <quick-badge status="warning" text="Warning"/>

    <icon-card></icon-card>

    <el-button></el-button>
    <qk-descriptions>

    </qk-descriptions>


    <quick-action v-bind="popoverAction">
      <el-button>popoverAction</el-button>
    </quick-action>

    <el-button @click="test">close</el-button>
    <el-button @click="open">open</el-button>

  </div>
</template>

<script>

  export default {

    data() {
      return {
        inter: null,
        countdown: 5,
        show: false,
        popoverAction: {

          action: {
            action: 'popover',
            params: {
              config: {
                component: 'quick-popover',
                props: {
                  width: '500px',
                  content: {
                    component: 'el-button',
                    children: 'popover',
                    props: {},
                  },
                  request: {
                    url: 'demo/index/read'
                  }
                }
              }
            }
          },
          display: {
            component: 'el-button',
            children: 'popover1',
            props: {
              slot: 'reference'
            },
            slot: 'reference'
          }
        }
      }
    },
    watch: {
      '$route.query': function (newVal, oldVal) {
        // console.log(newVal + '---' +  oldVal);
        console.log('欢迎进入登录页面' + newVal);
      }
    },
    mounted() {
      this.$watch(
        () => {
          return (
            this.currentSearch
          )
        },
        (e) => {
          // this.$emit('tttt')
        },
        {
          immediate: true
        }
      )
      // this.$on('tttt',function () {
      //   console.log('---getInitData---e------------')
      // })
    },
    unmounted() {
      // this.$off('tttt',function () {
      //   console.log('---getInitData---e------------')
      // })
    },
    computed: {
      currentSearch() {
        return this.$route.query.search || ''
      },
    },
    inject:[
      'parentModal'
    ],
    methods: {
      test() {

        Quick.api.close(this.parentModal.id,{d:'fdfdf'})
      },
      open() {
        const component = {
          // "component": "quick-dialog",
          "width": "900px",
          "size": "900px",
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
        Quick.api.open(content1, component)
      },
      handleClick() {

        this.show = true
      },
      goBack() {
        this.$router.push('/')
      }
    }
  }
</script>

<style lang="scss" scoped>

</style>
