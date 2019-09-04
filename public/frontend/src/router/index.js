import Vue from 'vue'
import VueRouter from 'vue-router'
import axios from 'axios'
import VueAxios from 'vue-axios'
import auth from '@websanova/vue-auth'

import routes from './routes'

Vue.use(VueRouter)

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation
 */

export default function (/* { store, ssrContext } */) {
  Vue.router = new VueRouter({
    scrollBehavior: () => ({ x: 0, y: 0 }),
    routes,

    // Leave these as is and change from quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    mode: process.env.VUE_ROUTER_MODE,
    base: process.env.VUE_ROUTER_BASE
  })

  Vue.use(VueAxios, axios)

  Vue.axios.defaults.baseURL = window.endpoint + '/api/'
  Vue.axios.defaults.headers.common = {
    'Accept': 'application/json',
    'Content-Type': 'application/json;charset=UTF-8',
    'X-Requested-With': 'XMLHttpRequest'
  }

  Vue.use(auth, {
    auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
    rolesVar: 'role',
    tokenDefaultName: 'token',
    tokenStore: ['localStorage'],
    registerData: { url: 'auth/register', method: 'POST', redirect: '/login' },
    loginData: { url: 'auth/login', method: 'POST', redirect: '/', fetchUser: true },
    logoutData: { url: 'auth/logout', method: 'POST', redirect: '/', makeRequest: true },
    fetchData: { url: 'auth/user', method: 'GET', enabled: true },
    refreshData: { url: 'auth/refresh', method: 'GET', enabled: true, interval: 30 },
    notFoundRedirect: { path: '/dashboard' } /* https://github.com/websanova/vue-auth/blob/master/docs/Privileges.md */
  })

  return Vue.router
}
