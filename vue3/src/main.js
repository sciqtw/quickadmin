import Quick from './Quick';

import 'element-plus/dist/index.css'
import 'element-plus/theme-chalk/display.css'
// 全局样式
import '@/assets/styles/globals.scss'
import '@/assets/styles/flex.scss'
import '@/assets/icons'

(function () {
  this.CreateQuick = function (config) {
    return new Quick(config);
  };
}.call(window));
