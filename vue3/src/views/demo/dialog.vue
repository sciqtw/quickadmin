<template>
  <div>
    <el-button @click="dialog">弹出dialog</el-button>
    <el-button @click="dialog1">弹出dialog,显示组件</el-button>
    <el-button @click="drawer">弹出drawer,显示组件</el-button>
    <el-button @click="dialogAsync">弹出dialogAsync 异步请求数据</el-button>
  </div>
</template>

<script setup>
  import test from "../test";

  const options = {
    title: '标题'
  }
  // 显示一个quick格式的json组件
  const dialog = function () {
    const jsonComponent = {
      'component': 'div',
      'children': '内容'
    }
    Quick.api.open(jsonComponent, options)
  }


  // 直接显示组件
  const dialog1 = function () {
    Quick.api.open(test, options)
  }


  const options2 = {
    component: 'drawer',
    width:'50%',
    title: '标题'
  }
  const drawer = function () {
    Quick.api.open(test, options2)
  }


  const options1 = {
    title: '标题',
    beforeClose: function (event, data, done) {
      done()
      console.log('-----beforeClose--', event, data)
    }
  }
  // 异步请求显示数据
  const dialogAsync = function () {
    const contentRequest = {
      'url': 'demo/index/read'
    }
    Quick.api.open(contentRequest, options1)
  }
</script>

