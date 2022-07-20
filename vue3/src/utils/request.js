import axios from 'axios';
import get from 'lodash/get';
import router from '@/router';

// const Quick = {};
// create an axios instance

const service = axios.create({
  // baseURL:process.env.NODE_ENV === "production" ? '/': '/', // url = base url + request url
  baseURL: window?.config?.base || 'index.php', // url = base url + request url
  // withCredentials: true, // send cookies when cross-domain requests
  timeout: 15000, // request timeout

});


service.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// service.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector(
//   'meta[name="csrf-token"]'
// ).content

// service.defaults.headers.common['quick-module'] = 'd';

// response interceptor
service.interceptors.response.use(
  (response) => {
    const res = response.data;

    // if the custom code is not 20000, it is judged as an error.
    if (res.code) {
      if (res.code === 419) {

        localStorage.removeItem('account')
        localStorage.removeItem('token')
        localStorage.removeItem('avatar')

        router.push({
          path: '/login'
        })

      }else{
        Quick.message({
          message: res.msg || 'Error',
          type: 'error',
          duration: 5 * 1000,
        });
      }
      return Promise.reject(response);
      // return Promise.reject(new Error(res.message || 'Error'));
    }
    return res;
  },
  (error) => {
    const status = get(error, 'response.status');


    // Show the user a 500 error
    if (status >= 500) {
      // console.log('-------------error',error)
      Quick.message({
        message: get(error, 'response.data.msg') || 'Error',
        type: 'error',
        duration: 5 * 1000,
      });
    }

    // Handle Session Timeouts
    if (status === 401) {
      // window.location.href = window.config.base;
      router.push({
        url: '/login'
      })
    }

    // Handle Forbidden
    if (status === 403) {
      router.push({name: '404'});
    }

    if (status === 404) {
      router.push({name: '404'});
    }

    // Handle Token Timeouts
    if (status === 419) {
      // todo
    }
    console.log('---------d', error.response);
    return Promise.reject(error);
  },
);

export default service;
