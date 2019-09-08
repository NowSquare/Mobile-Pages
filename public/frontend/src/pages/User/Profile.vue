<template>
  <q-page class="fit row justify-center q-pa-lg bg-blue-8">
    <div class="col-12">
      <div class="fit self-center">
        <q-card flat class="no-border-radius bg-grey-1 text-grey-10 full-width">
          <q-card-section class="bg-blue-grey-1">
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

          <q-form ref="frm1" id="frm1" @submit.prevent.stop="onSubmit" autocorrect="off" autocapitalize="off" autocomplete="off" spellcheck="false" accept-charset="UTF-8" enctype="multipart/form-data">
            <q-tab-panels v-model="tabs" animated keep-alive>
              <q-tab-panel name="general">

                <q-avatar
                  size="128px"
                  class="q-mb-xs"
                >
                  <q-img
                    :src="form.avatar.value"
                    spinner-color="black"
                  />
                </q-avatar>

                <ImageUpload
                  label="Avatar"
                  v-model="form.avatar.value"
                  name="avatar_upload"
                  :default-value="form.avatar_name"
                  :default-to-original="(form.avatar_name === '') ? true : false"
                />

                <q-input
                  class="q-mb-sm"
                  name="name"
                  v-model="form.name.value"
                  :error="form.name.error"
                  :error-message="form.email.errorMsg"
                  :label="$t('name')"
                  @keyup="resetValidation($event)"
                >
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
                </q-input>

              </q-tab-panel>
              <q-tab-panel name="localization">

                <q-select
                  autocomplete
                  v-model="form.locale.value"
                  name="locale"
                  label="Locale"
                  :options="locales"
                  use-input
                  hide-selected
                  fill-input
                  input-debounce="0"
                  @filter="filterLocales"
                  class="q-mb-lg"
                >
                  <template v-slot:no-option>
                    <q-item>
                      <q-item-section class="text-grey">
                        No results
                      </q-item-section>
                    </q-item>
                  </template>
                </q-select>

                <q-select
                  autocomplete
                  v-model="form.timezone.value"
                  name="timezone"
                  label="Timezone"
                  :options="timezones"
                  use-input
                  hide-selected
                  fill-input
                  input-debounce="0"
                  @filter="filterTimezones"
                  class="q-mb-lg"
                >
                  <template v-slot:no-option>
                    <q-item>
                      <q-item-section class="text-grey">
                        No results
                      </q-item-section>
                    </q-item>
                  </template>
                </q-select>

                <q-select
                  autocomplete
                  v-model="form.currency.value"
                  name="currency"
                  label="Currency"
                  :options="currencies"
                  use-input
                  hide-selected
                  fill-input
                  input-debounce="0"
                  @filter="filterCurrencies"
                  class="q-mb-lg"
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
              <q-tab-panel name="password">
                <q-input
                  :type="isPwd ? 'password' : 'text'"
                  class="q-mb-sm"
                  name="new_password"
                  v-model="form.new_password.value"
                  :error="form.new_password.error"
                  :error-message="form.new_password.errorMsg"
                  :label="$t('change_password')"
                  :hint="$t('change_password_hint')"
                  @keyup="resetValidation($event)"
                >
                  <template v-slot:append>
                    <q-icon
                      :name="isPwd ? 'visibility_off' : 'visibility'"
                      class="cursor-pointer"
                      @click="isPwd = !isPwd"
                    />
                  </template>
                </q-input>
              </q-tab-panel>
            </q-tab-panels>

            <q-card-section>
              <q-input
                filled
                :type="isPwd ? 'password' : 'text'"
                class="q-mb-sm"
                name="current_password"
                v-model="form.current_password.value"
                :error="form.current_password.error"
                :error-message="form.current_password.errorMsg"
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

            <q-card-actions class="q-pa-md">
              <q-btn type="submit" :loading="loading" color="green" class="no-border-radius shadow-0" size="lg" style="min-width: 200px">Update</q-btn>
            </q-card-actions>
          </q-form>
        </q-card>
      </div>
    </div>
  </q-page>
</template>
<script>
import ImageUpload from 'components/ImageUpload'

