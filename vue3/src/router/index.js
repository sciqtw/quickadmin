import { createRouter, createWebHashHistory } from 'vue-router';
import NProgress from 'nprogress'; // progress bar
import routes from './routes';

import 'nprogress/nprogress.css'; // progress bar style
import store from '@/store'
import getPageTitle from "../utils/get-page-title";

NProgress.configure({ showSpinner: false }); // NProgress Configuration

// { base: window.config._baseUrl }
// eslint-disable-next-line no-use-before-define
const router = createRouters({ base: '' });

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  // eslint-disable-next-line no-use-before-define
  const newRouter = createRouters();
  router.matcher = newRouter.matcher; // reset router
}

export default router;

/**
 * Global router guard.
 *
 * @param {Route} to
 * @param {Route} from
 * @param {Function} next
 */
async function beforeEach(to, from, next) {
  // set page title
  document.title = getPageTitle(to.meta.title);

  NProgress.start();
  store.commit('menu/setCurrentPath', to)
  store.state.settings.menuMode !== 'single' && store.commit('menu/setHeaderActived', to.path)
  if(!store.state.settings.router){
    /********解决动态路由F5刷新空白问题 ******/
    store.state.settings.router = true
    next({...to,replace:true});
  }else{
    next();
  }


}

/**
 * Global after hook.
 *
 * @param {Route} to
 * @param {Route} from
 * @param {Function} next
 */
// eslint-disable-next-line no-unused-vars
async function afterEach(to, from, next) {
  // await router.app.$nextTick();
  NProgress.done();
}

/**
 * Resolve async components.
 *
 * @param  {Array} components
 * @return {Array}
 */
// eslint-disable-next-line no-unused-vars
function resolveComponents(components) {
  return Promise.all(
    components.map((component) => (typeof component === 'function' ? component() : component)),
  );
}

/**
 * Scroll Behavior
 *
 * @link https://router.vuejs.org/en/advanced/scroll-behavior.html
 *
 * @param  {Route} to
 * @param  {Route} from
 * @param  {Object|undefined} savedPosition
 * @return {Object}
 */
function scrollBehavior(to, from, savedPosition) {
  if (savedPosition) {
    return savedPosition;
  }

  if (to.hash) {
    return { selector: to.hash };
  }

  // const [component] = router.getMatchedComponents({ ...to }).slice(-1);
  //
  // if (component && component.scrollToTop === false) {
  //   return {};
  // }

  return { x: 0, y: 0 };
}

/**
 * The router factory
 */
function createRouters({ base }) {
  // eslint-disable-next-line no-shadow
  const router = createRouter({
    scrollBehavior,
    base,
    history: createWebHashHistory(),
    routes,
  });

  router.beforeEach(beforeEach);
  router.afterEach(afterEach);

  return router;
}
