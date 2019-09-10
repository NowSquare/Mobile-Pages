<template>
  <div>
    <q-input
      :label="label"
      v-model="filename"
      :ref="name + 'FileName'"
      readonly
      class="q-mb-lg ellipsis"
    >
      <template v-slot:append>
        <q-btn round color="white" text-color="grey-9" flat size="sm" icon="mdi-image-plus" @click="uploadFile">
          <q-tooltip>
            Upload image
          </q-tooltip>
        </q-btn>
        <q-btn round :disabled="filename === null || filename === ''" color="white" text-color="grey-9" flat size="sm" icon="delete" @click="removeUpload">
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
      pauseVModelWatch: false,
      originalFilename: null,
      filename: null,
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
    this.filename = (this.vModel !== null && this.vModel !== '' && !this.vModel.startsWith('data:')) ? this.vModel.split('/').reverse()[0] : this.defaultValue
    this.originalFilename = JSON.parse(JSON.stringify(this.filename))
  },
  watch: {
    uploadImgSrc: function (newVal, oldVal) {
      this.imgSrc = newVal
    },
    vModel: function (newVal, oldVal) {
      if (!this.pauseVModelWatch) {
        this.filename = this.originalFilename
      }
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
      this.pauseVModelWatch = true
      this.$refs.uploader.reset()
      this.$refs.uploader.removeQueuedFiles()
      this.filename = null
      this.imgSrc = (this.defaultToOriginal) ? this.uploadImgSrcOld : ''
      this.$nextTick(() => {
        this.pauseVModelWatch = false
      })
    },
    uploadAdded (files) {
      this.pauseVModelWatch = true
      this.imgSrc = files[0].__img.src
      this.filename = files[0].name
      this.$emit('filename', this.filename)
      this.$nextTick(() => {
        this.pauseVModelWatch = false
      })
    },
    uploadRemoved (files) {
      this.imgSrc = this.uploadImgSrcOld
      this.filename = null
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
  .ellipsis input {
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
  }
</style>
