# 组件管理

组件目录下的
```
├── plugins 
│   └── test          //组件test
│     └── style.css
├── assets
│   └── css
│     └── style.css
├── controllers
│   └── IndexController.php
├── models
│   └── DemoPost.php
├── tree.txt
└── views
    └── index
        └── index.php
```

`admin/resource/article/index`
quick 资源注册，资源输出
采用模块加载方式
    1. 平台场景，使用了平台模块a资源b资源 
    
    使用平台关键字过滤输出
    
    模块下面有注册方法，只有注册的组件允许获取
    注册命名 模块名称_组件名称