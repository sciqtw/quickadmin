<template>
    <div class="user" >
        <div class="tools">
<!--            <span v-if="$store.state.settings.enableNavSearch" class="item" @click="$eventBus.emit('global-search-toggle')">-->
<!--                <svg-icon name="search" />-->
<!--            </span>-->
            <span v-if="$store.state.settings.mode === 'pc'
            && isFullscreenEnable && $store.state.settings.enableFullscreen" class="item" @click="toggle">
                <svg-icon  :icon="isFullscreen ? 'icon-fullscreen-exit' : 'icon-fullscreen'" />
            </span>
            <span v-if="$store.state.settings.enablePageReload" class="item" @click="reload()">
                <svg-icon icon="icon-toolbar-reload" />
            </span>
            <span v-if="$store.state.settings.enableThemeSetting" class="item" @click="$eventBus.emit('global-theme-toggle')">
                <svg-icon icon="icon-toolbar-theme" />
            </span>
        </div>

        <el-dropdown class="user-container" @command="userCommand">
            <div class="user-wrapper">
                <el-avatar size="default" :src="$store.state.user.avatar">
                    <el-icon ><el-icon-user-filled  /></el-icon>
                </el-avatar>
                {{ $store.state.user.account }}
<!--                <el-icon><el-icon-caret-bottom /></el-icon>-->
            </div>

            <template #dropdown>
                <el-dropdown-menu class="user-dropdown">
                  <template v-if="$store.state.settings.config && $store.state.settings.config.actions && $store.state.settings.config.actions.length">
                    <el-dropdown-item v-for="(action,index) in $store.state.settings.config.actions" :key="index" command="" >
                      <json-render :render-data="action" />
                    </el-dropdown-item>
                  </template>

<!--                    <el-dropdown-item v-if="$store.state.settings.enableDashboard" command="dashboard">控制台</el-dropdown-item>-->
<!--                    <el-dropdown-item command="setting">个人设置</el-dropdown-item>-->
                    <el-dropdown-item   command="logout">
                      <div style="text-align: center;width:100%;">{{__('Logout')}}</div>
                    </el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>
    </div>
</template>

<script setup>
  import { computed  ,inject ,getCurrentInstance} from 'vue';
  import { useStore } from 'vuex';


  import { useFullscreen } from '@vueuse/core'

  const { isFullscreen, toggle ,isSupported} = useFullscreen()

  const reload = inject('reload')
  const store = useStore()
  const {proxy} = getCurrentInstance()

  const isFullscreenEnable = computed(() => isSupported)

  function userCommand(command) {
      switch (command) {
          case 'dashboard':
            proxy.$routePush({
                  name: 'dashboard'
              })
              break
          case 'setting':
              proxy.$routePush({
                  path: '/setting',
                  // name: 'user_setting',
              })
              break
          case 'logout':
              store.dispatch('user/logout').then(() => {
                proxy.$routePush({
                      name: 'login'
                  })
              })
              break
      }
  }

</script>

<style lang="scss" scoped>
.user {
    display: flex;
    align-items: center;
    padding: 0 20px;
    white-space: nowrap;
}
.tools {
    margin-right: 20px;
    .item {
        margin-left: 5px;
        padding: 6px 8px;
        border-radius: 5px;
        outline: none;
        cursor: pointer;
        vertical-align: middle;
        transition: all 0.3s;
    }
    .item-pro {
        display: inline-block;
        transform-origin: right center;
        animation: pro-text 3s ease-out infinite;
        @keyframes pro-text {
            0%,
            20% {
                transform: scale(1);
            }
            50%,
            70% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }
        .title {
            padding-left: 5px;
            font-weight: bold;
            font-size: 14px;
            background-image: linear-gradient(to right, #ffa237, #fc455d);
            /* stylelint-disable-next-line property-no-vendor-prefix */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    }
}
:deep(.user-container) {
    display: inline-block;
    height: 50px;
    line-height: 50px;
    cursor: pointer;
    .user-wrapper {
        .el-avatar {
            vertical-align: middle;
            margin-top: -2px;
            margin-right: 4px;
        }
    }
}
</style>
