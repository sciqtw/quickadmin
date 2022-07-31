import QkCode from './field/qk-code.vue'
import code from './views/code.vue'
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
      {name:'code', path: '/code', component:code},
    ]
  })

  app.component('qk-code',QkCode)
  app.component('qk-crud',code)
})
