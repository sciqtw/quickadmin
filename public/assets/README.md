插件管理模块
===============
```
    example
    ├── application    //此文件夹中所有文件会覆盖到根目录的`/app`文件夹
    |── controller // 接口控制器 前端页面
    |── components // 组件插件 此文件夹中所有文件会覆盖到根目录的`/components/example`文件夹中
    |── model // 模型
    |── library // 第三方类库
    |── resource  // quick 后端管理
    │    └── expmlle.php 
    |── tools  // 资源工具 如：前端组件 定制的field column
    │    └── dist //打包文件
    │    └── js
    |       └── components //组件文件夹
    │    └── sass
    |── actions // 动作类
    │    └── example //对应resource 文件夹资源类
    │       └── deleteAction.php
    |── config // 配置
    │       └── quick.php
    |── route // 路由
    |── view // 视图
    │    └── index
    |       └── index.html
    |── assets // 插件安装时将自动复制到`/public/assets/plugins/插件名`目录下
    │   └── css
    │       └── style.css
    |── public //此文件夹中所有文件会覆盖到根目录的/public文件夹
    |── package.json // 
    |── webpack.mix.js //
    |── Plugin.php //此文件为插件核心安装卸载控制器,必需存在
    |── middleware.php 
    |── QuickService.php // quick启动服务 属于thinkservice启动服务
    
```
# 插件功能
    1. 插件可能只是一个quick工具类
    2. 插件后台功能可能插入平台侧边栏
        1. 现在resource是根据目录来加载的，路由访问的时候会直接加载当前模块资源resource,直接加载此资源没有
        问题是静态组件的加载
        2. 安装插件之后再买挂载到侧边栏
    3. 插件可能独立存在，也可能直接插入平台侧边栏
    
    方案：
        1. 每一个应用都有独立空间，其他应用启动的时候可以把quick插入自己空间或者别人的工具
            插入到别人空间不不现实，插件注册统一注册在自己空间，其他人需要需要自己来拿
            平台特殊性，平台如何自动加载插件的资源，查询数据库检查插件是否启用，启用的插件自动加载进平台
        2. 应用quick组件也可以插入公用空间，公用空间资源需要插件主动去获取否则不输出
        
        平台启动的时候 其他插件模块需要启动注册
        输出是注册完成之后触发quick再去过滤获取
        
        quick 模块之间应该是独立的
        
        组件加载本质是
        1. 加载js css
            1. 
        2. 加载resource
        3. 路由
        
        插件不允许有service模块，应该是在中间件阶段进行启动
        
```
www  WEB部署目录（或者子目录）
├─app           应用目录
│  ├─app_name           应用目录
│  │  ├─common.php      函数文件
│  │  ├─controller      控制器目录
│  │  ├─quick           QuickAdmin目录
│  │  ├─model           模型目录
│  │  ├─view            视图目录
│  │  ├─config          配置目录
│  │  ├─route           路由目录
│  │  └─ ...            更多类库目录
│  │
│  ├─common.php         公共函数文件
│  └─event.php          事件定义文件
│  │
├─components            组件工具目录
│  │─component_name        组件目录
│  │  │─dist        
│  │  │─resources  
│  │  │  │─js    
│  │  │  │─sass
│  │  │  └─ ...   
│  │  │─route        
│  │  │─src        
│  │  │      
│  │  ├─.gitignore          
│  │  ├─package.json          
│  │  └─webpack.mix.js          
│  └─ComponentService.php          
│  │
├─plugins      插件应用目录
│  ├─plugin_name           插件应用
│  │  ├─common.php      函数文件
│  │  ├─controller      控制器目录
│  │  ├─quick           QuickAdmin目录
│  │  ├─model           模型目录
│  │  ├─view            视图目录
│  │  ├─config          配置目录
│  │  ├─route           路由目录
│  │  └─ ...            更多类库目录
│
├─config                全局配置目录
│  ├─app.php            应用配置
│  ├─cache.php          缓存配置
│  ├─console.php        控制台配置
│  ├─cookie.php         Cookie配置
│  ├─database.php       数据库配置
│  ├─filesystem.php     文件磁盘配置
│  ├─lang.php           多语言配置
│  ├─log.php            日志配置
│  ├─middleware.php     中间件配置
│  ├─route.php          URL和路由配置
│  ├─session.php        Session配置
│  ├─trace.php          Trace配置
│  └─view.php           视图配置
│
├─public                WEB目录（对外访问目录）
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重写
│
├─extend                扩展类库目录
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                Composer类库目录
├─.example.env          环境变量示例文件
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件

```

