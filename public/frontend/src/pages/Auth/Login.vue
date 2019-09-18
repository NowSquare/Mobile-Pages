<template>
  <q-page class="fit row justify-center q-pa-lg bg-light-blue-8">
    <div class="col-12 col-sm-4 col-lg-3 col-xl-2 self-center" style="min-width: 380px">
      <div class="fit self-center shadow-22">
        <q-card flat class="no-border-radius bg-grey-1 text-grey-10 full-width">
          <q-card-section>
            <div class="row items-center no-wrap">
              <div class="col">
                <div class="text-h5">{{ $t('log_in') }}</div>
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

          <q-form ref="frm" @submit.prevent.stop="onSubmit" autocorrect="off" autocapitalize="off" autocomplete="off" spellcheck="false">

            <q-card-section>
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

              <q-input
                class="q-mb-sm"
                name="password"
                v-model="form.password.value"
                :error="form.password.error"
                :error-message="form.password.errorMsg"
                type="password"
                :label="$t('password')"
                @keyup="resetValidation($event)"
              >
                <template v-slot:prepend>
                  <q-icon name="mdi-key-variant" />
                </template>

                <template v-slot:append>
                  <q-btn flat no-caps :to="{ name: 'password.email' }">{{ $t('forgot_password') }}</q-btn>
                </template>
              </q-input>

              <q-checkbox
                v-model="form.remember"
                label="Keep me logged in"
                color="blue-grey-8"
              />

            </q-card-section>

            <q-card-actions>
              <q-btn type="submit" :loading="loading" color="green" class="full-width no-border-radius shadow-0" size="lg">Login</q-btn>
              <q-btn color="primary" label="Click here for demo login" v-if="config.demo" no-caps size="18px" @click="form.email.value='user@example.com';form.password.value='welcome123';onSubmit()" class="q-mt-sm full-width no-border-radius shadow-0"/>
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
      successResetUpdateRedirect: false,
      loading: false,
      successMsg: null, /* You can now log in. */
      errorMsg: null,
      form: {
        email: {
          value: null,
          error: false,
          errorMsg: null
        },
        password: {
          value: null,
          error: false,
          errorMsg: null
        },
        remember: true
      }
    }
  },
  created () {
    this.successResetUpdateRedirect = this.$route.params.successResetUpdateRedirect || false

    if (this.successResetUpdateRedirect) {
      this.successMsg = 'Your password has been successfully reset. You can now log in with your new password.'
    }
  },
  methods: {
    onSubmit () {
      this.loading = true
      let redirect = this.$auth.redirect()

      this.$auth.login({
        redirect: { name: redirect ? redirect.from.name : 'sites.overview', query: redirect ? redirect.from.query : null },
        rememberMe: this.form.remember,
        fetchUser: true,
        params: {
          locale: this.$i18n.locale,
          email: this.form.email.value,
          password: this.form.password.value,
          remember: this.form.remember
        },
        success: function (res) {
          this.$q.notify({
            icon: 'done',
            message: 'Login successful'
          })
        },
        error: function (err) {
          let res = err.response.data
          if (typeof res.error !== 'undefined') {
            this.errorMsg = res.error
          }
          if (typeof res.errors !== 'undefined') {
            for (let field in res.errors) {
              this.form[field].error = true
              this.form[field].errorMsg = res.errors[field][0]
            }
          }
          this.loading = false
        }
      })
    },
    resetValidation (event) {
      if (typeof event.target.name !== 'undefined') {
        this.form[event.target.name].error = false
      }
    }
  },
  computed: {
    config () {
      return window.config
    }
  }
}
</script>
<style>
</style>
