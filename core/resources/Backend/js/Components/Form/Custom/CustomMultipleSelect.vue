<template>
    <MultiSelect
        :model-value="selectedOptions"
        @change="selectChange($event.value)"
        :options="options"
        :loading="field.loading"
        :placeholder="placeholder"
        :optionLabel="labelBy"
        :autoFilterFocus="true"
        display="chip"
        :filter="field.filter || true"
        :filterFields="['filter', labelBy, keyBy]"
    />
    <!-- @update="asyncSearch" -->
</template>

<script>
export default {
    props: ['field', 'modelValue'],
    emits: ['change'],

    watch: {
        'field.options': {
            handler() {
                this.fetchSource()
            },
        },
        'field.filter': {
            handler() {
                console.log('sdvsdv')
            },
        },
        modelValue: {
            handler() {
                console.log(this.modelValue)
                if (this.lazySearch && this.modelValue) {
                    this.fetchSource({ params: { keyword: this.modelValue } })
                }
            },
        },
    },

    data() {
        return {
            loading: false,
            options: [],
            timer: null,
        }
    },

    created() {
        if (!this.lazySearch) {
            this.fetchSource()
        } else if (this.lazySearch && this.modelValue) {
            this.fetchSource({ params: { keyword: this.modelValue } })
        }
    },

    computed: {
        keyBy() {
            return this.field.keyBy || 'id'
        },
        labelBy() {
            return this.field.labelBy || 'label'
        },
        placeholder() {
            return this.field.placeholder || `${this.tt('models.field.choose')} ${this.field.label}`
        },
        lazySearch() {
            return this.field.source !== undefined && this.field.source.method === 'lazySearch'
        },
        options() {
            let options = []
            this.field.options?.forEach((option) => {
                options.push({
                    [this.keyBy]: option[this.keyBy].toString(),
                    [this.labelBy]: option[this.labelBy].toString(),
                    filter: this.slugify(option[this.labelBy]),
                })
            })
            return options
        },
        selectedOptions() {
            if (!this.modelValue) return []

            const selectedIds = this.modelValue.map((option) => {
                if (typeof option === 'object') {
                    return option[this.keyBy].toString()
                } else {
                    return option
                }
            })

            const sortedOptions = this.options
                .filter((x) => selectedIds.includes(x[this.keyBy]))
                .sort((a, b) => {
                    const indexA = selectedIds.indexOf(a[this.keyBy].toString())
                    const indexB = selectedIds.indexOf(b[this.keyBy].toString())
                    return indexA - indexB
                })

            return sortedOptions
        },
    },

    methods: {
        asyncSearch(keyword) {
            console.log(keyword)
            if (keyword.length === 0) return

            if (this.timer) {
                clearTimeout(this.timer)
                this.timer = null
            }

            this.timer = setTimeout(() => {
                this.loading = true
                this.fetchSource({ params: { keyword } })
            }, 150)
        },
        async fetchSource(appendParams = {}) {
            let options = []
            if (this.field.options !== undefined) {
                options = this.field.options
            } else {
                this.loading = true
                await this.$axios
                    .post(route('admin.helper.model-data'), { ...this.field.source, ...appendParams })
                    .then((res) => {
                        options = res.data

                        if (this.sourceIsConst) {
                            options = Object.entries(options).map((x) => {
                                return { [this.keyBy]: x[0], [this.labelBy]: x[1] }
                            })
                        }

                        this.loading = false
                    })
            }

            if (this.field.emptyLabel !== undefined && options.length > 0) {
                let emptyOption = { ...options[0] }
                Object.keys(emptyOption).forEach((key) => {
                    emptyOption[key] = key === this.labelBy ? this.field.emptyLabel : 0
                })
                options.unshift(emptyOption)
            }

            this.options = options
        },
        selectChange(value) {
            console.log(value)
            this.$emit('change', value)
        },
        slugify(str) {
            return str
                .toLowerCase()
                .replace(/\t/g, '')
                .replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a')
                .replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e')
                .replace(/ì|í|ị|ỉ|ĩ/g, 'i')
                .replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o')
                .replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u')
                .replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y')
                .replace(/đ/g, 'd')
                .replace(/[^A-Za-z0-9_-]/g, ' ')
                .replace(/\s+/g, ' ')
        },
    },
}
</script>
