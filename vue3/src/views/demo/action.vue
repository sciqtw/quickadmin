<template>
  <div>
    <quick-action :action="action" :params="params"  :before-close="beforeClose">
      <el-button>按钮</el-button>
    </quick-action>
    <quick-action :action="action" :params="params"  @response="handleResponse">
      <el-button>按钮response</el-button>
    </quick-action>

    <quick-action :action="asyncAction"> <el-button>asyncAction</el-button> </quick-action>
    <quick-action :action="action2"> <el-button>openInNewTab</el-button> </quick-action>
    <quick-action :action="download"> <el-button>download</el-button> </quick-action>
    <quick-action :action="pushAction" :params="{key1:'value1'}"> <el-button>pushAction</el-button> </quick-action>
    <quick-action :action="requestAction" :params="{key1:'value1'}"> <el-button>requestAction</el-button> </quick-action>
    <el-button @click="handleTest">测试</el-button>
  </div>
</template>

<script setup>
  import test from "../test3";
  import {getCurrentInstance} from "vue"
  import DialogContent from "./dialog-content"
  const params = {
    key:'1'
  }
  const beforeClose = function (event,data,bone) {
    bone()
    console.log('--beforeClose-1---',event,data)
  }

  const handleResponse = function (event,data) {
    console.log('-handleResponse--',event,data)
  }
  const action = {
    action: 'dialog',
    params: {
      config: {
        props: {
          title: 'dialog'
        }
      },
      content: DialogContent,
      // content: 'demo/index/read',
    }
  }

  const action2 = {
    action: 'openInNewTab',
    params: {
      url:'https:www.baidu.com'
    }
  }

  const download = {
    action: 'download',
    params: {
      link:'https:www.baidu.com',
      name:'baidu',
    }
  }

  const pushAction = {
    action: 'push',
    params: {
      path:'/dashboard',
      query:{key:'value'},
    }
  }

  const requestAction = {
    action: 'request',
    params: {
      url:'demo/test/read1',
      method:'post',
    }
  }

  const asyncAction = {
    action: 'dialog',
    params: {
      config: {
        props: {
          title: 'dialog'
        }
      },
      content: "demo/index/read"
    }
  }
  const {proxy} = getCurrentInstance()
  const handleTest =  () =>  {

    console.log('----------handleTest',proxy,proxy.$formItemMixin)
  }
</script>
