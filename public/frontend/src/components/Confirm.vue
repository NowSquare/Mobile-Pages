<template>
  <q-dialog v-model="dialog" persistent @keydown.esc="cancel" v-bind:style="{ zIndex: options.zIndex }">
    <q-card>
      <q-card-section class="row">
        <div class="col-2 text-center">
          <q-avatar :icon="options.icon" color="grey-9" text-color="white" />
        </div>
        <div class="col-10">
          <div class="q-ml-md text-body1">{{ message }}</div>
        </div>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="Cancel" color="grey-9" v-close-popup @click.native="cancel" />
        <q-btn flat label="Yes" color="red" v-close-popup @click.native="agree" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
<script>
/*
 * Confirm Dialog component
 *
 * Insert component where you want to use it:
 * <confirm ref="confirm"></confirm>
 *
 * Call it:
 * this.$refs.confirm.open('Are you sure?', { color: 'red' }).then((confirm) => {})
 * Or use await:
 * if (await this.$refs.confirm.open('Delete', 'Are you sure?', { color: 'red' })) {
 *   // yes
 * }
 * else {
 *   // cancel
 * }
 *
 * Alternatively you can place it in main App component and access it globally via this.$root.$confirm
 * <template>
 *   <v-app>
 *     ...
 *     <confirm ref="confirm"></confirm>
 *   </v-app>
 * </template>
 *
 * mounted() {
 *   this.$root.$confirm = this.$refs.confirm.open
 * }
 */
export default {
  data: () => ({
    dialog: false,
    resolve: null,
    reject: null,
    message: null,
    options: {
      color: 'primary',
      icon: 'warning',
      width: 290,
      zIndex: 200
    }
  }),
  methods: {
    open (message, options) {
      this.dialog = true
      this.message = message
      this.options = Object.assign(this.options, options)
      return new Promise((resolve, reject) => {
        this.resolve = resolve
        this.reject = reject
      })
    },
    agree () {
      this.resolve(true)
      this.dialog = false
    },
    cancel () {
      this.resolve(false)
      this.dialog = false
    }
  }
}
</script>
