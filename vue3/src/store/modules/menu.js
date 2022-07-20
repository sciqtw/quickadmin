import path from 'path-browserify'
import {deepClone} from '@/utils'
// import api from '@/api'
function isExternalLink(path) {
  return /^(https?:|mailto:|tel:)/.test(path)
}
function resolveRoutePath (basePath, route) {
  if (isExternalLink(route.path)) {
    return route.path
  }
  if (isExternalLink(basePath)) {
    return basePath
  }

  let url = route.path
  if(url.indexOf("?") === -1){
    url =  url += "?spm="+route.spm
  }else{
    url =  url += "&spm="+route.spm
  }

  return basePath ? path.resolve(basePath, url) : url
}

// 将多层嵌套路由处理成平级
function flatAsyncRoutes(routes, breadcrumb, baseUrl = '') {
  let res = []
  routes.forEach(route => {
    if (route.children) {
      let childrenBaseUrl = ''
      if (baseUrl == '') {
        childrenBaseUrl = route.path
      } else if (route.path != '') {
        childrenBaseUrl = `${baseUrl}/${route.path}`
      }
      let childrenBreadcrumb = deepClone(breadcrumb)
      if (route.meta.breadcrumb !== false) {
        childrenBreadcrumb.push({
          path: childrenBaseUrl,
          title: route.meta.title
        })
      }
      let tmpRoute = deepClone(route)
      tmpRoute.path = childrenBaseUrl
      tmpRoute.meta.breadcrumbNeste = childrenBreadcrumb
      delete tmpRoute.children
      res.push(tmpRoute)
      let childrenRoutes = flatAsyncRoutes(route.children, childrenBreadcrumb, childrenBaseUrl)
      childrenRoutes.map(item => {
        // 如果 path 一样则覆盖，因为子路由的 path 可能设置为空，导致和父路由一样，直接注册会提示路由重复
        if (res.some(v => v.path == item.path)) {
          res.forEach((v, i) => {
            if (v.path == item.path) {
              res[i] = item
            }
          })
        } else {
          res.push(item)
        }
      })
    } else {
      let tmpRoute = deepClone(route)
      if (baseUrl != '') {
        if (tmpRoute.path != '') {
          tmpRoute.path = `${baseUrl}/${tmpRoute.path}`
        } else {
          tmpRoute.path = baseUrl
        }
      }
      // 处理面包屑导航
      let tmpBreadcrumb = deepClone(breadcrumb)
      if (tmpRoute.meta.breadcrumb !== false) {
        tmpBreadcrumb.push({
          path: tmpRoute.path,
          title: tmpRoute.meta.title
        })
      }
      tmpRoute.meta.breadcrumbNeste = tmpBreadcrumb
      res.push(tmpRoute)
    }
  })
  return res
}

function getDeepestPath(routes, rootPath = '') {
  let retnPath
  if (routes?.children && routes?.children.length) {
    if (
      routes.children.some(item => {
        return item.meta.sidebar != false
      })
    ) {
      for (let i = 0; i < routes.children.length; i++) {
        if (routes.children[i].meta.sidebar != false) {
          retnPath = getDeepestPath(routes.children[i], resolveRoutePath(rootPath, routes))
          break
        }
      }
    } else {
      retnPath = getDeepestPath(routes.children[0], resolveRoutePath(rootPath, routes))
    }
  } else {
    retnPath = resolveRoutePath(rootPath, routes)
  }
  return retnPath
}


function findActiveMenu(attr, path) {

  if (attr.length) {
    let has = false;
    for (let item of attr) {
      if (item.children && item.children.length) {
        if (findActiveMenu(item.children, path)) {
          return true;
        }
      }

      if (path.indexOf(item.path + '/') === 0 || path === item.path) {
        has = true;
        return true;
      }
    }
    return has
  }
  if (attr.children && attr.children.length) {
    if (findActiveMenu(attr.children, path)) {
      return true;
    }
  }
  if (path.indexOf(attr.path + '/') === 0 || path === attr.path) {
    return true;
  }
  return false;
}

function handleMenuItem(menus, oneArrList, topMenu) {
  menus.forEach((menu) => {
    menu.spm = topMenu ? topMenu.spm + '-' + menu.id : 'm-' + menu.id
    if (menu.children && menu.children.length) {
      handleMenuItem(menu.children, oneArrList, menu)
    }
    oneArrList[menu.spm] = menu

  })
}

// function handleMenus(menus) {
//
//   let oneArrList = []
//   handleMenuItem(menus,oneArrList,0)
// }


