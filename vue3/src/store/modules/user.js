

const state = () => ({
    account: localStorage.account || '',
    avatar: localStorage.avatar || '',
    token: localStorage.token || '',
    failure_time: localStorage.failure_time || '',
    permissions: []
})

const getters = {
    isLogin: state => {
        let retn = false
        if (state.token) {
            if (new Date().getTime() < state.failure_time * 1000) {
                retn = true
            }
        }
        return retn
    }
}

const actions = {
    login({ commit }, data) {
        return new Promise((resolve, reject) => {
            // 通过 mock 进行登录
            // api.post('member/login', data, {
            //     baseURL: '/mock/'
            // }).then(res => {
            //     commit('setUserData', res.data)
            //     resolve()
            // }).catch(error => {
            //     reject(error)
            // })
        })
    },
    getUserInfo({ commit }, data) {
      return new Promise((resolve, reject) => {
        Quick.request({
          url: window?.config?.userInfoUrl || 'admin/passport/userInfo',
          method:"POST"
        }).then(res => {
            if(!res.code){
              commit('setUserData', res.data)
              resolve(res.data)
            }
            reject(res)
        }).catch(error => {
            console.log('------------',error)
            reject(error)
        })
      })
    },
    logout({ commit }) {

        Quick.request({
          url: window?.config?.logoutUrl ||  'admin/index/logout',
          method:"POST"
        }).then(res => {
          if(!res.code){
            commit('setUserData', {})
            return
          }
        }).catch(error => {

        })
        commit('removeUserData')
        commit('menu/invalidRoutes', null, { root: true })
        commit('menu/removeRoutes', null, { root: true })
    },
    // 获取我的权限
    getPermissions({ state, commit }) {
        return new Promise(resolve => {
            // 通过 mock 获取权限
            // api.get('member/permission', {
            //     baseURL: '/mock/',
            //     params: {
            //         account: state.account
            //     }
            // }).then(res => {
            //     commit('setPermissions', res.data.permissions)
            //     resolve(res.data.permissions)
            // })
        })
    },
    editPassword({ state }, data) {
        return new Promise(resolve => {
            // api.post('member/edit/password', {
            //     account: state.account,
            //     password: data.password,
            //     newpassword: data.newpassword
            // }, {
            //     baseURL: '/mock/'
            // }).then(() => {
            //     resolve()
            // })
        })
    }
}

const mutations = {
    setUserData(state, data) {
        localStorage.setItem('account', data.username)
        localStorage.setItem('avatar', data.avatar)
        // localStorage.setItem('failure_time', data.failure_time)
        state.account = data.username
        state.avatar = data.avatar
        // state.failure_time = data.failure_time
    },
    removeUserData(state) {
        localStorage.removeItem('account')
        localStorage.removeItem('token')
        localStorage.removeItem('avatar')
        localStorage.removeItem('failure_time')
        state.account = ''
        state.avatar = ''
        state.token = ''
        state.failure_time = ''
    },
    setPermissions(state, permissions) {
        state.permissions = permissions
    }
}

export default {
    namespaced: true,
    state,
    actions,
    getters,
    mutations
}
