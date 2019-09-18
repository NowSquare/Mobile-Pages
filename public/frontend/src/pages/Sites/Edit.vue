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
          <q-form id="frmSite" @submit.prevent.stop="saveSite" autocorrect="off" autocapitalize="off" autocomplete="off" spellcheck="false" class="fit">
            <q-toolbar>
              <q-toolbar-title class="text-body1 text-center"> {{ site.pages[0].name }}<span v-if="siteChangesDetected"> *</span></q-toolbar-title>
              <q-btn flat round dense v-if="siteChangesDetected" type="submit">
                <q-icon name="save" class="text-grey-9" />
                <q-tooltip>
                {{ $t('save_changes') }}
                </q-tooltip>
              </q-btn>
              <q-btn flat round dense v-if="siteChangesDetected" @click="undoSiteChanges">
                <q-icon name="undo" class="text-grey-9" />
                <q-tooltip>
                {{ $t('undo_changes') }}
                </q-tooltip>
              </q-btn>
              <q-btn flat round dense>
                <q-icon name="more_vert" class="text-grey-9" />
                <q-menu
                  transition-show="jump-down"
                  transition-hide="jump-up"
                >
                  <q-list style="min-width: 120px">
                    <q-item clickable @click="openURL(site.host + '/#/' + site.path)">
                      <q-item-section>View site</q-item-section>
                      <q-item-section avatar>
                        <q-icon name="open_in_new" />
                      </q-item-section>
                    </q-item>
                    <q-separator />
                    <q-item clickable class="text-red" @click="deleteSite">
                      <q-item-section>Delete site</q-item-section>
                    </q-item>
                  </q-list>
                </q-menu>
              </q-btn>
            </q-toolbar>
            <q-separator />
            <q-tabs
                v-model="siteTab"
                class="text-blue-grey-9 bg-blue-grey-1"
                active-color="light-blue-10"
                indicator-color="light-blue-10"
                align="justify"
                narrow-indicator
              >
              <q-tab name="pages" label="Pages" />
              <q-tab name="add_page" label="Add Page" />
              <q-tab name="site" label="Site" />
              <q-tab name="design" label="Design" />
            </q-tabs>
            <q-separator />
            <div class="fit">
              <q-scroll-area class="fit">
                <q-tab-panels v-model="siteTab" animated keep-alive>
                  <q-tab-panel name="pages">
                    <div class="fit q-pa-xs q-gutter-md row">
<!--
---------------------------------------------------------------------------------------------------
Site - Tree
---------------------------------------------------------------------------------------------------
-->
                      <q-tree
                        style="user-select: none"
                        :nodes="site.pages"
                        :selected.sync="globals.currentPage"
                        selected-color="primary"
                        node-key="uuid"
                        label-key="name"
                        :expanded.sync="treeExpandedKeys"
                        @update:selected="checkForPageChanges"
                      >
                        <template v-slot:header-move="prop">
                          <div class="row items-center">
                            <q-icon :name="prop.node.icon" class="q-mr-xs"/>
                            <div v-if="prop.node.position !== 'single'">
                              <q-btn flat icon="arrow_upward" :disabled="prop.node.position === 'first'" size="9px" color="grey-9" class="q-pa-none" @click="movePageConfirm(prop.node.uuid, 'up')">
                                <q-tooltip>Move page up</q-tooltip>
                              </q-btn>
                              <q-btn flat icon="arrow_downward" :disabled="prop.node.position === 'last'" size="9px" color="grey-9" class="q-pa-none q-mr-xs" @click="movePageConfirm(prop.node.uuid, 'down')">
                                <q-tooltip>Move page down</q-tooltip>
                              </q-btn>
                            </div>
                            <div>{{ prop.node.name }}</div>
                          </div>
                        </template>
                      </q-tree>
                    </div>
                  </q-tab-panel>
<!--
---------------------------------------------------------------------------------------------------
Site - Add page
---------------------------------------------------------------------------------------------------
-->
                  <q-tab-panel name="add_page">
                    <div class="fit q-pa-xs q-gutter-lg row">
                      <q-btn class="site-page" round flat :color="item.color" stack no-caps size="32px" v-for="(item, index) in pageModules" :key="index" @click="addPage(item)">
                        <q-icon size="28px" class="q-mb-xs" :name="item.icon" />
                        <div class="title" style="line-height: 18px">{{ item.label }}</div>
                        <q-tooltip>
                        {{ item.description }}
                        </q-tooltip>
                      </q-btn>
                    </div>
                  </q-tab-panel>
