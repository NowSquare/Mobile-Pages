
const routes = [
  {
    path: '/',
    component: () => import('layouts/Master.vue'),
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('pages/Index.vue')
      }
    ]
  },
  {
    path: '/auth/login',
    component: () => import('layouts/Master.vue'),
    children: [
      {
        path: '',
        name: 'login',
        component: () => import('pages/Auth/Login.vue')
      }
    ],
    meta: {
      auth: false
    }
  },
  {
    path: '/auth/password/reset',
    component: () => import('layouts/Master.vue'),
    children: [
      {
        path: '',
        name: 'password.email',
        component: () => import('pages/Auth/Password/Email.vue')
      }
    ],
    meta: {
      auth: false
    }
  },
  {
    path: '/auth/password/reset/:token',
    component: () => import('layouts/Master.vue'),
    children: [
      {
        path: '',
        name: 'password.reset',
        component: () => import('pages/Auth/Password/Reset.vue')
      }
    ],
    meta: {
      auth: false
    }
  },
  {
    path: '/user/profile',
    component: () => import('layouts/Master.vue'),
    children: [
      {
        path: '',
        name: 'user.profile',
        component: () => import('pages/User/Profile.vue')
      }
    ],
    meta: {
      auth: { roles: [1, 2], redirect: { name: 'login' }, forbiddenRedirect: '/' }
    }
  },
  {
    path: '/-/:siteSlug',
    name: 'site.home',
    component: () => import('pages/Sites/Site.vue')
  },
  {
    path: '/-/:siteSlug/:pageSlug',
    name: 'site.page',
    component: () => import('pages/Sites/Site.vue')
  },
  {
    path: '/sites',
    component: () => import('layouts/Master.vue'),
    children: [
      {
        path: '',
        name: 'sites.overview',
        component: () => import('pages/Sites/Overview.vue')
      }
    ],
    meta: {
      auth: { roles: [2], redirect: { name: 'login' }, forbiddenRedirect: '/' }
    }
  },
  {
    path: '/site/new',
    component: () => import('layouts/Master.vue'),
    children: [
      {
        path: '',
        name: 'site.new',
        component: () => import('pages/Sites/New.vue')
      }
    ],
    meta: {
      auth: { roles: [2], redirect: { name: 'login' }, forbiddenRedirect: '/' }
    }
  },
  {
    path: '/site/edit/:uuid',
    component: () => import('layouts/Master.vue'),
    children: [
      {
        path: '',
        name: 'site.edit',
        component: () => import('pages/Sites/Edit.vue')
      }
    ],
    meta: {
      auth: { roles: [2], redirect: { name: 'login' }, forbiddenRedirect: '/' }
    }
  }
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
