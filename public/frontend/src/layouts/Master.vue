<template>
  <q-layout view="lHh Lpr fff">
    <q-header
      bordered
      height-hint="57"
      class="bg-blue-grey-1 text-blue-grey-10"
    >
      <q-toolbar style="height: 57px">
        <q-btn
          v-if="$auth.check()"
          flat
          dense
          round
          @click="leftDrawerOpen = !leftDrawerOpen"
          aria-label="Menu"
          icon="menu"
          class="q-mx-md"
        />

        <q-toolbar-title shrink class="row items-center no-wrap" v-if="!$auth.check()">
          <span class="q-ml-sm">{{ window.config.app_name }}</span>
        </q-toolbar-title>

        <div class="q-gutter-sm row items-center no-wrap" v-if="$auth.check() && !leftDrawerOpen">
          <q-btn flat :label="$t('sites')" exact :to="{ name: 'sites.overview' }" icon="apps"/>
          <q-btn flat :label="$t('new_site')" exact :to="{ name: 'site.new' }" icon="add"/>
        </div>

        <q-space />

        <div class="q-gutter-sm row items-center no-wrap q-mr-sm" v-if="!$auth.check()">
          <q-btn flat :label="$t('home')" :to="{ name: 'home' }"/>
          <q-btn flat :label="$t('login')" :to="{ name: 'login' }" icon="mdi-login-variant"/>
        </div>

        <div class="q-gutter-sm row items-center no-wrap">
          <q-btn round flat v-if="$auth.check()">
            <q-avatar size="26px">
              <img :src="$auth.user().avatar">
            </q-avatar>
            <q-menu>
              <q-list style="min-width: 100px">
                <q-item clickable v-close-popup exact :to="{ name: 'user.profile' }">
                  <q-item-section>{{ $t('profile') }}</q-item-section>
                </q-item>
                <q-separator />
                <q-item clickable v-close-popup @click="$auth.logout()">
                  <q-item-section>{{ $t('logout') }}</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-if="$auth.check()"
      v-model="leftDrawerOpen"
      :width="250"
      :breakpoint="1023"
      content-class="bg-blue-grey-10 text-grey-1"
      content-style="pointer-events:all"
    >
      <q-scroll-area class="fit">

        <q-toolbar style="height: 57px">
          <q-toolbar-title class="row items-center text-grey-5">
            <span class="q-ml-sm">{{ window.config.app_name }} <span class="text-caption">v{{ window.config.version }}</span></span>
          </q-toolbar-title>
        </q-toolbar>

        <q-list class="text-blue-grey-3">
          <q-item v-for="link in links1" :key="link.text" clickable exact :to="{ name: link.to }" active-class="bg-blue-grey-9">
            <q-item-section avatar>
              <q-icon :name="link.icon" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ link.text }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator class="q-my-md bg-blue-grey-9" />

          <q-item clickable :to="{ name: 'user.profile' }" active-class="bg-blue-grey-10">
            <q-item-section avatar>
              <q-icon name="person" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ $t('profile') }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator class="q-my-md bg-blue-grey-9" />

          <q-item clickable @click="$auth.logout()" active-class="bg-blue-grey-10">
            <q-item-section avatar>
              <q-icon name="logout" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ $t('logout') }}</q-item-label>
            </q-item-section>
          </q-item>

        </q-list>
      </q-scroll-area>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
    <confirm ref="confirm"></confirm>
    <prompt ref="prompt"></prompt>
  </q-layout>
</template>

<script>
import Confirm from 'components/Confirm'
import Prompt from 'components/Prompt'

export default {
  name: 'MasterLayout',
  components: {
    Confirm,
    Prompt
  },
  mounted () {
    this.$root.$confirm = this.$refs.confirm.open
    this.$root.$prompt = this.$refs.prompt.open
  },
  created () {
    this.leftDrawerOpen = !this.$q.screen.lt.md
  },
  data () {
    return {
      leftDrawerOpen: false,
      links1: [
        /* { icon: 'home', text: this.$t('home'), to: 'home' }, */
        { icon: 'apps', text: this.$t('sites'), to: 'sites.overview' },
        { icon: 'add', text: this.$t('new_site'), to: 'site.new' }
      ]
    }
  },
  computed: {
    window () {
      return window
    }
  }
}
</script>

<style>
</style>
