
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
    path: '/login',
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
