import test from './field/test.vue'
import index from './views/index.vue'
import {h,resolveComponent} from 'vue'



window.Quick.booting( (app,router) => {

  router.addRoute( {
    path:'/',
    component:{
      render(){
        return h(resolveComponent('quick-layout'))
      }
    },
    children:[
      {name:'plugin-test', path: '/plugin-test', component:index},
    ]
  })

  app.component('qk-test',test)
})
