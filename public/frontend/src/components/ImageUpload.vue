<template>
  <div>
    <q-field :label="label" stack-label borderless class="q-mt-xs">
      <template v-slot:control>
        <div class="row full-width">
          <div class="col q-mr-sm">
            <q-btn color="white" text-color="black" class="full-width" size="md" icon="add_photo_alternate" label="Upload image" @click="showUploader =! showUploader" no-caps />
          </div>
          <div class="col q-ml-sm">
            <q-btn v-if="imgSrc !== ''" color="red" class="full-width" size="md" @click="$refs.uploader.reset(); imgSrc = ''" label="Delete image" no-caps />
          </div>
        </div>
      </template>
    </q-field>
    <div class="row" v-show="showUploader">
      <div class="col">
        <q-uploader
          @added="uploadAdded"
          @removed="uploadRemoved"
          ref="uploader"
          label="Upload new image"
          accept=".jpg, image/*"
          style="width: 100%"
          color="grey-8"
          class="q-mb-md"
          flat
          bordered
          hide-upload-btn
        />
      </div>
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
      showUploader: false,
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
