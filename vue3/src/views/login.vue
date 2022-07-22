<template>
  <div>
    <div class="bg-banner"/>
    <div id="login-box">
      <div class="login-banner"/>
      <el-form v-show="formType == 'login'" ref="loginFormRef" :model="loginForm" :rules="loginRules" class="login-form"
               autocomplete="on" label-position="left">
        <div class="title-container">
          <h3 class="title">{{ title }}</h3>
        </div>
        <div>
          <el-form-item prop="account">
            <el-input ref="name" v-model="loginForm.account" placeholder="用户名" type="text" tabindex="1"
                      autocomplete="on">
              <template #prefix>
                <svg-icon icon="icon-user"/>
              </template>
            </el-input>
          </el-form-item>
          <el-form-item prop="password">
            <el-input ref="password" v-model="loginForm.password" :type="passwordType" placeholder="密码" tabindex="2"
                      autocomplete="on" @keyup.enter="handleLogin">
              <template #prefix>
                <svg-icon icon="icon-password"/>
              </template>
              <template #suffix>
                <svg-icon :icon="passwordType === 'password' ? 'icon-eye' : 'icon-eye-open'" @click="showPassword"/>
              </template>
            </el-input>
          </el-form-item>
          <el-form-item prop="captcha" v-if="checkCaptcha">
            <el-row style="flex-wrap: nowrap;width:100%; ">
              <div style="flex-grow: 1;">
                <el-input ref="captcha" v-model="loginForm.captcha" placeholder="验证码" type="text"
                          @keyup.enter="handleLogin">
                  <template #prefix>
                    <svg-icon icon="icon-captcha"/>
                  </template>
                </el-input>
              </div>
              <el-image
                @click="changeCaptcha"
                style="width: 130px;padding-left:6px;border-radius: 5px;flex-shrink: 0;"
                :src="captchaUrl"
                :fit="'fill'"
              ></el-image>
            </el-row>
          </el-form-item>
        </div>
<!--        <div class="flex-bar">-->
<!--          <el-checkbox v-model="loginForm.remember">记住我</el-checkbox>-->
<!--          <el-button type="text" @click="formType = 'reset'">忘记密码</el-button>-->
<!--        </div>-->
        <el-button :loading="loading" type="primary" style="width: 100%;" @click.prevent="handleLogin">登 录</el-button>
        <div
          style="margin-top: 20px; margin-bottom: -10px; color: #666; font-size: 14px; text-align: center; font-weight: bold;"
          v-if="false">
          <span style="margin-right: 5px;">演示帐号一键登录：</span>
          <el-button type="danger" size="mini" @click="testAccount('admin')">admin</el-button>
        </div>
      </el-form>
      <el-form v-show="formType == 'reset'" ref="resetFormRef" :model="resetForm" :rules="resetRules" class="login-form"
               auto-complete="on" label-position="left">
        <div class="title-container">
          <h3 class="title">重置密码</h3>
        </div>
        <div>
          <el-form-item prop="account">
            <el-input ref="name" v-model="resetForm.account" placeholder="用户名" type="text" tabindex="1"
                      autocomplete="on">
              <template #prefix>
                <svg-icon name="user"/>
              </template>
            </el-input>
          </el-form-item>
          <el-form-item prop="captcha">
            <el-input ref="captcha" v-model="resetForm.captcha" placeholder="验证码" type="text" tabindex="2"
                      autocomplete="on">
              <template #prefix>
                <svg-icon name="captcha"/>
              </template>
              <template #append>
                <el-button>发送验证码</el-button>
              </template>
            </el-input>
          </el-form-item>
          <el-form-item prop="newPassword">
            <el-input ref="newPassword" v-model="resetForm.newPassword" :type="passwordType" placeholder="新密码"
                      tabindex="3" autocomplete="on">
              <template #prefix>
                <svg-icon name="password"/>
              </template>
              <template #suffix>
                <svg-icon :name="passwordType === 'password' ? 'eye' : 'eye-open'" @click="showPassword"/>
              </template>
            </el-input>
          </el-form-item>
        </div>
        <el-row :gutter="15" style="padding-top: 20px;">
          <el-col :md="6">
            <el-button style="width: 100%;" @click="formType = 'login'">去登录</el-button>
          </el-col>
          <el-col :md="18">
            <el-button :loading="loading" type="primary" style="width: 100%;" @click.prevent="handleFind">确 认
            </el-button>
          </el-col>
        </el-row>
      </el-form>
    </div>
    <!--    <Copyright v-if="$store.state.settings.showCopyright" />-->
  </div>
</template>

