<template>
  <q-page class="flex">

    <div class="fit q-pa-lg q-gutter-lg row">
      <q-card
        v-for="(item, index) in sites" :key="index"
        class="small-card text-grey-9 column"
      >
        <q-card-section class="col-auto">
          <QRCode
            class="qr-100"
            :text="item.short_url"
            color="#000000"
            bg-color="transparent"
            error-level="Q"
          />
        </q-card-section>

        <q-card-section class="col">
          <div class="text-body1">{{ item.name }}</div>
        </q-card-section>

        <q-separator style="min-height: 1px;" />

        <q-card-actions class="col-auto">
          <q-btn flat :to="{ name: 'site.edit', params: { 'uuid': item.uuid } }" label="Edit" icon="mdi-square-edit-outline"/>
          <q-space />
          <q-btn flat round color="grey-10" icon="more_vert">
            <q-menu>
              <q-list style="min-width: 100px">
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

      <q-btn
        v-if="sites.length === 0"
        class="small-card text-grey-10"
        style="height: 280px"
        label="Create a new site"
        icon="mdi-qrcode"
        size="32px"
        no-caps
        :to="{ name: 'site.new' }"
      />
    </div>

  </q-page>
</template>
<script>
import { openURL } from 'quasar'
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
  },
  watch: {
  },
  methods: {
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
            site_uuid: site.uuid
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
  .small-card {
    width: 196px !important;
  }
</style>
