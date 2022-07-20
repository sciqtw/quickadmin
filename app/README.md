插件管理模块
===============
```
    example
    ├── application    //此文件夹中所有文件会覆盖到根目录的`/app`文件夹
    |── controller // 接口控制器 前端页面
    |── components // 组件插件 此文件夹中所有文件会覆盖到根目录的`/components/example`文件夹中
    |── model // 模型
    |── resource  // quick 后端管理
    │    └── expmlle.php 
    |── tools  // 资源工具 如：前端组件 定制的field column
    │    └── js
            └── components //组件文件夹
    │    └── sass
    |── actions // 动作类
    │    └── example //对应resource 文件夹资源类
    │       └── deleteAction.php
    |── config // 配置
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
    |── Plugin.php //
    
```
