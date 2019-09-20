// Configuration for your app
// https://quasar.dev/quasar-cli/quasar-conf-js

module.exports = function (ctx) {
  return {
    // app boot file (/src/boot)
    // --> boot files are part of "main.js"
    supportIE: true,
    boot: [
      'i18n',
      'axios',
      'lodash'
    ],

    css: [
      'app.styl'
    ],

    extras: [
      // 'ionicons-v4',
      'mdi-v3',
      // 'fontawesome-v5',
      // 'eva-icons',
      // 'themify',
      // 'roboto-font-latin-ext', // this or either 'roboto-font', NEVER both!

      'roboto-font', // optional, you are not bound to it
      'material-icons' // optional, you are not bound to it
    ],

    framework: {
      // iconSet: 'ionicons-v4',
      lang: 'en-us', // Quasar language

      // all: true, // --- includes everything; for dev only!

      components: [
        'QLayout',
        'QHeader',
        'QFooter',
        'QBar',
        'QDrawer',
        'QPageContainer',
        'QPage',
        'QPageScroller',
        'QToolbar',
        'QToolbarTitle',
        'QBtn',
        'QIcon',
        'QList',
        'QItem',
        'QItemSection',
        'QItemLabel',
        'QTabs',
        'QTab',
        'QTabPanels',
        'QTabPanel',
        'QRouteTab',
        'QBadge',
        'QPageSticky',
        'QSpace',
        'QInput',
        'QEditor',
        'QField',
        'QMenu',
        'QTooltip',
        'QAvatar',
        'QSeparator',
        'QLinearProgress',
        'QScrollArea',
        'QCard',
        'QCardSection',
        'QCardActions',
        'QResizeObserver',
        'QInnerLoading',
        'QColor',
        'QPopupProxy',
        'QExpansionItem',
        'QTree',
        'QUploader',
        'QImg',
        'QDialog',
        'QCheckbox',
        'QBanner',
        'QForm',
        'QSelect',
        'QToggle',
        'QDate',
        'QTime'
      ],

      directives: [
        'Ripple',
        'ClosePopup'
      ],

      // Quasar plugins
      plugins: [
        'Notify',
        'Cookies',
        'Dialog',
        'Loading',
        'LocalStorage',
        'SessionStorage'
      ]
    },

    supportIE: true,

    htmlVariables: {
      config: ctx.dev ? '{"found":false,"app_name":"Mobile Pages","language":"en","locale":"en-US","timezone":"UTC","currency_code":"USD","app_host":"msb.test","app_scheme":"http","version":"1.0.0","config":{"pusher":{"key":"","app_id":"","options":{"cluster":"mt1","encrypted":true}}},"demo":true}' : '{!! $config !!}'
    },

    build: {
      distDir: '../public/assets',
      htmlFilename: '../../resources/views/app.blade.php',
      publicPath: '/assets',
      scopeHoisting: true,
      vueRouterMode: 'hash',
      // vueCompiler: true,
      // gzip: true,
      // analyze: true,
      // extractCSS: false,
      extendWebpack (cfg) {
        cfg.module.rules.push({
          enforce: 'pre',
          test: /\.(js|vue)$/,
          loader: 'eslint-loader',
          exclude: /node_modules/,
          options: {
            formatter: require('eslint').CLIEngine.getFormatter('stylish')
          }
        })
      }
    },

    devServer: {
      // https: true,
      // port: 8080,
      open: true // opens browser window automatically
    },

    // animations: 'all', // --- includes all animations
    animations: [],

    ssr: {
      pwa: false
    },

    pwa: {
      // workboxPluginMode: 'InjectManifest',
      // workboxOptions: {}, // only for NON InjectManifest
      manifest: {
        // name: 'App',
        short_name: 'Mobile Pages',
        description: 'Mobile Site Builder',
        display: 'standalone',
        orientation: 'portrait',
        background_color: '#ffffff',
        theme_color: '#027be3',
        icons: [
          {
            'src': 'statics/icons/icon-128x128.png',
            'sizes': '128x128',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-192x192.png',
            'sizes': '192x192',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-256x256.png',
            'sizes': '256x256',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-384x384.png',
            'sizes': '384x384',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-512x512.png',
            'sizes': '512x512',
            'type': 'image/png'
          }
        ]
      }
    },

    cordova: {
      // id: 'org.cordova.quasar.app',
      // noIosLegacyBuildFlag: true, // uncomment only if you know what you are doing
    },

    electron: {
      // bundler: 'builder', // or 'packager'

      extendWebpack (cfg) {
        // do something with Electron main process Webpack cfg
        // chainWebpack also available besides this extendWebpack
      },

      packager: {
        // https://github.com/electron-userland/electron-packager/blob/master/docs/api.md#options

        // OS X / Mac App Store
        // appBundleId: '',
        // appCategoryType: '',
        // osxSign: '',
        // protocol: 'myapp://path',

        // Windows only
        // win32metadata: { ... }
      },

      builder: {
        // https://www.electron.build/configuration/configuration

        // appId: 'app'
      }
    }
  }
}
