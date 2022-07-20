import {createApp} from 'vue';
import mitt from 'mitt';
import router from './router';
import store from './store';
import {routePush} from './utils/routePush';
import quickApi from './utils/quickApi'
import components from './components';
import Cookies from 'js-cookie'
import hotkeys from 'hotkeys-js'
import dayjs from 'dayjs'
import lodash from 'lodash'
import {useModal} from "./components/Modal/index";
import App from './App'

import axios from './utils/request'
import Localization from '@/mixins/Localization'
import FormItemMixin from '@/mixins/FormItemMixin'

export default class Quick {
  constructor(config) {
    this.bus = mitt();
    this.bootingCallbacks = [];
    this.config = config;
    this.api = quickApi;

  }

  /**
   * 注册一个要在nova启动前调用的回调。
   * 这用于引导加载项、工具、自定义字段或Nova需要的任何其他内容。
   */
  booting(callback) {
    this.bootingCallbacks.push(callback);
  }

  /**
   * 启动
   */
  boot() {
    this.bootingCallbacks.forEach((callback) => callback(this.app, router, store));
    this.bootingCallbacks = [];
  }

  /**
   *
   * @param resources
   */
  registerStoreModules(resources) {
    this.config.resources.forEach((resource) => {
      store.registerModule(resource.uriKey, resources);
    });
  }

  /**
   *
   */
  inits(apps, props, rootContainer) {

    if(apps){
      this.app = createApp(apps, props);
    }else{
      this.app = createApp(App);
    }



    this.app.config.globalProperties.$eventBus = mitt()
    this.app.config.globalProperties.$dayjs = dayjs
    this.app.config.globalProperties.$cookies = Cookies
    this.app.config.globalProperties.$hotkeys = hotkeys
    this.app.config.globalProperties.$lodash = lodash
    this.app.config.globalProperties.$useModal = useModal
    this.app.config.globalProperties.$routePush = routePush
    this.app.config.globalProperties.$formItemMixin = FormItemMixin;
    // this.app.config.globalProperties.$router = router

    this.app.mixin(Localization)
    // this.registerStoreModules();
    this.app.use(components);
    this.app.use(router);
    this.app.use(store);

    this.boot();
    if (rootContainer !== false) {
      if (!rootContainer) {
        rootContainer = '#app'
      }
      this.app.mount(rootContainer);
    }

    return this.app;

  }

  /**
   * 创建一个请求对象
   * @param options
   * @returns {*}
   */
  request(options) {
    if (options !== undefined) {
      return axios(options)
    }
    return axios
  }

  /**
   * 注册监听事件
   */
  $on(...args) {
    this.bus.on(...args);
  }

  /**
   *
   * @param args
   */
  $once(...args) {
    this.bus.once(...args);
  }

  /**
   *
   * @param args
   */
  $off(...args) {
    this.bus.off(...args);
  }

  /**
   *
   * @param args
   */
  $emit(...args) {
    this.bus.emit(...args);
  }

  /**
   *
   * @param args
   */
  $clear() {
    this.bus.all.clear();
  }

  /**
   *
   * @param message
   */
  error(message) {
    this.app.config.globalProperties.$message({
      type: 'error',
      message,
    });
  }

  /**
   *
   * @param message
   */
  message(message) {
    // console.log('quick-message', this.app)
    this.app.config.globalProperties.$message(message);
  }

  /**
   * success
   * @param message
   */
  success(message) {
    this.app.config.globalProperties.$message({
      type: 'success',
      message,
    });
  }

  /**
   * warning
   * @param message
   */
  warning(message) {
    this.app.config.globalProperties.$message({
      type: 'warning',
      message,
    });
  }


  /**
   *
   * @param uriKey
   * @returns {boolean}
   */
  missingResource(uriKey) {
    return window._.find(this.config.resources, (r) => r.uriKey === uriKey) === undefined;
  }
}
