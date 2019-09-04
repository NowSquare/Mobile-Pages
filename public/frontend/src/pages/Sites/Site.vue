<template>
  <div v-if="globals.currentPage !== null">
    <q-layout view="lhh LpR lff" container :style="{'height': (parseInt($q.screen.height) - 66) + 'px', 'background-color': site.design.bgColor, 'color': site.design.textColor}">
      <q-header reveal :style="{'background-color': site.design.headerBgColor, 'color': site.design.headerTextColor}">
        <q-toolbar>
          <q-btn flat @click="drawerLeft = !drawerLeft" round dense icon="menu" />
          <q-toolbar-title>{{ site.pages[0].title }}</q-toolbar-title>
        </q-toolbar>
      </q-header>

      <q-drawer
        v-model="drawerLeft"
        :width="260"
        :breakpoint="700"
        bordered
        content-class="bg-grey-3"
      >
        <q-scroll-area class="fit">
          <div class="q-pa-sm">

            <q-item v-for="page in getPages()" :key="page.id" clickable>
              <q-item-section>
                <q-item-label>{{ page.title }}</q-item-label>
              </q-item-section>
            </q-item>

          </div>
        </q-scroll-area>
      </q-drawer>

      <q-page-container>
        <q-page style="padding-top: 66px" class="q-pa-md">

          <q-page-sticky style="z-index:999" position="top" expand :style="{'background-color': site.design.titleBarBgColor, 'color': site.design.titleBarTextColor}">
            <q-toolbar>
              <q-toolbar-title>{{ sitePage.title }}</q-toolbar-title>
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
</style>

<script>
export default {
  name: 'MobileSite',
  data () {
    return {
      drawerLeft: false,
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
            title: null,
            children: [
              {
                title: null,
                content: null
              }
            ]
          }
        ]
      }
    }
  },
  beforeCreate () {
    var that = this
    this.$root.$on('site', function (site) {
      that.site = site
    })
    this.$root.$on('globals', function (globals) {
      that.globals = globals
    })
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
    }
  },
  computed: {
    sitePage () {
      let allPages = this.getAllPages(this.site.pages[0].children, [])
      let getPageByUuid = this.$_.find(allPages, ['uuid', this.globals.currentPage])

      return getPageByUuid || null
    }
  }
}
</script>
