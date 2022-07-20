<template>
  <div class="layout">
    <div id="app-main">
      <Header/>
      <div class="wrapper">
        <div class="sidebar-container"
             :class="{'show': $store.state.settings.mode === 'mobile' && !$store.state.settings.sidebarCollapse}">
          <MainSidebar/>
          <SubSidebar/>
        </div>
        <div class="sidebar-mask"
             :class="{'show': $store.state.settings.mode === 'mobile' && !$store.state.settings.sidebarCollapse}"
             @click="$store.commit('settings/toggleSidebarCollapse')"/>
        <div class="main-container" :style="{'padding-bottom': $route.meta.paddingBottom}">
          <Topbar/>
          <div class="main loading_class" >
            <!--            <router-view> </router-view>-->
            <router-view v-slot="{ Component, route }">
              <transition name="main" mode="out-in" appear>
                <keep-alive :include="$store.state.keepAlive.list">
                  <component :is="Component" :key="route.path"/>
                </keep-alive>
              </transition>
            </router-view>
          </div>
          <Copyright v-if="showCopyright"/>
        </div>
      </div>
      <el-backtop :right="20" :bottom="20" title="回到顶部"/>
    </div>
    <!--            <BuyIt></BuyIt>-->
    <ThemeSetting/>
  </div>
</template>

<script setup>
  import {getCurrentInstance, computed, watch, onMounted, provide, nextTick, onUnmounted} from 'vue';
  import {useStore} from 'vuex';
  import {useRoute, useRouter} from 'vue-router';
  import ModalProvider from '../components/Modal/modal-provider'
  import Header from './components/Header/index.vue';
  import MainSidebar from './components/MainSidebar/index.vue';
  import SubSidebar from './components/SubSidebar/index.vue';
  import Topbar from './components/Topbar/index.vue';
  import Search from './components/Search/index.vue';
  import ThemeSetting from './components/ThemeSetting/index.vue';
  import BuyIt from './components/BuyIt/index.vue';
  import Copyright from './components/Copyright'

  const {proxy} = getCurrentInstance();
  const store = useStore();
  const routeInfo = useRoute();
  const router = useRouter();

  const showCopyright = computed(() => (typeof routeInfo.meta.copyright !== 'undefined' ? routeInfo.meta.copyright : store.state.settings.showCopyright));

  watch(() => store.state.settings.sidebarCollapse, (val) => {
    if (store.state.settings.mode === 'mobile') {
      if (!val) {
        document.querySelector('body').classList.add('hidden');
      } else {
        document.querySelector('body').classList.remove('hidden');
      }
    }
  });

  // watch(() => routeInfo.path, () => {
  //   if (store.state.settings.mode === 'mobile') {
  //     store.commit('settings/updateThemeSetting', {
  //       sidebarCollapse: true,
  //     });
  //   }
  // });

  onMounted(() => {
    proxy.$hotkeys('f5', (e) => {
      if (store.state.settings.enablePageReload) {
        e.preventDefault();
        reload();
      }
    });


    store.dispatch('user/getUserInfo').then(() => {
      Quick.$emit('admin_menu')
    }).catch(() => {

    });

  });
  provide('reload', reload);

  function reload() {

    router.push({
      name: 'reload',
    });
  }

  provide('switchMenu', switchMenu);

  function switchMenu(index) {
    store.commit('menu/switchHeaderActived', index);

    if (store.state.settings.switchSidebarAndPageJump) {

      router.push(store.getters['menu/sidebarRoutesFirstDeepestPath']);
    }
  }


  watch(() => store.state.settings.mode, () => {
    if (store.state.settings.mode === 'pc') {
      store.commit('settings/updateThemeSetting', {
        'sidebarCollapse': store.state.settings.sidebarCollapseLastStatus
      })
    } else if (store.state.settings.mode === 'mobile') {
      store.commit('settings/updateThemeSetting', {
        'sidebarCollapse': true
      })
    }
    document.body.setAttribute('data-mode', store.state.settings.mode)
  }, {
    immediate: true
  })

  watch([
    () => store.state.settings.menuMode,
    () => store.state.settings.sidebarCollapse
  ], () => setMenuMode(), {
    immediate: true
  })

  function setMenuMode() {
    document.body.removeAttribute('data-sidebar-no-collapse')
    document.body.removeAttribute('data-sidebar-collapse')
    if (store.state.settings.sidebarCollapse) {
      document.body.setAttribute('data-sidebar-collapse', '')
    } else {
      document.body.setAttribute('data-sidebar-no-collapse', '')
    }
    document.body.setAttribute('data-menu-mode', store.state.settings.menuMode)
  }

  watch([
    () => store.state.settings.enableDynamicTitle,
    () => store.state.settings.title
  ], () => setDocumentTitle(), {
    immediate: true
  })

  function setDocumentTitle() {
    if (store.state.settings.enableDynamicTitle && store.state.settings.title) {
      let title = store.state.settings.title
      document.title = `${title} - quick`
    } else {
      document.title = 'quick'
    }
  }

  const resize = _.debounce(function () {
    Quick.$emit('onresize')
  },100)

  const keydown = _.debounce(function(e){
    if(e.keyCode == 13){
      Quick.$emit('enter')
    }
  })


  onMounted(() => {

    window.addEventListener('keydown',keydown);

    Quick.$on('admin_menu',() => {
      Quick.request({url:  window?.config?.menuUrl || 'admin/index/menu'})
        .then((res) => {
          if (!res.code) {
            // formFields.value = res.data;
            store.dispatch('menu/generateRoutesAtFront', res.data)
            nextTick(() => {
              store.commit('menu/setCurrentPath', routeInfo)
              store.commit('menu/setHeaderActived', routeInfo.path)
            })
          }
        }).catch(() => {

      });
    })
    Quick.$on('onresize',() => {
      store.commit('settings/setMode', document.documentElement.clientWidth)
    })
    window.onresize = () => {
      resize()
    }
    window.onresize()
  })

  onUnmounted(() => {
    window.removeEventListener('keydown',keydown,false);
  })