const state = () => ({
  isGenerate: false,
  routes: [],
  oneArrMenus: {},
  defaultOpenedPaths: [],
  headerActived: 0,
  currentPath:'',
  currentRemoveRoutes: []
})

const getters = {
  // 由于 getter 的结果不会被缓存，导致导航栏切换时有明显的延迟，该问题会在 Vue 3.2 版本中修复，详看 https://github.com/vuejs/vuex/pull/1883
  routes: (state, getters, rootState) => {
    let routes
    if (rootState.settings.menuMode === 'single') {
      routes = [{children: []}]
      state.routes.map(item => {
        routes[0].children.push(...item.children)
      })
    } else {
      routes = state.routes
    }
    return routes
  },
  sidebarRoutes: (state, getters) => {
    return getters.routes.length > 0 ? getters.routes[state.headerActived].children : []
  },
  sidebarRoutesFirstDeepestPath: (state, getters) => {
    return getters.routes.length > 0 ? getDeepestPath(getters.sidebarRoutes[0]) : '/'
  }
}

const actions = {
  // 根据权限动态生成路由（前端生成）
  generateRoutesAtFront({rootState, dispatch, commit}, asyncRoutes) {
    // eslint-disable-next-line no-async-promise-executor
    return new Promise(async resolve => {
      let accessedRoutes

      accessedRoutes = deepClone(asyncRoutes)
      commit('setRoutes', accessedRoutes)
      let routes = []
      accessedRoutes.map(item => {
        routes.push(...item.children)
      })
      // 将三级及以上路由数据拍平成二级
      routes.map(item => {
        if (item.children) {
          item.children = flatAsyncRoutes(item.children, [{
            path: item.path,
            title: item.meta.title
          }], item.path)
        }
      })
      commit('setDefaultOpenedPaths', routes)
      resolve(routes)
    })
  },
  // 生成路由（后端获取）
  generateRoutesAtBack({rootState, dispatch, commit}) {
    return new Promise(resolve => {
      Quick.request({url: 'admin/index/menu'}).then(async res => {
        let accessedRoutes = res.data
        commit('setRoutes', accessedRoutes)
        let routes = []
        accessedRoutes.map(item => {
          if (item.children) {
            routes.push(...item.children)
          } else {
            item.children = []
          }


        })
        // 将三级及以上路由数据拍平成二级
        routes.map(item => {
          if (item.children) {
            item.children = flatAsyncRoutes(item.children, [{
              path: item.path,
              title: item.meta.title
            }], item.path)
          }
        })

        commit('setDefaultOpenedPaths', routes)
        resolve(routes)
      })
    })
  }
}

const mutations = {
  invalidRoutes(state) {
    state.isGenerate = false
    state.routes = []
    state.defaultOpenedPaths = []
    state.headerActived = 0
  },
  setRoutes(state, routes) {
    state.isGenerate = true
    let newRoutes = deepClone(routes)
    let oneArrList = []
    handleMenuItem(newRoutes, oneArrList, 0)
    state.oneArrMenus = oneArrList
    state.routes = newRoutes.filter(item => {
      return item.children.length != 0
    })
  },
  setDefaultOpenedPaths(state, routes) {
    let defaultOpenedPaths = []
    routes.map(item => {
      item.meta.defaultOpened && defaultOpenedPaths.push(item.path)
      item.children && item.children.map(child => {
        child.meta.defaultOpened && defaultOpenedPaths.push(path.resolve(item.path, child.path))
      })
    })
    state.defaultOpenedPaths = defaultOpenedPaths
  },
  // 根据路由判断属于哪个头部导航
  setHeaderActived(state, path) {

    state.routes.map((item, index) => {
      if (findActiveMenu(item, state.currentPath)) {
        state.headerActived = index
      }
    })
  },
  // 切换头部导航
  switchHeaderActived(state, index) {
    state.headerActived = index
  },
  // 记录 accessRoutes 路由，用于登出时删除路由
  setCurrentRemoveRoutes(state, routes) {
    state.currentRemoveRoutes = routes
  },
  // 清空动态路由
  removeRoutes(state) {
    state.currentRemoveRoutes.forEach(removeRoute => {
      removeRoute()
    })
    state.currentRemoveRoutes = []
  },
  /**
   * 设置当前path
   * @param state
   * @param path
   * @param spm
   */
  setCurrentPath(state,to){

    if(to.query.spm && state.oneArrMenus[to.query.spm]){
      state.currentPath = state.oneArrMenus[to.query.spm].path
    }else{
      state.currentPath = to.path
    }

  },
}

export default {
  namespaced: true,
  state,
  actions,
  getters,
  mutations
}
