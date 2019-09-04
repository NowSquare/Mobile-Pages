<template>
  <q-page class="flex">
<!--
---------------------------------------------------------------------------------------------------
Site
---------------------------------------------------------------------------------------------------
-->
    <div class="col bg-grey-1" v-show="$q.screen.gt.sm">
      <div class="flex fit relative-position">
        <div class="full-width" style="padding-bottom: 100px;" v-show="!siteLoading">
          <q-toolbar>
            <q-toolbar-title class="text-body1 text-center"> {{ site.pages[0].title }}<span v-if="siteChangesDetected"> *</span></q-toolbar-title>
            <q-btn flat round dense v-if="siteChangesDetected" @click="siteChangesDetected = false">
              <q-icon name="save" class="text-grey-9" />
              <q-tooltip>
              Save changes
              </q-tooltip>
            </q-btn>
            <q-btn flat round dense v-if="siteChangesDetected" @click="siteChangesDetected = false">
              <q-icon name="undo" class="text-grey-9" />
              <q-tooltip>
              Undo changes
              </q-tooltip>
            </q-btn>
            <q-btn flat round dense>
              <q-icon name="more_vert" class="text-grey-9" />
              <q-menu
                transition-show="jump-down"
                transition-hide="jump-up"
              >
                <q-list style="min-width: 120px">
                  <q-item clickable>
                    <q-item-section>Open site</q-item-section>
                  </q-item>
                  <q-item clickable>
                    <q-item-section>Show QR code</q-item-section>
                  </q-item>
                  <q-separator />
                  <q-item clickable class="text-red">
                    <q-item-section>Delete site</q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>
          </q-toolbar>
          <q-separator />
          <q-tabs
              v-model="siteTab"
              class="text-dark bg-blue-grey-1"
              active-color="primary"
              indicator-color="primary"
              align="justify"
              narrow-indicator
            >
            <q-tab name="pages" label="Pages" />
            <q-tab name="add_page" label="Add Page" />
            <q-tab name="design" label="Design" />
            <q-tab name="settings" label="Settings" />
          </q-tabs>
          <q-separator />
          <div class="fit">
            <q-scroll-area class="fit">
              <q-tab-panels v-model="siteTab" animated keep-alive>
                <q-tab-panel name="pages">
                  <div class="fit q-pa-xs q-gutter-md row">
                    <q-tree
                      :nodes="site.pages"
                      :selected.sync="globals.currentPage"
                      selected-color="primary"
                      node-key="uuid"
                      label-key="title"
                      :expanded.sync="treeExpandedKeys"
                      @update:selected="checkForPageChanges"
                    />
                  </div>
                </q-tab-panel>
                <q-tab-panel name="add_page">
                  <div class="fit q-pa-xs q-gutter-lg row">
                    <q-btn class="site-page" round flat :color="item.color" stack no-caps size="32px" v-for="(item, index) in pageModules" :key="index">
                      <q-icon size="28px" class="q-mb-xs" :name="item.icon" />
                      <div class="title" style="line-height: 18px">{{ item.text }}</div>
                      <q-tooltip>
                      {{ item.description }}
                      </q-tooltip>
                    </q-btn>
                  </div>
                </q-tab-panel>
                <q-tab-panel name="design" keep-alive>
                  <q-list bordered class="rounded-borders">
<!--
---------------------------------------------------------------------------------------------------
Site - Design tab - Background
---------------------------------------------------------------------------------------------------
-->
                    <q-expansion-item
                        v-model="expandedSiteDesignBackground"
                        dense-toggle
                        label="Site"
                        class="text-subtitle1"
                        header-class="bg-grey-1"
                      >
                      <q-separator />
                      <div class="q-pa-lg">

                        <ColorPicker
                          label="Site background color"
                          v-model="site.design.bgColor"
                        />

                        <ColorPicker
                          label="Site text color"
                          v-model="site.design.textColor"
                        />

                        <ImageUpload
                          label="Background image"
                          v-model="site.design.bgImg"
                          id="imgSiteDesignBackground"
                        />

                      </div>
                    </q-expansion-item>
