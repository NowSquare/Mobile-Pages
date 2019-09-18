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

            <q-card-section>
              A link to reset your password will be sent to your e-mail address.
            </q-card-section>

            <q-card-section>
              <q-input
                class="q-mb-sm"
                name="email"
                v-model="form.email.value"
                :error="form.email.error"
                :error-message="form.email.errorMsg"
                type="email"
                label="Enter e-mail address"
                @keyup="resetValidation($event)"
              >
                <template v-slot:prepend>
                  <q-icon name="mdi-at" />
                </template>
              </q-input>

            </q-card-section>

            <q-card-actions>
              <q-btn type="submit" :loading="loading" color="green" class="full-width no-border-radius shadow-0" size="lg">Send reset link</q-btn>
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
      successMsg: null,
      errorMsg: null,
      form: {
        email: {
          value: null,
          error: false,
          errorMsg: null
        }
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

      this.$axios
        .post('/auth/password/reset', {
          locale: this.$i18n.locale,
          email: this.form.email.value
        })
        .then(response => {
          if (response.data.status === 'success') {
            this.form.email.value = ''
            this.successMsg = 'An email has been sent to reset your password. Check your spam folder.'
          } else if (typeof response.data.error !== 'undefined') {
            this.errorMsg = response.data.error
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
          this.loading = false
        })
        .finally(() => {
          this.loading = false
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
