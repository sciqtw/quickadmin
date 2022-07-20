<template>
  <div>
    <json-render :render-data="content"></json-render>
    <json-render :render-data="content1"></json-render>
    <json-render :render-data="content2"></json-render>
    <json-render :render-data="content3"></json-render>
  </div>
</template>

<script setup>

  const content = {
    component:'div',
    props:{
      class:{myclass:true},
      style:{color:'red'}
    },
    children:'内容',
  }

  // 传参
  const content1 = {
    component:'el-button',
    children:'按钮',
    props:{
      type:'success'
    }
  }


  // 子组件
  const content2 = {
    component:'el-card',
    children:[
      {
        component:'div',
        children:'header',
        slot:'header'
      },
      content1
    ],
    props:{
      type:'success',
    }
  }

  const content3 = {
    "component":"el-card",
    "children":[
      {"component":"div","children":"header","props":{"slot":"header"},"slot":"header"},
      {"component":"el-button","children":"内容","props":{"type":"success"}}
      ],
    "props":{"type":"success","class":{"ttt":true}}
  }
</script>

