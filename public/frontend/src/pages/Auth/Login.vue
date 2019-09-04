<template>
  <q-page class="fit row justify-center q-pa-lg bg-blue-grey-8">
    <div class="col-12 col-sm-4 col-lg-3 col-xl-2 self-center" style="min-width: 380px">
      <div class="fit self-center shadow-22">
        <q-card flat class="no-border-radius bg-grey-1 full-width">
          <q-card-section>
            <div class="row items-center no-wrap">
              <div class="col">
                <div class="text-h5">Log in</div>
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
                label="E-mail address"
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
                label="Password"
                @keyup="resetValidation($event)"
              >
                <template v-slot:prepend>
                  <q-icon name="mdi-key-variant" />
                </template>

                <template v-slot:append>
                  <q-btn flat no-caps :to="{ name: 'home' }">Forgot password?</q-btn>
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
            </q-card-actions>
          </q-form>
        </q-card>
      </div>
      <q-card-actions class="q-pa-none q-pt-md">
        <q-space />
        <q-btn flat no-caps color="white">Create a new account</q-btn>
      </q-card-actions>
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
  mounted () {
  },
  watch: {
  },
  methods: {
    onSubmit () {
      this.loading = true
      this.$auth.login({
        rememberMe: this.form.remember,
        fetchUser: true,
        params: {
          locale: this.$i18n.locale,
          email: this.form.email.value,
          password: this.form.password.value,
          remember: this.form.remember
        },
        success: function (res) {
          console.log(res.data.token)
          this.$q.notify({
            icon: 'done',
            message: 'Login successful'
          })

          let redirectAfterLogin = 'edit'
          switch (parseInt(this.$auth.user().role)) {
            case 2:
              redirectAfterLogin = 'edit'
              break
          }
          this.$router.push({ name: redirectAfterLogin })
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
  }
}
</script>
<style>
</style>