<script setup name="Login">
  import {getCurrentInstance, ref, onMounted, watch, computed} from 'vue';
  import {useStore} from "vuex"
  import {useRoute, useRouter} from 'vue-router'

  const {proxy} = getCurrentInstance()
  const store = useStore();
  const route = useRoute(), router = useRouter();

  const title = window?.config?.appName || "QuickAdmin"

  // 表单类型，login 登录，reset 重置密码
  const formType = ref('login')

  const loginForm = ref({
    account: localStorage.login_account || '',
    captcha: '',
    password: '',
    remember: !!localStorage.login_account
  })


  const resetForm = ref({
    account: localStorage.login_account || '',
    captcha: '',
    newPassword: ''
  })
  const resetRules = ref({
    account: [
      {required: true, trigger: 'change', message: '请输入用户名'}
    ],
    captcha: [
      {required: true, trigger: 'change', message: '请输入验证码'}
    ],
    newPassword: [
      {required: true, trigger: 'change', message: '请输入新密码'},
      {min: 6, max: 18, trigger: 'change', message: '密码长度为6到18位'}
    ]
  })

  const loading = ref(false)
  const passwordType = ref('password')
  const redirect = ref(null)

  const baseUrl = window?.config?.base || 'index.php'
  const checkCaptcha = ref(false)
  const captchaUrl = ref(baseUrl + '/admin/captcha')
  const changeCaptcha = function () {
    captchaUrl.value = baseUrl + '/admin/captcha?v=v' + Math.random()
  }

  const loginRules = computed(() => {

    let rules = {
      account: [
        {required: true, trigger: 'blur', message: '请输入用户名'}
      ],
      password: [
        {required: true, trigger: 'blur', message: '请输入密码'},
        {min: 6, max: 18, trigger: 'blur', message: '密码长度为6到18位'}
      ]
    };
    if (checkCaptcha.value) {
      rules.captcha = [
        {required: true, trigger: 'blur', message: '请输入验证码'}
      ]
    }
    return rules
  })


  onMounted(() => {
    redirect.value = route.query.redirect ?? location.origin + location.pathname
    if (localStorage.account) {
      window.location = redirect.value
    }
  })

  function showPassword() {
    passwordType.value = passwordType.value === 'password' ? '' : 'password'
    nextTick(() => {
      proxy.$refs.password.focus()
    })
  }

  function handleLogin() {
    proxy.$refs.loginFormRef.validate(valid => {
      if (valid) {
        loading.value = true
        Quick.request({
          url: window?.config?.loginUrl || "admin/passport/login",
          method: 'POST',
          data: loginForm.value
        }).then((res) => {
          loading.value = false

          if (res.code === 0) {
            if (loginForm.value.remember) {
              localStorage.setItem('login_username', loginForm.value.username)
            } else {
              localStorage.removeItem('login_username')
            }
            window.location = redirect.value
          } else {
            checkCaptcha.value = true
          }


        }).catch(() => {
          checkCaptcha.value = true
          changeCaptcha()
          loading.value = false
        })

      }
    })
  }

  function handleFind() {
    proxy.$message({
      message: '重置密码仅提供界面演示，无实际功能，需开发者自行扩展',
      type: 'info'
    })
    proxy.$refs.resetFormRef.validate(valid => {
      if (valid) {
        // 这里编写业务代码
      }
    })
  }

  function testAccount(account) {
    loginForm.value.account = account
    loginForm.value.password = '123456'
    handleLogin()
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

  onMounted(() => {

    checkCaptcha.value = window?.config?.checkCaptcha
    window.onresize = () => {
      store.commit('settings/setMode', document.documentElement.clientWidth)
    }
    window.onresize()
  })
</script>

<style lang="scss" scoped>
  [data-mode="mobile"] {
    #login-box {
      max-width: 80%;

      .login-banner {
        display: none;
      }
    }
  }

  :deep(input[type="password"]::-ms-reveal) {
    display: none;
  }

  .bg-banner {
    position: absolute;
    z-index: 0;
    width: 100%;
    height: 100%;
    background-image: url("../assets/images/login-bg.jpg");
    background-size: cover;
    background-position: center center;
  }

  #login-box {
    display: flex;
    justify-content: space-between;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
    background: rgb(255 255 255 / 80%);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 5px #999;

    .login-banner {
      width: 300px;
      background-image: url("../assets/images/login-banner.jpg");
      background-size: cover;
      background-position: center center;
    }

    .login-form {
      width: 450px;
      padding: 50px 35px 30px;
      overflow: hidden;

      .title-container {
        position: relative;

        .title {
          font-size: 22px;
          color: #666;
          margin: 0 auto 30px;
          text-align: center;
          font-weight: bold;
          @include text-overflow;
        }
      }
    }

    :deep(.el-input) {
      height: 48px;
      line-height: inherit;
      width: 100%;

      input {
        height: 48px;
      }

      .el-input__prefix,
      .el-input__suffix {
        display: flex;
        align-items: center;
      }

      .el-input__prefix {
        left: 10px;
      }

      .el-input__suffix {
        right: 10px;
      }
    }

    .flex-bar {
      display: flex;
      justify-content: space-between;
    }

    .el-checkbox {
      margin: 20px 0;
    }
  }

  .copyright {
    position: absolute;
    bottom: 30px;
    width: 100%;
    margin: 0;
    mix-blend-mode: difference;
  }
</style>
