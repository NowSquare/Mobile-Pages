<template>
  <q-page class="fit row wrap justify-start items-start content-start q-pr-lg q-pb-lg bg-light-blue-8">
    <div
      v-for="(item, index) in sites" :key="index"
      class="col-6 col-sm-4 col-md-3 col-lg-2"
    >
      <q-card
        class="card text-blue-grey-9 bg-blue-grey-1 q-ml-lg q-mt-lg column"
      >
        <q-card-section class="col-auto">
          <QRCode
            class="qr-100 q-mb-xs"
            :text="item.short_url"
            color="#000000"
            bg-color="transparent"
            error-level="Q"
          />
        </q-card-section>

        <q-card-section class="col" style="max-width: 100%">
          <div class="text-body1 ellipsis-3-lines">{{ item.name }}</div>
        </q-card-section>

        <q-separator style="min-height: 1px;" />

        <q-card-actions class="col-auto">
          <q-btn flat :to="{ name: 'site.edit', params: { 'uuid': item.uuid } }" label="Edit" icon="mdi-square-edit-outline"/>
          <q-space />
          <q-btn flat size="12px" round color="grey-10" icon="more_vert">
            <q-menu>
              <q-list style="min-width: 150px">
                <q-item clickable v-close-popup @click="openURL(item.short_url)">
                  <q-item-section>View site</q-item-section>
                  <q-item-section avatar>
                    <q-icon name="open_in_new" />
                  </q-item-section>
                </q-item>
                <q-separator />
                <q-item clickable v-close-popup class="text-red" @click="deleteSite(item)">
                  <q-item-section>Delete site</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </q-card-actions>
      </q-card>
    </div>

    <div
      class="col-6 col-sm-4 col-md-3 col-lg-2"
    >
      <q-card
        class="card text-grey-9 bg-grey-2 q-ml-lg q-mt-lg column"
      >
        <q-card-section class="col-auto text-center">
          <q-icon name="mdi-qrcode" size="148px" class="text-grey-5 q-mt-md"/>
        </q-card-section>

        <q-card-section class="col text-center">
        </q-card-section>

        <q-separator style="min-height: 1px;" />

        <q-card-actions class="col-auto full-width">
          <q-btn flat class="full-width" color="grey-10" :to="{ name: 'site.new' }" label="New site" icon="add"/>
          <q-space />
        </q-card-actions>
      </q-card>
    </div>

  </q-page>
</template>
<script>
import { openURL, debounce } from 'quasar'
import QRCode from 'vue-qrcode-component'

export default {
  name: 'SiteOverview',
  components: {
    QRCode
  },
  data () {
    return {
      sites: {}
    }
  },
  created () {
    this.loadSites()

    window.addEventListener(
      'resize',
      debounce(() => {
        this.setMaxCardHeight()
      }, 300)
    )
  },
  methods: {
    setMaxCardHeight () {
      let maxHeight = 0
      let cards = document.getElementsByClassName('card')
      for (let i = 0; i < cards.length; i++) {
        cards[i].style.height = null
      }
      for (let i = 0; i < cards.length; i++) {
        if (parseInt(cards[i].offsetHeight) > maxHeight) {
          maxHeight = parseInt(cards[i].offsetHeight)
        }
      }
      for (let i = 0; i < cards.length; i++) {
        cards[i].style.height = maxHeight + 'px'
      }
    },
    openURL (url) {
      openURL(url)
    },
    loadSites () {
      this.$q.loading.show({
        delay: 40 // ms
      })
      this.$axios
        .get('sites')
        .then(response => {
          this.$q.loading.hide()
          this.sites = response.data
          this.$nextTick(() => {
            this.setMaxCardHeight()
          })
        })
    },
    deleteSite (site) {
      this.$root.$confirm('Do you want to delete "' + site.name + '"? This cannot be undone.', { icon: 'delete', agreeLabel: 'Delete', width: 420 }).then((confirm) => {
        if (confirm) {
          this.$q.loading.show({
            delay: 40 // ms
          })
          this.$axios.post('site/delete-site', {
            locale: this.$i18n.locale,
            siteUuid: site.uuid
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
                this.loadSites()
                this.$nextTick(() => {
                  this.setMaxCardHeight()
                })
              }
            })
            .catch(err => {
              let res = err.response.data
              console.log(res)
            })
            .finally(() => {
            })
        }
      })
    }
  },
  computed: {
  }
}
</script>
<style>
  .qr-100 img,
  .qr-100 canvas {
    width: 100% !important;
    max-width: 100% !important;
  }
</style>
