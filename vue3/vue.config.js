const path = require('path');

function resolve(dir) {
  return path.join(__dirname, dir);
}

var BrowserSyncPlugin = require('browser-sync-webpack-plugin');

const name = 'quick';
module.exports = {
  runtimeCompiler: true,
  productionSourceMap:true,
  pages: {
    index: {
      // page 的入口
      entry: 'src/main.js',
      // 模板来源
      // template: 'public/layout.html',
      template: process.env.NODE_ENV === 'production' ?
        'public/layout.html': 'public/index.html',
      // 在 dist/index.html 的输出
      filename:  process.env.NODE_ENV === 'production' ?
        '../../view/quick/quick/vue3.html':'index.html',
      // 当使用 title 选项时，
    }
  },
  devServer: {
    port: "8081",//代理端口
    open: false,//项目启动时是否自动打开浏览器，我这里设置为false,不打开，true表示打开
    proxy: {
      '/index.php': {//代理api
        target: "http://quicktest.com/",// 代理接口
        changeOrigin: true,//是否跨域
        ws: true, // proxy websockets
        pathRewrite: {//重写路径
          "^/index.php": 'http://quicktest.com/'//代理路径
        }
      },
      '/upload': {//代理api
        target: "http://quicktest.com/",// 代理接口
        changeOrigin: true,//是否跨域
        ws: true, // proxy websockets
        pathRewrite: {//重写路径
          "^/upload": 'http://testquick.com/upload'//代理路径
        }
      }
    }
  },
  publicPath: process.env.NODE_ENV === 'production' ? '/vue3/':'/',
  outputDir: path.join(__dirname, '../public/vue3'),
  chainWebpack(config) {
    // set svg-sprite-loader
    config.module
      .rule('svg')
      .exclude.add(resolve('src/assets/icons'))
      .end()
    config.module
      .rule('icons')
      .test(/\.svg$/)
      .include.add(resolve('src/assets/icons'))
      .end()
      .use('svg-sprite-loader')
      .loader('svg-sprite-loader')
      .options({
        symbolId: 'icon-[name]'
      })
      .end()
  },
  css: {
    loaderOptions: {
      sass: {
        prependData: `
        @import "@/assets/styles/resources/layout.scss";
        @import "@/assets/styles/resources/themes.scss";
        @import "@/assets/styles/resources/utils.scss";
        @import "@/assets/styles/resources/variables.scss";
        `
      }
    }
  },
  configureWebpack: {
    name,
    plugins: [
      // new BrowserSyncPlugin({
      //   host: 'localhost',
      //   port: 8083,
      //   proxy: 'http://quickadmin.com/',
      //   // files: '../public/vue3'
      // }),
    ],
    resolve: {
      alias: {
        '@': resolve('src'),
      },
    },
    externals:  process.env.NODE_ENV  === 'production' ? {
      vue: "Vue",
      // 'element-plus':"ElementPlus",
    }:{}
  },
};
