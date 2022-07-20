import { createStore, createLogger } from 'vuex'

import settings from './modules/settings';
import keepAlive from './modules/keepAlive';
import user from './modules/user';
import menu from './modules/menu';
import filters from './modules/filters';

export default createStore({
    modules: {
      'settings' :settings,
      'keepAlive' :keepAlive,
      'filters' :filters,
      'user' :user,
      'menu' :menu,
    },
    strict: false,
    plugins: !0 ? [createLogger()] : []
})
