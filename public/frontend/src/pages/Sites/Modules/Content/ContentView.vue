<template>
  <div>
  Here comes the content input
  </div>
</template>
<script>
export default {
  name: 'content',
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