<!--
---------------------------------------------------------------------------------------------------
Site - Site tab
---------------------------------------------------------------------------------------------------
-->
                  <q-tab-panel name="site">
                    <q-input
                      v-model="site.pages[0].name"
                      label="Name"
                      ref="siteName"
                      name="siteName"
                      maxlength="64"
                      :error="(typeof errorBag.siteName !== 'undefined') ? errorBag.siteName.error : false"
                      :error-message="(typeof errorBag.siteName !== 'undefined') ? errorBag.siteName.errorMsg : null"
                    />
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
                            label="Background color"
                            v-model="site.design.bgColor"
                          />

                          <ColorPicker
                            label="Text color"
                            v-model="site.design.textColor"
                          />

                          <ImageUpload
                            ref="imgSiteBg"
                            label="Background image"
                            name="imgSiteBg"
                            v-model="site.design.imgSiteBg"
                            key="imgSiteBg"
                            :error="(typeof errorBag.imgSiteBg !== 'undefined') ? errorBag.imgSiteBg.error : false"
                            :error-message="(typeof errorBag.imgSiteBg !== 'undefined') ? errorBag.imgSiteBg.errorMsg : null"
                            @filename="(val) => { site.design.imgSiteBgFileName = val }"
                            :default-value="site.design.imgSiteBgFileName"
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
                            label="Background color"
                            v-model="site.design.headerBgColor"
                          />

                          <ColorPicker
                            label="Text color"
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
                            label="Background color"
                            v-model="site.design.titleBarBgColor"
                          />

                          <ColorPicker
                            label="Text color"
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

                          <div class="text-body2 text-weight-bold">Menu background</div>

                          <ColorPicker
                            label="Background color"
                            v-model="site.design.drawerBgColor"
                          />

                          <ColorPicker
                            label="Text color"
                            v-model="site.design.drawerTextColor"
                          />

                          <div class="q-mt-md text-body2 text-weight-bold">Active button</div>

                          <ColorPicker
                            label="Background color"
                            v-model="site.design.drawerActiveBgColor"
                          />

                          <ColorPicker
                            label="Text color"
                            v-model="site.design.drawerActiveTextColor"
                          />

                        </div>
                      </q-expansion-item>
                    </q-list>
                  </q-tab-panel>
                </q-tab-panels>
                <q-separator />
              </q-scroll-area>
            </div>
          </q-form>
        </div>
        <q-inner-loading :showing="leftColumnLoading"></q-inner-loading>
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
            <q-toolbar-title class="text-body1 text-center"> {{ sitePage.name }}<span v-if="pageChangesDetected"> *</span></q-toolbar-title>
            <q-btn flat round dense v-if="pageChangesDetected" type="submit">
              <q-icon name="save" class="text-grey-9" />
              <q-tooltip>
              {{ $t('save_changes') }}
              </q-tooltip>
            </q-btn>
            <q-btn flat round dense v-if="pageChangesDetected" @click="undoPageChanges">
              <q-icon name="undo" class="text-grey-9" />
              <q-tooltip>
              {{ $t('undo_changes') }}
              </q-tooltip>
            </q-btn>
            <q-btn flat round dense>
              <q-icon name="more_vert" class="text-grey-9" />
              <q-menu
                transition-show="jump-down"
                transition-hide="jump-up"
              >
                <q-list style="min-width: 120px">
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
            class="text-blue-grey-9 bg-blue-grey-1"
            active-color="light-blue-10"
            indicator-color="light-blue-10"
            align="justify"
            narrow-indicator
          >
            <q-tab name="page" label="Page" />
            <q-tab name="settings" label="Settings" />
          </q-tabs>
          <q-separator />
          <div class="fit">
            <q-scroll-area class="fit">
              <q-tab-panels v-model="pageTab" animated keep-alive>
