<template>
  <div v-if="globals.currentPage !== null">
    <q-layout view="lhh LpR lff" container :style="{'height': (parseInt($q.screen.height) - 58) + 'px', 'background-color': site.design.bgColor, 'color': site.design.textColor, 'background-image': 'url(' + site.design.imgSiteBg + ')'}" class="siteBg">
      <q-header reveal :style="{'background-color': site.design.headerBgColor, 'color': site.design.headerTextColor}">
        <q-toolbar>
          <q-btn flat @click="drawerLeft = !drawerLeft" round dense icon="menu" />
          <q-toolbar-title>{{ site.pages[0].name }}</q-toolbar-title>
        </q-toolbar>
      </q-header>

      <q-drawer
        v-model="drawerLeft"
        :width="260"
        :breakpoint="700"
        content-class="shadow-1"
        :content-style="{'background-color': site.design.drawerBgColor}"
      >

        <q-scroll-area class="fit">
          <div class="q-pa-sm">

            <q-item v-for="page in getPages()" :key="page.id" clickable :style="{'color': site.design.drawerTextColor}">
              <q-item-section>
                <q-item-label>{{ page.name }}</q-item-label>
              </q-item-section>
            </q-item>

          </div>
        </q-scroll-area>
      </q-drawer>

      <q-page-container>
        <q-page :style="{ 'padding-top': pagePaddingTop + 'px' }" class="q-pa-md">

          <q-page-sticky style="z-index:999" position="top" expand :style="{'background-color': site.design.titleBarBgColor, 'color': site.design.titleBarTextColor}" v-if="sitePage.content.settings.showTitleBar">
            <q-toolbar>
              <q-toolbar-title>{{ sitePage.name }}</q-toolbar-title>
            </q-toolbar>
          </q-page-sticky>

          <q-img
            v-if="sitePage.content.imgAboveContent"
            class="q-mb-md rounded-borders shadow-1"
            :src="sitePage.content.imgAboveContent"
            transition="fade"
            spinner-color="white"
          />

          <div v-html="sitePage.content.content"/>

        </q-page>

      </q-page-container>
    </q-layout>
    </div>
</template>

<style>
  .siteBg {
    background-repeat: no-repeat;
    background-position: center center;
    background-attachment: fixed;
    background-size: cover;
  }
</style>

<script>
export default {
  name: 'MobileSite',
  data () {
    return {
      drawerLeft: false,
      slug: null,
      globals: {
        currentPage: null
      },
      site: {
        design: {
          headerBgColor: null
        },
        page: 0,
        pages: [
          {
            name: null,
            children: [
              {
                name: null,
                content: null
              }
            ]
          }
        ]
      }
    }
  },
  created () {
    this.slug = this.$route.params.slug || null

    if (this.slug !== null) {
      this.$q.loading.show({
        delay: 40 // ms
      })
      this.loadSite()
    } else {
      var that = this
      this.$root.$on('site', function (site) {
        that.site = site
        this.$q.loading.hide()
      })
      this.$root.$on('globals', function (globals) {
        that.globals = globals
      })
    }
  },
  methods: {
    getAllPages (pages, allPages) {
      for (let page of pages) {
        allPages.push(page)
        if (typeof page.children !== 'undefined') {
          this.getAllPages(page.children, allPages)
        }
      }
      return allPages
    },
    getPages (parentId = null) {
      let allPages = []
      if (parentId === null) {
        for (let page of this.site.pages[0].children) {
          allPages.push(page)
        }
      }
      return allPages
    },
    loadSite () {
      this.$axios
        .get('site-by-slug', {
          params: {
            slug: this.slug
          }
        })
        .then(response => {
          if (response.data.length === 0) {
            /* Site not found */
          } else {
            this.$q.loading.hide()
          }
        })
    }
  },
  computed: {
    sitePage () {
      let allPages = this.getAllPages(this.site.pages[0].children, [])
      let getPageByUuid = this.$_.find(allPages, ['uuid', this.globals.currentPage])

      return getPageByUuid || null
    },
    pagePaddingTop () {
      return (this.sitePage.content.settings.showTitleBar) ? 66 : 15
    }
  }
}
</script>