</script>

<style lang="scss" scoped>
  // 侧边栏未折叠
  [data-sidebar-no-collapse] {
    .sidebar-container {
      width: calc(#{$g-main-sidebar-width} + #{$g-sub-sidebar-width});
    }

    .main-container {
      margin-left: calc(#{$g-main-sidebar-width} + #{$g-sub-sidebar-width});
    }

    // 没有主侧边栏
    &[data-menu-mode="head"],
    &[data-menu-mode="single"] {
      .sidebar-container {
        width: $g-sub-sidebar-width;
      }

      .main-container {
        margin-left: $g-sub-sidebar-width;
      }
    }
  }

  // 侧边栏折叠
  [data-sidebar-collapse] {
    .sidebar-container {
      width: calc(#{$g-main-sidebar-width} + 64px);
    }

    .main-container {
      margin-left: calc(#{$g-main-sidebar-width} + 64px);
    }

    // 没有主侧边栏
    &[data-menu-mode="head"],
    &[data-menu-mode="single"] {
      .sidebar-container {
        width: 64px;
      }

      .main-container {
        margin-left: 64px;
      }
    }
  }

  [data-mode="mobile"] {
    .sidebar-container {
      width: calc(#{$g-main-sidebar-width} + #{$g-sub-sidebar-width});
      transform: translateX(-#{$g-main-sidebar-width}) translateX(-#{$g-sub-sidebar-width});

      &.show {
        transform: translateX(0);
      }
    }

    .main-container {
      margin-left: 0;
    }

    &[data-menu-mode="head"] {
      .sidebar-container {
        width: calc(#{$g-main-sidebar-width} + #{$g-sub-sidebar-width});
        transform: translateX(-#{$g-main-sidebar-width}) translateX(-#{$g-sub-sidebar-width});

        &.show {
          transform: translateX(0);
        }
      }

      .main-container {
        margin-left: 0;
      }
    }

    &[data-menu-mode="single"] {
      .sidebar-container {
        width: $g-sub-sidebar-width;
        transform: translateX(-#{$g-sub-sidebar-width});

        &.show {
          transform: translateX(0);
        }
      }

      .main-container {
        margin-left: 0;
      }
    }
  }

  .layout {
    height: 100%;
  }

  #app-main {
    width: 100%;
    height: 100%;
    margin: 0 auto;
    transition: all 0.2s;
  }

  .wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    box-shadow: -1px 0 0 0 darken($g-main-bg, 10);

    .sidebar-container {
      position: fixed;
      z-index: 1010;
      top: 0;
      bottom: 0;
      display: flex;
      transition: transform 0.3s;
    }

    .sidebar-mask {
      position: fixed;
      z-index: 1000;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgb(0 0 0 / 50%);
      backdrop-filter: blur(2px);
      transition: all 0.2s;
      transform: translateZ(0);
      opacity: 0;
      visibility: hidden;

      &.show {
        opacity: 1;
        visibility: visible;
      }
    }

    .main-sidebar-container + .sub-sidebar-container {
      left: $g-main-sidebar-width;
    }

    .main-container {
      display: flex;
      flex-direction: column;
      min-height: 100%;
      transition: margin-left 0.3s;
      background-color: $g-main-bg;
      box-shadow: 1px 0 0 0 darken($g-main-bg, 10);

      .topbar-container {
        top: 0;
        z-index: 998;
      }

      .main {
        height: 100%;
        flex: auto;
        position: relative;
        padding: $g-topbar-height 0 0;
        overflow: hidden;
        transition: 0.3s;
      }

      .topbar-container + .main {
        padding: $g-topbar-height 0 0;
      }
    }
  }

  header + .wrapper {
    padding-top: $g-header-height;

    .sidebar-container {
      top: $g-header-height;

      :deep(.sidebar-logo) {
        display: none;
      }

      :deep(.el-menu) {
        padding-top: 0;
      }
    }

    .main-container {
      .topbar-container {
        top: $g-header-height;

        :deep(.user) {
          display: none;
        }
      }
    }
  }

  // 主内容区动画
  .main-enter-active {
    transition: 0.2s;
  }

  .main-leave-active {
    transition: 0.15s;
  }

  .main-enter-from {
    opacity: 0;
    margin-left: -20px;
  }

  .main-leave-to {
    opacity: 0;
    margin-left: 20px;
  }
</style>
