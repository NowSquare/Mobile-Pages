<template>
  <q-layout view="lHh Lpr fff">
    <q-header
      bordered
      class="bg-white text-grey-8"
      height-hint="64"
    >
      <q-toolbar style="height: 64px">
        <q-btn
          flat
          dense
          round
          @click="leftDrawerOpen = !leftDrawerOpen"
          aria-label="Menu"
          icon="menu"
          class="q-mx-md"
        />

        <q-toolbar-title v-if="$q.screen.gt.sm" shrink class="row items-center no-wrap">
          <span class="q-ml-sm">Mobile Site Builder</span>
        </q-toolbar-title>

        <q-space />

        <div class="q-gutter-sm row items-center no-wrap" v-if="!$auth.check()">
          <q-btn flat :to="{ name: 'home' }">
            Home
          </q-btn>
          <q-btn flat>
            About
          </q-btn>
        </div>

        <div class="q-gutter-sm row items-center no-wrap" v-if="$auth.check()">
          <q-btn flat label="All Sites" :to="{ name: 'sites.overview' }" icon="apps"/>
          <q-btn flat label="New Site" :to="{ name: 'sites.overview' }" icon="add"/>
        </div>

        <q-space />

        <div class="q-gutter-sm row items-center no-wrap">
          <q-btn round flat v-if="$auth.check()">
            <q-avatar size="26px">
              <img :src="$auth.user().avatar">
            </q-avatar>
            <q-menu>
              <q-list style="min-width: 100px">
                <q-item clickable v-close-popup>
                  <q-item-section>Profile</q-item-section>
                </q-item>
                <q-separator />
                <q-item clickable v-close-popup @click="$auth.logout()">
                  <q-item-section>Logout</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
          <q-btn round flat v-if="!$auth.check()" :to="{ name: 'login' }">
            <q-icon name="mdi-login-variant" />
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      bordered
      :width="260"
      behavior="mobile"
      @click="leftDrawerOpen = false"
      class="bg-white text-grey-9"
    >
      <q-scroll-area class="fit">
        <q-toolbar>
          <q-toolbar-title class="row items-center text-grey-8">
            <span class="q-ml-sm">Options</span>
          </q-toolbar-title>
        </q-toolbar>

        <q-list padding>
          <q-item v-for="link in links1" :key="link.text" clickable>
            <q-item-section avatar>
              <q-icon :name="link.icon" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ link.text }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator class="q-my-md" />

          <q-item v-for="link in links2" :key="link.text" clickable>
            <q-item-section avatar>
              <q-icon :name="link.icon" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ link.text }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator class="q-my-md" />

          <q-item clickable>
            <q-item-section avatar>
              <q-icon name="power" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Logout</q-item-label>
            </q-item-section>
          </q-item>

        </q-list>
      </q-scroll-area>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
    <confirm ref="confirm"></confirm>
  </q-layout>
</template>

<script>
import Confirm from 'components/Confirm'

export default {
  name: 'MasterLayout',
  components: {
    Confirm
  },
  mounted () {
    this.$root.$confirm = this.$refs.confirm.open
  },
  data () {
    return {
      leftDrawerOpen: false,
      search: '',
      storage: 0.26,
      links1: [
        { icon: 'photo', text: this.$t('new_') },
        { icon: 'photo_album', text: 'Albums' },
        { icon: 'assistant', text: 'Assistant' },
        { icon: 'people', text: 'Sharing' },
        { icon: 'book', text: 'Photo books' }
      ],
      links2: [
        { icon: 'archive', text: 'Archive' },
        { icon: 'delete', text: 'Trash' }
      ],
      links3: [
        { icon: 'settings', text: 'Settings' },
        { icon: 'help', text: 'Help & Feedback' },
        { icon: 'get_app', text: 'App Downloads' }
      ]
    }
  },
  methods: {
  }
}
</script>

<style>
</style>