<!--
---------------------------------------------------------------------------------------------------
Site - Page tab
---------------------------------------------------------------------------------------------------
-->
                <q-tab-panel name="page">

                  <q-input
                    ref="name"
                    v-model="sitePage.name"
                    label="Name"
                    name="name"
                    maxlength="64"
                    :error="(typeof errorBag.name !== 'undefined') ? errorBag.name.error : false"
                    :error-message="(typeof errorBag.name !== 'undefined') ? errorBag.name.errorMsg : null"
                  />

                  <ImageUpload
                    ref="imgAboveContent"
                    label="Image above content"
                    name="imgAboveContent"
                    v-model="sitePage.content.imgAboveContent"
                    :key="sitePage.uuid"
                    :error="(typeof errorBag.imgAboveContent !== 'undefined') ? errorBag.imgAboveContent.error : false"
                    :error-message="(typeof errorBag.imgAboveContent !== 'undefined') ? errorBag.imgAboveContent.errorMsg : null"
                    @filename="(val) => { sitePage.content.imgAboveContentFileName = val }"
                    :default-value="sitePage.content.imgAboveContentFileName"
                  />

                  <Editor
                    ref="content"
                    label="Content"
                    name="content"
                    v-model="sitePage.content.content"
                    :error="(typeof errorBag.content !== 'undefined') ? errorBag.content.error : false"
                    :error-message="(typeof errorBag.content !== 'undefined') ? errorBag.content.errorMsg : null"
                  />

                </q-tab-panel>
