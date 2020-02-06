<template>
  <q-field :label="label" stack-label borderless>
    <template v-slot:control>
      <q-editor
        v-model="editorContent"
        :name="name"
        min-height="5rem"
        class="full-width q-mt-sm"
        :toolbar="[
          [
            {
              label: $q.lang.editor.align,
              icon: $q.iconSet.editor.align,
              fixedLabel: true,
              list: 'only-icons',
              options: ['left', 'center', 'right', 'justify']
            }
          ],
          ['bold', 'link'],
          [
            {
              label: $q.lang.editor.fontSize,
              icon: $q.iconSet.editor.fontSize,
              fixedLabel: true,
              fixedIcon: true,
              list: 'no-icons',
              options: [
                'size-1',
                'size-2',
                'size-3',
                'size-4',
                'size-5',
                'size-6',
                'size-7'
              ]
            },
            {
              label: $q.lang.editor.defaultFont,
              icon: $q.iconSet.editor.font,
              fixedIcon: true,
              list: 'no-icons',
              options: [
                'default_font',
                'arial',
                'arial_black',
                'comic_sans',
                'courier_new',
                'impact',
                'lucida_grande',
                'times_new_roman',
                'verdana'
              ]
            },
            'removeFormat'
          ],
          ['unordered', 'ordered'],

          ['viewsource']
        ]"
        :fonts="{
          arial: 'Arial',
          arial_black: 'Arial Black',
          comic_sans: 'Comic Sans MS',
          courier_new: 'Courier New',
          impact: 'Impact',
          lucida_grande: 'Lucida Grande',
          times_new_roman: 'Times New Roman',
          verdana: 'Verdana'
        }"
      />
    </template>
  </q-field>
</template>
<script>
export default {
  name: 'editor',
  model: {
    prop: 'v-model',
    event: 'input'
  },
  data () {
    return {
      editorText: null
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
      default: null,
      required: false,
      type: String
    }
  },
  computed: {
    editorContent: {
      get () {
        return this.vModel
      },
      set (val) {
        this.editorText = val
        this.$emit('input', val)
      }
    }
  }
}
</script>
<style>
</style>
