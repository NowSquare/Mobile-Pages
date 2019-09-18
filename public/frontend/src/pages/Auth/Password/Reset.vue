<template>
  <q-page class="fit row justify-center q-pa-lg bg-light-blue-8">
    <div class="col-12 col-sm-4 col-lg-3 col-xl-2 self-center" style="min-width: 380px">
      <div class="fit self-center shadow-22">
        <q-card flat class="no-border-radius bg-grey-1 text-grey-10 full-width">
          <q-card-section>
            <div class="row items-center no-wrap">
              <div class="col">
                <div class="text-h5">Reset password</div>
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

            <q-card-section v-if="!invalidToken">

              <p>Enter a new password for your account.</p>

              <q-input
                class="q-mb-sm"
                name="password"
                v-model="form.password.value"
                :error="form.password.error"
                :error-message="form.password.errorMsg"
                :type="isPwd ? 'password' : 'text'"
                label="Password"
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

            <q-card-actions v-if="!invalidToken">
              <q-btn type="submit" :loading="loading" color="green" class="full-width no-border-radius shadow-0" size="lg">Update password</q-btn>
            </q-card-actions>
          </q-form>
        </q-card>
      </div>
      <q-card-actions class="q-pa-none q-pt-md">
        <q-space />
        <q-btn flat no-caps color="white" :to="{ name: 'login' }">Back to login</q-btn>
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
      invalidToken: false,
      successMsg: null,
      errorMsg: null,
      isPwd: true,
      form: {
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
    let token = this.$route.params.token
    this.$axios
      .post('/auth/password/reset/validate-token', {
        locale: this.$i18n.locale,
        token: token
      })
      .then(res => {
        if (res.data.status === 'success') {
          this.invalidToken = false
        } else {
          this.invalidToken = true
          this.errorMsg = 'The token is invalid, already used or expired.'
        }
      })
      .catch(err => {
        if (typeof err.response.data !== 'undefined') {
          this.invalidToken = true
          this.errorMsg = 'The token is invalid, already used or expired.'
        }
      })
  },
  methods: {
    onSubmit () {
      this.loading = true

      this.$axios.post('/auth/password/update', {
        locale: this.$i18n.locale,
        password: this.form.password.value,
        token: this.$route.params.token
      })
        .then(response => {
          if (response.data.status === 'success') {
            this.$router.push({ name: 'login', params: { successResetUpdateRedirect: true } })
          }
        })
        .catch(err => {
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
        })
        .finally(() => { this.loading = false })
    },
    resetValidation (event) {
      if (typeof event.target.name !== 'undefined') {
        this.form[event.target.name].error = false
      }
    }
  }
}
</script>
<style>
</style>