export default {
  name: 'Page',
  components: {
    ImageUpload
  },
  data () {
    return {
      tabs: 'general',
      loading: false,
      successMsg: null,
      errorMsg: null,
      isPwd: true,
      locales: [],
      localesFilter: [],
      timezones: [],
      timezonesFilter: [],
      currencies: [],
      currenciesFilter: [],
      form: {
        avatar_name: this.$auth.user().avatar_name,
        avatar_media_changed: false,
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
        locale: {
          value: {},
          error: false,
          errorMsg: null
        },
        timezone: {
          value: {},
          error: false,
          errorMsg: null
        },
        currency: {
          value: {},
          error: false,
          errorMsg: null
        },
        new_password: {
          value: null,
          error: false,
          errorMsg: null
        },
        current_password: {
          value: null,
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
        this.localesFilter = response.data
        this.locales = response.data
        this.form.locale.value = {
          label: this.$_.find(this.locales, ['value', this.$auth.user().locale])['label'],
          value: this.$auth.user().locale
        }
      })
    this.$axios
      .get('/localization/timezones', { params: { locale: this.$i18n.locale } })
      .then(response => {
        this.timezonesFilter = response.data
        this.timezones = response.data
        this.form.timezone.value = {
          label: this.$_.find(this.timezones, ['value', this.$auth.user().timezone])['label'],
          value: this.$auth.user().timezone
        }
      })
    this.$axios
      .get('/localization/currencies', { params: { locale: this.$i18n.locale } })
      .then(response => {
        this.currenciesFilter = response.data
        this.currencies = response.data
        this.form.currency.value = {
          label: this.$_.find(this.currencies, ['value', this.$auth.user().currency])['label'],
          value: this.$auth.user().currency
        }
      })
  },
  watch: {
    'form.avatar.value': function (val) {
      this.form.avatar_media_changed = true
    }
  },
  methods: {
    onSubmit () {
      this.loading = true
      let formData = new FormData()
      formData.append('locale', this.$i18n.locale)
      formData.append('avatar_media_changed', this.form.avatar_media_changed)
      formData.append('avatar', this.form.avatar.value)
      formData.append('name', this.form.name.value)
      formData.append('email', this.form.email.value)
      formData.append('locale', this.form.locale.value.value)
      formData.append('timezone', this.form.timezone.value.value)
      formData.append('currency', this.form.currency.value.value)
      formData.append('new_password', this.form.new_password.value)
      formData.append('current_password', this.form.current_password.value)

      this.$axios.post('auth/profile', formData, { headers: { 'content-type': 'multipart/form-data' } })
        .then(res => {
          if (res.data.status === 'success') {
            this.form.success = true
            this.form.new_password.value = null
            this.form.current_password.value = null
            this.$auth.user(res.data.user)
            this.form.avatar.value = this.$auth.user().avatar

            if (typeof res.data.msg !== 'undefined') {
              this.$q.notify({
                icon: (res.data.status === 'success') ? 'done' : 'error',
                position: 'bottom-left',
                message: res.data.msg
              })
            }
          }
        })
        .catch(err => {
          let res = err.response.data
          if (typeof res.msg !== 'undefined') {
            this.errorMsg = res.msg
          }
          if (typeof res.errors !== 'undefined') {
            /* Get first error and select tab where error occurs */
            let field = Object.keys(res.errors)[0]
            let el = (typeof this.$refs[field] !== 'undefined') ? this.$refs[field] : null
            let tab = (el !== null) ? el.$parent.name : null
            this.pageTab = tab

            for (let field in res.errors) {
              this.form[field].error = true
              this.form[field].errorMsg = res.errors[field][0]
            }
          }
        })
        .finally(() => {
          this.loading = false
        })
    },
    resetValidation (event) {
      if (typeof event.target.name !== 'undefined') {
        this.form[event.target.name].error = false
      }
    },
    filterLocales (val, update, abort) {
      update(() => {
        this.locales = this.localesFilter.filter((element, index, array) => element.label.toLowerCase().indexOf(val) > -1)
      })
    },
    filterTimezones (val, update, abort) {
      update(() => {
        this.timezones = this.timezonesFilter.filter((element, index, array) => element.label.toLowerCase().indexOf(val) > -1)
      })
    },
    filterCurrencies (val, update, abort) {
      update(() => {
        this.currencies = this.currenciesFilter.filter((element, index, array) => element.label.toLowerCase().indexOf(val) > -1)
      })
    }
  },
  computed: {
  }
}
</script>
<style>
</style>
