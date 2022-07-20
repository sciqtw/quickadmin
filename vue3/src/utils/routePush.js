import router from '../router';
import store from '../store';
/**
 * Remove class from element
 * @param {HTMLElement} elm
 * @param {string} cls
 */
export function routePush(data) {

  if(typeof data === 'string'){
    data = {
      path:data
    }
  }
  /** 处理菜单问题  ***/
  let hasMenuPath = false
  for(let i in store.state.menu.oneArrMenus){
    if(store.state.menu.oneArrMenus[i].path === data.path){
      hasMenuPath = store.state.menu.oneArrMenus[i]
      break;
    }
  }
  let query = {}
  if(hasMenuPath){
    query.spm =hasMenuPath.spm
  }else{
    if(router.currentRoute.value.query && router.currentRoute.value.query.spm){
      query.spm = router.currentRoute.value.query.spm
    }
  }
  data.query = Object.assign({}, data.query || {}, query)
  /** 处理菜单问题 end ***/
  router.push(data)
}
