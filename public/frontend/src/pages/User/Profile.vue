<template>
  <q-page class="fit row justify-center q-pa-lg bg-blue-8">
    <div class="col-12">
      <div class="fit self-center">
        <q-card flat class="no-border-radius bg-grey-1 text-grey-10 full-width">
          <q-card-section>
            <div class="row items-center no-wrap">
              <div class="col">
                <div class="text-h5">{{ $t('profile') }}</div>
              </div>
            </div>

            <q-banner class="bg-red text-white q-mt-lg" v-if="errorMsg">
              <template v-slot:avatar>
                <q-icon name="warning" color="white" />
              </template>
              {{ errorMsg }}
            </q-banner>

            <q-banner class="bg-blue text-white q-mt-lg" v-if="successMsg">
              <template v-slot:avatar>
                <q-icon name="check" color="white" />
              </template>
              {{ successMsg }}
            </q-banner>

          </q-card-section>

          <q-tabs
              v-model="tabs"
              class="text-blue-grey-9 bg-blue-grey-1"
              active-color="primary"
              indicator-color="primary"
              align="left"
              narrow-indicator
            >
            <q-tab name="general" label="General" />
            <q-tab name="localization" label="Localization" />
            <q-tab name="password" label="Change password" />
          </q-tabs>
          <q-separator />

          <q-form ref="frm" @submit.prevent.stop="onSubmit" autocorrect="off" autocapitalize="off" autocomplete="off" spellcheck="false">
            <q-tab-panels v-model="tabs" animated keep-alive>
              <q-tab-panel name="general">

                <q-input
                  class="q-mb-sm"
                  name="name"
                  v-model="form.name.value"
                  :error="form.name.error"
                  :error-message="form.email.errorMsg"
                  :label="$t('name')"
                  @keyup="resetValidation($event)"
                >
                  <template v-slot:prepend>
                    <q-icon name="person" />
                  </template>
                </q-input>

                <q-input
                  class="q-mb-sm"
                  name="email"
                  v-model="form.email.value"
                  :error="form.email.error"
                  :error-message="form.email.errorMsg"
                  type="email"
                  :label="$t('email_address')"
                  @keyup="resetValidation($event)"
                >
                  <template v-slot:prepend>
                    <q-icon name="mdi-at" />
                  </template>
                </q-input>

              </q-tab-panel>
              <q-tab-panel name="localization">

                <q-select
                  v-model="form.locale.value"
                  label="Locale"
                  :options="locales"
                  use-input
                  hide-selected
                  fill-input
                  input-debounce="0"
                  filter="filterLocales"
                >
                  <template v-slot:no-option>
                    <q-item>
                      <q-item-section class="text-grey">
                        No results
                      </q-item-section>
                    </q-item>
                  </template>
                </q-select>
              </q-tab-panel>
            </q-tab-panels>

            <q-card-section>
              <q-input
                filled
                :type="isPwd ? 'password' : 'text'"
                class="q-mb-sm"
                name="password"
                v-model="form.password.value"
                :error="form.password.error"
                :error-message="form.password.errorMsg"
                :label="$t('current_password')"
                @keyup="resetValidation($event)"
              >
                <template v-slot:prepend>
                  <q-icon name="mdi-key-variant" />
                </template>
                <template v-slot:append>
                  <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                  />
                </template>
              </q-input>

            </q-card-section>

            <q-card-actions>
              <q-btn type="submit" :loading="loading" color="green" class="no-border-radius shadow-0" size="lg" style="min-width: 200px">Update</q-btn>
            </q-card-actions>
          </q-form>
        </q-card>
      </div>
    </div>
  </q-page>
</template>
<script>
export default {
  name: 'Page',
  components: {
  },
  data () {
    return {
      tabs: 'general',
      loading: false,
      successMsg: null,
      errorMsg: null,
      isPwd: true,
      locales: [],
      timezones: [],
      currencies: [],
      form: {
        avatar: {
          value: this.$auth.user().avatar,
          error: false,
          errorMsg: null
        },
        name: {
          value: this.$auth.user().name,
          error: false,
          errorMsg: null
        },
        email: {
          value: this.$auth.user().email,
          error: false,
          errorMsg: null
        },
        password: {
          value: null,
          error: false,
          errorMsg: null
        },
        locale: {
          value: {},
          error: false,
          errorMsg: null
        },
        timezone: {
          value: this.$auth.user().timezone,
          error: false,
          errorMsg: null
        },
        currency: {
          value: this.$auth.user().currency,
          error: false,
          errorMsg: null
        }
      }
    }
  },
  mounted () {
    this.$axios
      .get('/localization/locales', { params: { locale: this.$i18n.locale } })
      .then(response => {
        /* this.locales = this.$_.toPairs(response.data) */
        this.locales = response.data
        this.form.locale.value = {
          label: this.locales.$_.map(this.locales.value, this.$auth.user().locale),
          value: this.$auth.user().locale
        }
      })
    this.$axios
      .get('/localization/timezones', { params: { locale: this.$i18n.locale } })
      .then(response => {
        this.timezones = response.data
      })
    this.$axios
      .get('/localization/currencies', { params: { locale: this.$i18n.locale } })
      .then(response => {
        this.currencies = response.data
      })
  },
  watch: {
  },
  methods: {
    onSubmit () {
      this.loading = true
    },
    resetValidation (event) {
      if (typeof event.target.name !== 'undefined') {
        this.form[event.target.name].error = false
      }
    },
    filterLocales (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.locales = this.locales.filter(v => v.toLowerCase().indexOf(needle) > -1)
      })
    }
  },
  computed: {
  }
}
</script>
<style>
</style>
