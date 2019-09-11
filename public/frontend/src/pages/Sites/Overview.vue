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
          <q-menu fit anchor="top left" self="top left">
            <q-item clickable>
              <q-item-section>Open in new window</q-item-section>
            </q-item>
            <q-item clickable>
              <q-item-section>Download QR</q-item-section>
            </q-item>
            <q-item clickable>
              <q-item-section>Open QR in dialog</q-item-section>
            </q-item>
          </q-menu>
        </q-card-section>

        <q-card-section class="col">
          <div class="text-body1">{{ item.name }}</div>
          <div class="text-body2">{{ item.short_url }}</div>
        </q-card-section>

        <q-separator />

        <q-card-actions class="col-auto">
          <q-btn flat :to="{ name: 'site.edit', params: { 'uuid': item.uuid } }" label="Edit" icon="edit"/>
          <q-space />
          <q-btn flat :to="{ name: 'site.edit', params: { 'uuid': item.uuid } }" label="View" icon-right="open_in_browser"/>
        </q-card-actions>
      </q-card>
    </div>

  </q-page>
</template>
<script>
import QRCode from 'vue-qrcode-component'

export default {
  name: 'SiteOverview',
  components: {
    QRCode
  },
  data () {
    return {
      sites: null
    }
  },
  beforeCreate () {
    var that = this
    this.$axios
      .get('sites')
      .then(response => {
        that.sites = response.data
      })
  },
  watch: {
  },
  methods: {
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
