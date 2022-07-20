/**
 * hidden: true                   if set true, item will not show in the sidebar(default is false)
 * redirect: noRedirect           if set noRedirect will no redirect in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    roles: ['admin','editor']    control the page roles (you can set multiple roles)
    title: 'title'               the name show in sidebar and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    badge: 'badge'               the badge show in the sidebar
    noCache: true                if set true, the page will no be cached(default is false)
    affix: true                  if set true, the tag will affix in the tags-view
    breadcrumb: false            if set false, the item will hidden in breadcrumb(default is true)
    activeMenu: '/example/list'  if set path, the sidebar will highlight the path you set
  }
 */

export default [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/login.vue'),
    meta: { title: 'login', noCache: true },
    hidden: true,
  },
  {
    path: '/test',
    name: 'test',
    component: () => import('@/views/test.vue'),
    meta: { title: 'test', noCache: true },
    hidden: true,
  },
  {
    path: '/demo',
    name: 'test',
    component: () => import('@/components/AppLayout.vue'),
    meta: { title: 'test', noCache: true },
    hidden: true,
    children:[
      {
        path: 'action',
        name: 'action',
        component: () => import('@/views/demo/action.vue'),
        meta: { title: 'action', noCache: true },
        hidden: true,
      },
      {
        path: 'dialog',
        name: 'dialog',
        component: () => import('@/views/demo/dialog.vue'),
        meta: { title: 'dialog', noCache: true },
        hidden: true,
      },

      {
        path: 'component',
        name: 'component',
        component: () => import('@/views/demo/component.vue'),
        meta: { title: 'component', noCache: true },
        hidden: true,
      }
    ]
  },
  {
    path: '/',
    name: 'index',
    component: () => import('@/components/AppLayout.vue'),
    props: route => ({
      lay_empty: route.query.lay_empty ,
      layout:route.query.layout ? route.query.layout:'quick-layout'
    }),
    meta: { title: 'index', noCache: true },
    redirect: '/dashboard',
    hidden: true,
    children:[
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@/views/resource-page.vue'),
        props: route => {
          return {
            moduleName: window?.config?.module || 'admin',
            resourceName: 'dashboard',
            actionName: 'index',
            func: ''
          }
        },
        meta: { title: 'dashboard', noCache: true },
        hidden: true,
      },
      {
        path: 'icons',
        name: 'icons',
        component: () => import('@/views/icons.vue'),
        meta: { title: 'icons', noCache: true },
        hidden: true,
      },

      {
        path: '/404',
        name: '404',
        component: () => import('@/views/404.vue'),
        meta: { title: 'page404', noCache: true },
        hidden: true,
      },
      {
        path: '/:moduleName/resource/:resourceName/:actionName',
        name: 'resource-page',
        component: () => import('@/views/resource-page.vue'),
        props: route => {
          return {
            moduleName: route.params.moduleName.replace('.', '/'),
            resourceName: route.params.resourceName,
            actionName: route.params.actionName,
            func: ''
          }
        }
      },
      {
        path: '/:moduleName/resource/:resourceName/:actionName/:func',
        name: 'resource-action',
        component: () => import('@/views/resource-page.vue'),
        props: route => {
          return {
            moduleName: route.params.moduleName.replace('.', '/'),
            resourceName: route.params.resourceName,
            actionName: route.params.actionName,
            func: route.params.func
          }
        }
      },
      {
        path: 'reload',
        name: 'reload',
        component: () => import('@/views/reload.vue')
      }
    ]
  },
  // { path: '/:pathMatch(.*)*', name: 'NotFound', redirect: '/404',hidden: true  }
];