<!--
---------------------------------------------------------------------------------------------------
Site - Design tab - Header
---------------------------------------------------------------------------------------------------
-->
                    <q-separator />
                    <q-expansion-item
                        v-model="expandedSiteDesignHeader"
                        dense-toggle
                        label="Header"
                        class="text-subtitle1"
                        header-class="bg-grey-1"
                      >
                      <q-separator />
                      <div class="q-pa-lg">

                        <ColorPicker
                          label="Header background color"
                          v-model="site.design.headerBgColor"
                        />

                        <ColorPicker
                          label="Header text color"
                          v-model="site.design.headerTextColor"
                        />

                      </div>
                    </q-expansion-item>
<!--
---------------------------------------------------------------------------------------------------
Site - Design tab - Title toolbar
---------------------------------------------------------------------------------------------------
-->
                    <q-separator />
                    <q-expansion-item
                        v-model="expandedSiteDesignTitleToolbar"
                        dense-toggle
                        label="Title toolbar"
                        class="text-subtitle1"
                        header-class="bg-grey-1"
                      >
                      <q-separator />
                      <div class="q-pa-lg">

                        <ColorPicker
                          label="Toolbar background color"
                          v-model="site.design.titleBarBgColor"
                        />

                        <ColorPicker
                          label="Toolbar text color"
                          v-model="site.design.titleBarTextColor"
                        />

                      </div>
                    </q-expansion-item>
<!--
---------------------------------------------------------------------------------------------------
Site - Design tab - Side navigation
---------------------------------------------------------------------------------------------------
-->
                    <q-separator />
                    <q-expansion-item
                        v-model="expandedSiteDesignSideNav"
                        dense-toggle
                        label="Side navigation"
                        class="text-subtitle1"
                        header-class="bg-grey-1"
                      >
                      <q-separator />
                      <div class="q-pa-lg">

                        <ColorPicker
                          label="Toolbar background color"
                          v-model="site.design.titleBarBgColor"
                        />

                        <ColorPicker
                          label="Toolbar text color"
                          v-model="site.design.titleBarTextColor"
                        />

                      </div>
                    </q-expansion-item>
                  </q-list>
                </q-tab-panel>
                <q-tab-panel name="settings">
                  <q-input
                    v-model="site.pages[0].title"
                    label="Site title"
                    maxlength="64"
                  />
                </q-tab-panel>
              </q-tab-panels>
              <q-separator />
            </q-scroll-area>
          </div>
        </div>
        <q-inner-loading :showing="siteLoading"></q-inner-loading>
      </div>
    </div>
    <q-separator vertical />
    <div class="col">
      <div class="flex fit">
        <div class="full-width relative-position">
          <SiteView/>
          <q-inner-loading :showing="siteLoading"></q-inner-loading>
        </div>
      </div>
    </div>
    <q-separator vertical />
<!--
---------------------------------------------------------------------------------------------------
Page
---------------------------------------------------------------------------------------------------
-->
    <div class="col relative-position bg-grey-1" v-show="$q.screen.gt.sm">
      <div class="flex fit" v-if="globals.currentPage !== null">
        <q-form id="frmPage" @submit.prevent.stop="savePage" autocorrect="off" autocapitalize="off" autocomplete="off" spellcheck="false" class="fit" style="padding-bottom: 100px;">
          <q-toolbar>
            <q-toolbar-title class="text-body1 text-center"> {{ sitePage.title }}<span v-if="pageChangesDetected"> *</span></q-toolbar-title>
            <q-btn flat round dense v-if="pageChangesDetected" type="submit">
              <q-icon name="save" class="text-grey-9" />
              <q-tooltip>
              Save changes
              </q-tooltip>
            </q-btn>
            <q-btn flat round dense v-if="pageChangesDetected" @click="undoPageChanges">
              <q-icon name="undo" class="text-grey-9" />
              <q-tooltip>
              Undo changes
              </q-tooltip>
            </q-btn>
            <q-btn flat round dense>
              <q-icon name="more_vert" class="text-grey-9" />
              <q-menu
                transition-show="jump-down"
                transition-hide="jump-up"
              >
                <q-list style="min-width: 120px">
                  <q-item clickable>
                    <q-item-section>Duplicate</q-item-section>
                  </q-item>
                  <q-separator />
                  <q-item clickable class="text-red" @click="deletePage">
                    <q-item-section>Delete page</q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>
          </q-toolbar>
          <q-separator />
          <q-tabs
            v-model="pageTab"
            class="text-dark bg-blue-grey-1"
            active-color="primary"
            indicator-color="primary"
            align="justify"
            narrow-indicator
          >
            <q-tab name="content" label="Content" />
            <q-tab name="settings" label="Settings" />
          </q-tabs>
          <q-separator />
          <div class="fit">
            <q-scroll-area class="fit">
              <q-tab-panels v-model="pageTab" animated keep-alive>