<!--
---------------------------------------------------------------------------------------------------
Site - Settings
---------------------------------------------------------------------------------------------------
-->
                <q-tab-panel name="settings">

                  <q-toggle
                    ref="showTitleBar"
                    label="Show title toolbar"
                    name="showTitleBar"
                    color="green"
                    v-model="sitePage.content.settings.showTitleBar"
                  />

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
import { openURL } from 'quasar'
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
      leftColumnLoading: true,
      rightColumnLoading: true,
      siteTab: 'pages',
      pageTab: 'page',
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
        short_url: null,
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
      undoSite: null,
      oldSitePages: null,
      undoSitePages: null,
      errorBag: {},
      pageModules: [
        {
          icon: 'notes',
          color: 'grey-9',
          label: 'Content',
          description: 'Basic content page.',
          module: 'Content',
          canBeHomePage: true,
          onlyOneInstanceAllowed: false
        }
      ]
    }
  },
  created () {
    this.globals.uuid = this.$route.params.uuid || null
    this.oldGlobals = this.copyObject(this.globals)
    this.loadSite()
  },
  beforeRouteLeave (to, from, next) {
    if (this.siteChangesDetected || this.pageChangesDetected) {
      this.$root.$confirm('Do you want to leave? Unsaved changes will be lost.', { icon: 'exit_to_app', width: 400, agreeLabel: 'Continue without saving' }).then((confirm) => {
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
    loadSite (selectedPageUuid = null) {
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
            this.site = response.data
            this.oldSite = this.copyObject(this.site)
            this.undoSite = this.copyObject(this.site)
            this.oldSitePages = this.copyObject(this.site.pages[0].children)
            this.undoSitePages = this.copyObject(this.site.pages[0].children)
            this.globals.currentPage = (selectedPageUuid !== null) ? selectedPageUuid : this.site.pages[0].children[0].uuid || null
            this.currentPage = (selectedPageUuid !== null) ? selectedPageUuid : this.site.pages[0].children[0].uuid || null

            /* Fill error bag for form validation */
            this.errorBag.name = {
              error: false,
              errorMsg: null
            }
            this.errorBag.siteName = {
              error: false,
              errorMsg: null
            }
            let fieldFound = []
            for (let design in this.oldSite.design) {
              if (!fieldFound.includes(design)) {
                fieldFound.push(design)
                this.errorBag[design] = {
                  error: false,
                  errorMsg: null
                }
              }
            }
            for (let page in this.oldSitePages) {
              for (let content in this.oldSitePages[page].content) {
                if (!fieldFound.includes(content)) {
                  fieldFound.push(content)
                  this.errorBag[content] = {
                    error: false,
                    errorMsg: null
                  }
                }
              }
            }
            this.$root.$emit('site', this.site)

            this.siteLoading = false
            this.leftColumnLoading = false
            this.rightColumnLoading = false
          }
        })
    },
    saveSite () {
      this.leftColumnLoading = true
      let siteData = this.copyObject(this.site)
      siteData.siteName = siteData.pages[0].name
      siteData.pages[0] = null

      let frmPage = new FormData()
      frmPage.append('locale', this.$i18n.locale)
      frmPage.append('site', JSON.stringify(siteData))
      frmPage.append('siteUuid', this.globals.uuid)

      this.$axios.post('site/save-site', frmPage, { headers: { 'content-type': 'multipart/form-data' } })
        .then(res => {
          if (typeof res.data.msg !== 'undefined') {
            this.$q.notify({
              icon: (res.data.status === 'success') ? 'done' : 'error',
              position: 'bottom-left',
              message: res.data.msg
            })
          }
          this.siteChangesDetected = false
        })
        .catch(err => {
          let res = err.response.data
          if (typeof res.errors !== 'undefined') {
            /* Get first error and select tab where error occurs */
            let field = Object.keys(res.errors)[0]
            let el = (typeof this.$refs[field] !== 'undefined') ? this.$refs[field] : null
            let tab = (el !== null) ? el.$parent.name : null
            this.siteTab = tab

            for (let field in res.errors) {
              this.errorBag[field].error = true
              this.errorBag[field].errorMsg = res.errors[field][0]
            }
          }
        })
        .finally(() => {
          this.leftColumnLoading = false
        })
    },
    deleteSite () {
      this.$root.$confirm('Do you want to delete this site? This cannot be undone.', { icon: 'delete', agreeLabel: 'Delete' }).then((confirm) => {
        if (confirm) {
          this.leftColumnLoading = true
          this.$axios.post('site/delete-site', {
            locale: this.$i18n.locale,
            siteUuid: this.globals.uuid
          })
            .then(res => {
              if (typeof res.data.msg !== 'undefined') {
                this.$q.notify({
                  icon: (res.data.status === 'success') ? 'done' : 'error',
                  position: 'bottom-left',
                  message: res.data.msg
                })
              }
              if (res.data.status === 'success') {
                this.$router.push({ name: 'sites.overview' })
              }
              this.pageChangesDetected = false
            })
            .catch(err => {
              let res = err.response.data
              console.log(res)
            })
            .finally(() => {
              this.leftColumnLoading = false
            })
        }
      })
    },
    undoSiteChanges () {
      this.site.design = this.copyObject(this.undoSite.design)
      for (let field in this.site.pages[0]) {
        if (field !== 'children' && field !== 'design') {
          this.site.pages[0][field] = this.undoSite.pages[0][field]
        }
      }
      this.$nextTick(() => {
        this.siteChangesDetected = false
      })
    },
    addPage (item) {
      this.$root.$prompt('Enter name for new page', { icon: item.icon }).then((confirm) => {
        if (confirm.submit && confirm.input !== null && confirm.input !== '') {
          this.leftColumnLoading = true
          this.$axios.post('site/add-page', {
            locale: this.$i18n.locale,
            siteUuid: this.globals.uuid,
            module: item,
            name: confirm.input
          })
            .then(res => {
              if (typeof res.data.msg !== 'undefined') {
                this.$q.notify({
                  icon: (res.data.status === 'success') ? 'done' : 'error',
                  position: 'bottom-left',
                  message: res.data.msg
                })
              }
              if (res.data.status === 'success') {
                this.siteTab = 'pages'
                this.loadSite(res.data.uuid)
              }
              this.pageChangesDetected = false
            })
            .catch(err => {
              let res = err.response.data
              this.$q.notify({
                icon: 'error',
                position: 'bottom-left',
                message: res.errors.name[0]
              })
            })
            .finally(() => {
              this.leftColumnLoading = false
            })
        }
      })
    },
    movePageConfirm (pageUuid, direction) {
      if (this.siteChangesDetected || this.pageChangesDetected) {
        this.$root.$confirm('Unsaved changes will be lost.', { icon: 'mdi-file-undo', width: 400, agreeLabel: 'Continue' }).then((confirm) => {
          if (confirm) {
            this.movePage(pageUuid, direction)
          }
        })
      } else {
        this.movePage(pageUuid, direction)
      }
    },
    movePage (pageUuid, direction) {
      this.leftColumnLoading = true
      this.$axios.post('site/move-page', {
        locale: this.$i18n.locale,
        pageUuid: pageUuid,
        direction: direction
      })
        .then(res => {
          if (typeof res.data.msg !== 'undefined') {
            this.$q.notify({
              icon: (res.data.status === 'success') ? 'done' : 'error',
              position: 'bottom-left',
              message: res.data.msg
            })
          }
          if (res.data.status === 'success') {
            this.loadSite()
          }
          this.siteChangesDetected = false
          this.pageChangesDetected = false
        })
        .catch(err => {
          let res = err.response.data
          console.log(res)
        })
        .finally(() => {
          this.leftColumnLoading = false
        })
    },
    deletePage () {
      if (parseInt(this.site.pages[0].children.length) === 1) {
        this.$root.$confirm('You can\'t delete this page, a site must have at least one page.', { icon: 'warning', showCancel: false, agreeColor: 'grey-9', agreeLabel: 'OK' }).then((confirm) => {
          if (confirm) {
          }
        })
      } else {
        this.$root.$confirm('Do you want to delete this page? This cannot be undone.', { icon: 'delete', agreeLabel: 'Delete' }).then((confirm) => {
          if (confirm) {
            this.rightColumnLoading = true
            this.$axios.post('site/delete-page', {
              locale: this.$i18n.locale,
              pageUuid: this.sitePage.uuid
            })
              .then(res => {
                if (typeof res.data.msg !== 'undefined') {
                  this.$q.notify({
                    icon: (res.data.status === 'success') ? 'done' : 'error',
                    position: 'bottom-left',
                    message: res.data.msg
                  })
                }
                if (res.data.status === 'success') {
                  this.loadSite()
                }
                this.pageChangesDetected = false
              })
              .catch(err => {
                let res = err.response.data
                console.log(res)
              })
              .finally(() => {
                this.rightColumnLoading = false
              })
          }
        })
      }
    },
    checkForPageChanges () {
      if (this.nextPage === null) this.nextPage = this.globals.currentPage

      if (this.pageChangesDetected) {
        this.globals.currentPage = this.currentPage
        this.$root.$confirm('Do you want to leave? Unsaved changes will be lost.', { icon: 'mdi-file-undo', width: 400, agreeLabel: 'Continue without saving' }).then((confirm) => {
          if (confirm) {
            this.site.pages[0].children = this.copyObject(this.undoSitePages)
            this.globals.currentPage = this.nextPage
            this.currentPage = this.globals.currentPage
            this.nextPage = null
            this.$nextTick(() => {
              this.pageChangesDetected = false
            })
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
      frmPage.append('siteUuid', this.globals.uuid)

      this.$axios.post('site/save-page', frmPage, { headers: { 'content-type': 'multipart/form-data' } })
        .then(res => {
          if (typeof res.data.msg !== 'undefined') {
            this.$q.notify({
              icon: (res.data.status === 'success') ? 'done' : 'error',
              position: 'bottom-left',
              message: res.data.msg
            })
          }
          this.pageChangesDetected = false
        })
        .catch(err => {
          let res = err.response.data
          if (typeof res.errors !== 'undefined') {
            /* Get first error and select tab where error occurs */
            let field = Object.keys(res.errors)[0]
            let el = (typeof this.$refs[field] !== 'undefined') ? this.$refs[field] : null
            let tab = (el !== null) ? el.$parent.name : null
            this.pageTab = tab

            for (let field in res.errors) {
              this.errorBag[field].error = true
              this.errorBag[field].errorMsg = res.errors[field][0]
            }
          }
        })
        .finally(() => {
          this.rightColumnLoading = false
        })
    },
    undoPageChanges () {
      this.site.pages[0].children = this.copyObject(this.undoSitePages)
      this.$nextTick(() => {
        this.pageChangesDetected = false
      })
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
    },
    openURL (url) {
      openURL(url)
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
<style>
  .site-page .title {
    font-size: 12px;
  }
</style>
