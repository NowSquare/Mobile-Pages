<template>
  <q-page class="fit row justify-center q-pa-lg bg-blue-8">
    <div class="col-12">
      <div class="fit self-center">
        <q-card flat class="no-border-radius bg-grey-1 text-grey-10 full-width">
          <q-card-section class="bg-blue-grey-1">
            <div class="row items-center no-wrap">
              <div class="col">
                <div class="text-h5">Create a new site</div>
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
          <q-separator />
          <q-form ref="frm1" id="frm1" @submit.prevent.stop="onSubmit" autocorrect="off" autocapitalize="off" autocomplete="off" spellcheck="false" accept-charset="UTF-8" enctype="multipart/form-data">
            <q-card-section>
              <q-input
                class="q-mb-sm"
                name="name"
                v-model="form.name.value"
                :error="form.name.error"
                :error-message="form.name.errorMsg"
                :label="$t('name')"
                autofocus
                @keyup="resetValidation($event)"
              >
              </q-input>
              <q-select
                v-show="false"
                v-model="form.module.value"
                name="module"
                label="Homepage type"
                :options="modules"
                use-input
                hide-selected
                fill-input
                class="q-mb-lg"
              >
              </q-select>
            </q-card-section>
            <q-card-actions class="q-pa-md">
              <q-btn type="submit" :loading="loading" color="green" class="no-border-radius shadow-0" size="lg" style="min-width: 200px">Create site</q-btn>
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
      loading: false,
      successMsg: null,
      errorMsg: null,
      modules: [
        {
          label: 'Content',
          value: 'content'
        }
      ],
      form: {
        name: {
          value: '',
          error: false,
          errorMsg: null
        },
        module: {
          value: {
            label: 'Content',
            value: 'content'
          },
          error: false,
          errorMsg: null
        }
      }
    }
  },
  mounted () {
  },
  methods: {
    onSubmit () {
      this.loading = true
      let formData = new FormData()
      formData.append('locale', this.$i18n.locale)
      formData.append('name', this.form.name.value)
      formData.append('module', this.form.module.value.value)

      this.$axios.post('site/create-site', formData)
        .then(res => {
          if (res.data.status === 'success') {
            if (typeof res.data.msg !== 'undefined') {
              this.$q.notify({
                icon: (res.data.status === 'success') ? 'done' : 'error',
                position: 'bottom-left',
                message: res.data.msg
              })
            }
            this.$router.push({ name: 'site.edit', params: { 'uuid': res.data.uuid } })
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
    }
  },
  computed: {
  }
}
</script>
<style>
</style>