<!--
---------------------------------------------------------------------------------------------------
Site - Content tab
---------------------------------------------------------------------------------------------------
-->
                <q-tab-panel name="content">

                  <q-select
                    v-model="sitePage.order"
                    :options="pagePositions"
                    label="Position"
                    emit-value
                    map-options
                  />

                  <q-input
                    v-model="sitePage.title"
                    label="Title"
                    name="title"
                    maxlength="64"
                  />

                  <ImageUpload
                    label="Image above content"
                    :key="sitePage.id"
                    name="imgAboveContent"
                    v-model="sitePage.content.imgAboveContent"
                  />

                  <Editor
                    label="Content"
                    name="content"
                    v-model="sitePage.content.content"
                  />

                </q-tab-panel>
<!--
---------------------------------------------------------------------------------------------------
Site - Settings
---------------------------------------------------------------------------------------------------
-->
                <q-tab-panel name="settings">
                  <q-list bordered class="rounded-borders">
                    <q-expansion-item
                      v-model="expandedPageSettingsGeneral"
                      dense-toggle
                      label="General"
                      class="text-subtitle1"
                      header-class="bg-grey-1"
                    >
                      <q-separator />
                      <div class="q-pa-lg">

                        Settings

                      </div>
                      </q-expansion-item>
                    </q-list>
                  <q-separator />
                </q-tab-panel>
              </q-tab-panels>
              <q-separator />
            </q-scroll-area>
          </div>
        </q-form>
      </div>
      <q-inner-loading :showing="rightColumnLoading"></q-inner-loading>
    </div>
  </q-page>
</template>
<script>
import ColorPicker from 'components/ColorPicker'
import ImageUpload from 'components/ImageUpload'
import Editor from 'components/Editor'
import SiteView from 'pages/Sites/Site'

