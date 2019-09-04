<template>
  <q-input
      :label="label"
      v-model="inputColor"
      :rules="['anyColor']"
    >
    <template slot="prepend">
      <div style="width: 32px; height: 32px; display: inline-block; border: 1px solid #c2c2c2; border-radius: 2px;" :style="{'background-color': inputColor}"></div>
    </template>
    <template v-slot:append>
      <q-icon name="colorize" class="cursor-pointer">
        <q-popup-proxy transition-show="scale" transition-hide="scale">
          <q-color v-model="selectedColor" />
        </q-popup-proxy>
      </q-icon>
    </template>
  </q-input>
</template>
<script>
export default {
  name: 'color-picker',
  model: {
    prop: 'color',
    event: 'input'
  },
  data () {
    return {
      inputColor: null
    }
  },
  props: {
    label: {
      default: '',
      required: false,
      type: String
    },
    color: {
      default: null,
      required: false,
      type: String
    }
  },
  watch: {
    inputColor: function (newVal, oldVal) {
      this.selectedColor = newVal
    }
  },
  mounted () {
    this.inputColor = this.color
  },
  methods: {
  },
  computed: {
    selectedColor: {
      get () {
        return this.color
      },
      set (val) {
        this.inputColor = val
        this.$emit('input', val)
      }
    }
  }
}
</script>
<style>
</style>
