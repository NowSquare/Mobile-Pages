<template>
  <div class="row q-gutter-lg">
    <div class="col">
      <q-field :label="label" stack-label borderless class="q-mt-xs">
        <template v-slot:control>
          <q-uploader
            @added="uploadAdded"
            @removed="uploadRemoved"
            ref="uploader"
            label="Upload new image"
            accept=".jpg, image/*"
            style="width: 100%"
            color="grey-8"
            class="q-mt-sm"
            flat
            bordered
            hide-upload-btn
          />
        </template>
      </q-field>
    </div>
    <div class="col" v-show="false">
      <q-field label="Image" stack-label borderless class="q-mt-xs">
        <template v-slot:control>
          <q-btn color="white" text-color="black" class="full-width q-mt-sm" size="sm" label="Style" />
          <q-btn color="red" class="full-width q-mt-sm" size="sm" label="Delete" />
        </template>
      </q-field>
    </div>
  </div>
</template>
<script>
export default {
  name: 'image-upload',
  model: {
    prop: 'v-model',
    event: 'input'
  },
  data () {
    return {
      uploadImgSrc: null,
      uploadImgSrcOld: null
    }
  },
  props: {
    label: {
      default: null,
      required: false,
      type: String
    },
    vModel: {
      default: null,
      required: false,
      type: String
    }
  },
  created () {
    this.uploadImgSrcOld = this.imgSrc
  },
  watch: {
    uploadImgSrc: function (newVal, oldVal) {
      this.imgSrc = newVal
    }
  },
  methods: {
    uploadAdded (files) {
      this.imgSrc = files[0].__img.src
    },
    uploadRemoved (files) {
      this.imgSrc = this.uploadImgSrcOld
    }
  },
  computed: {
    imgSrc: {
      get () {
        return this.vModel
      },
      set (val) {
        this.uploadImgSrc = val
        this.$emit('input', val)
      }
    }
  }
}
</script>
<style>
</style>
