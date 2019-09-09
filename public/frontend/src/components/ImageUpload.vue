<template>
  <div>
    <q-input
      :label="label"
      v-model="imgName"
      :ref="name + 'FileName'"
      readonly
      class="q-mb-lg"
    >
      <template v-slot:append>
        <q-btn round color="white" text-color="grey-9" flat size="sm" icon="mdi-image-plus" @click="uploadFile">
          <q-tooltip>
            Upload image
          </q-tooltip>
        </q-btn>
        <q-btn round :disabled="imgName === null || imgName === ''" color="white" text-color="grey-9" flat size="sm" icon="delete" @click="removeUpload">
          <q-tooltip>
            Delete image
          </q-tooltip>
        </q-btn>
      </template>
    </q-input>

    <div class="row" v-show="showUploader">
      <div class="col">
        <q-uploader
          @added="uploadAdded"
          @removed="uploadRemoved"
          :name="name"
          :id="name"
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
      imgName: null,
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
    },
    name: {
      default: 'file',
      required: false,
      type: String
    },
    defaultValue: {
      default: '',
      required: false,
      type: String
    },
    defaultToOriginal: {
      default: false,
      required: false,
      type: Boolean
    }
  },
  created () {
    this.uploadImgSrcOld = this.imgSrc
    this.imgName = (this.vModel !== null && this.vModel !== '' && !this.vModel.startsWith('data')) ? this.vModel.split('/').reverse()[0] : this.defaultValue
  },
  watch: {
    uploadImgSrc: function (newVal, oldVal) {
      this.imgSrc = newVal
    }
  },
  methods: {
    uploadFile () {
      this.$refs.uploader.removeQueuedFiles()
      this.$nextTick(() => {
        this.$refs.uploader.pickFiles()
      })
    },
    removeUpload () {
      this.$refs.uploader.reset()
      this.$refs.uploader.removeQueuedFiles()
      this.imgName = null
      this.imgSrc = (this.defaultToOriginal) ? this.uploadImgSrcOld : ''
    },
    uploadAdded (files) {
      this.imgSrc = files[0].__img.src
      this.imgName = files[0].name
      this.$emit('filename', this.imgName)
    },
    uploadRemoved (files) {
      this.imgSrc = this.uploadImgSrcOld
      this.imgName = null
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