export default {
  name: 'PageIndex',
  components: {
    ColorPicker,
    ImageUpload,
    Editor,
    SiteView
  },
  data () {
    return {
      siteChangesDetected: false,
      pageChangesDetected: false,
      siteLoading: true,
      rightColumnLoading: true,
      siteTab: 'pages',
      pageTab: 'content',
      expandedSiteDesignBackground: false,
      expandedSiteDesignHeader: false,
      expandedSiteDesignTitleToolbar: false,
      expandedSiteDesignSideNav: false,
      expandedPageSettingsGeneral: false,
      treeExpandedKeys: [-1],
      globals: {
        uuid: null,
        currentPage: null
      },
      oldGlobals: null,
      currentPage: null,
      nextPage: null,
      site: {
        design: {
        },
        pages: [
          {
            uuid: -1,
            children: [
            ]
          }
        ]
      },
      oldSite: null,
      oldSitePages: null,
      undoSitePages: null,
      pageModules: [
        {
          icon: 'notes',
          color: 'grey-9',
          text: 'Content',
          description: 'Basic content page.',
          module: 'Content',
          canBeHomePage: true,
          onlyOneInstanceAllowed: false
        }
      ]
    }
  },
  mounted () {
    this.globals.uuid = this.$route.params.uuid || null
    this.oldGlobals = this.copyObject(this.globals)

    var that = this
    this.$axios
      .get('site', {
        params: {
          uuid: this.globals.uuid
        }
      })
      .then(response => {
        if (response.data.length === 0) {
          /* Site not found, redirect back */
          this.$router.push({ name: 'sites.overview' })
        } else {
          that.site = response.data
          that.oldSite = this.copyObject(that.site)
          that.oldSitePages = this.copyObject(that.site.pages[0].children)
          that.undoSitePages = this.copyObject(that.site.pages[0].children)
          that.siteLoading = false
          that.rightColumnLoading = false
          that.globals.currentPage = that.site.pages[0].children[0].uuid || null
          that.currentPage = that.site.pages[0].children[0].uuid || null
          this.$root.$emit('site', this.site)
        }
      })
  },
  beforeRouteLeave (to, from, next) {
    if (this.siteChangesDetected || this.pageChangesDetected) {
      this.$root.$confirm('Do you really want to leave? You have unsaved changes.', { icon: 'exit_to_app' }).then((confirm) => {
        if (confirm) {
          next()
        }
      })
    } else {
      next()
    }
  },
  watch: {
    globals: {
      handler (val, old) {
        let stringVal = String(JSON.stringify(val))
        let stringOld = String(JSON.stringify(this.oldGlobals))

        if (stringVal !== stringOld) {
          this.$root.$emit('globals', this.globals)
          this.oldGlobals = this.copyObject(this.globals)
        }
      },
      deep: true
    },
    site: {
      handler (val, old) {
        let stringVal = String(JSON.stringify(val))
        let stringOld = String(JSON.stringify(this.oldSite))

        let stringValSitePages = String(JSON.stringify(val.pages[0].children))
        let stringOldSitePages = String(JSON.stringify(this.oldSitePages))

        if (stringValSitePages !== stringOldSitePages) {
          this.pageChangesDetected = true
          this.$root.$emit('site', this.site)
          this.oldSite = this.copyObject(this.site)
          this.oldSitePages = this.copyObject(this.site.pages[0].children)
        } else if (stringVal !== stringOld) {
          this.siteChangesDetected = true
          this.$root.$emit('site', this.site)
          this.oldSite = this.copyObject(this.site)
          this.oldSitePages = this.copyObject(this.site.pages[0].children)
        }
      },
      deep: true
    }
  },
  methods: {
    addPage (module) {
      console.log(module)
    },
    deletePage () {
      this.$root.$confirm('Do you want to delete this page?', { icon: 'delete' }).then((confirm) => {
        if (confirm) {
          console.log('delete')
        }
      })
    },
    checkForPageChanges () {
      if (this.nextPage === null) this.nextPage = this.globals.currentPage

      if (this.pageChangesDetected) {
        this.globals.currentPage = this.currentPage
        this.$root.$confirm('Do you want to continue and lose all unsaved changes you have made to this page?', { icon: 'mdi-file-undo' }).then((confirm) => {
          if (confirm) {
            this.site.pages[0].children = this.copyObject(this.undoSitePages)
            this.globals.currentPage = this.nextPage
            this.currentPage = this.globals.currentPage
            this.nextPage = null
            setTimeout(() => {
              this.pageChangesDetected = false
            }, 100)
          } else {
            this.nextPage = null
          }
        })
      } else {
        this.undoSitePages = this.copyObject(this.site.pages[0].children)
        this.currentPage = this.globals.currentPage
        this.nextPage = null
      }
    },
    savePage () {
      this.rightColumnLoading = true
      let frmPage = new FormData()
      frmPage.append('locale', this.$i18n.locale)
      frmPage.append('page', JSON.stringify(this.sitePage))

      let that = this
      this.$axios.post('save-page', frmPage, { headers: { 'content-type': 'multipart/form-data' } })
        .then(res => {
          if (res.data.status === 'success') {
          }
        })
        .catch(err => {
          console.log(err)
        })
        .finally(() => {
          that.rightColumnLoading = false
        })

      this.pageChangesDetected = false
    },
    undoPageChanges () {
      this.site.pages[0].children = this.copyObject(this.undoSitePages)
      setTimeout(() => {
        this.pageChangesDetected = false
      }, 100)
    },
    copyObject (o) {
      let output, v, key
      output = Array.isArray(o) ? [] : {}
      for (key in o) {
        v = o[key]
        output[key] = (typeof v === 'object') ? this.copyObject(v) : v
      }
      return output
    },
    getAllPages (pages, allPages) {
      for (let page of pages) {
        allPages.push(page)
        if (typeof page.children !== 'undefined') {
          this.getAllPages(page.children, allPages)
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
    },
    pagePositions () {
      let allPages = this.getAllPages(this.site.pages[0].children, [])
      let response = [{
        label: 'Homepage',
        value: '0'
      }]
      for (let page in allPages) {
        response.push({
          label: 'Below "' + allPages[page].title + '"',
          value: String(parseInt(allPages[page].order) + 1)
        })
      }
      return response
    }
  }
}
</script>
<style>
  .site-page .title {
    font-size: 12px;
  }
</style>
