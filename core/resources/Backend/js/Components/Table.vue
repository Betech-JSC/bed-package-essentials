<template>
    <DataTable
        :value="items"
        :lazy="true"
        :paginator="true"
        :rows="rows"
        v-model:filters="filters"
        ref="data-table"
        dataKey="id"
        :totalRecords="totalItems"
        :loading="loading"
        @page="onPage($event)"
        @sort="onSort($event)"
        @filter="onFilter($event)"
        responsiveLayout="scroll"
        v-model:selection="selectedItems"
        :selectAll="selectAll"
        @select-all-change="onSelectAllChange"
        @row-select="onRowSelect"
        @row-unselect="onRowUnselect"
        stateStorage="session"
        :stateKey="`data-table-state-${currentResource}`"
        :rowHover="true"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[10, 20, 50, 100]"
        currentPageReportTemplate="Từ {first} đến {last} trên tổng {totalRecords}"
        :globalFilterFields="displayColumns.map((x) => x.field)"
    >
        <template #header v-if="!hideHeader">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <Link
                            v-if="canCreate"
                            class="p-button btn-primary"
                            text="Thêm mới"
                            :href="formUrl"
                        />
                        <a
                            v-if="canExport"
                            class="p-button btn-outline-primary"
                            text="Xuất Excel"
                            :href="route(`admin.${currentResource}.export`)"
                        />
                    </div>
                    <span v-if="sortByDate" class="w-1/2">
                        <div class="field-row">
                            <Field
                                v-model="filter_begin_time"
                                @change="onFilter()"
                                :field="{
                                    type: 'datetime-local',
                                    name: 'filter_begin_time',
                                }"
                            />
                            <Field
                                v-model="filter_end_time"
                                @change="onFilter()"
                                :field="{
                                    type: 'datetime-local',
                                    name: 'filter_end_time',
                                }"
                            />
                        </div>
                    </span>
                    <div class="p-input-icon-left w-[20rem]">
                        <heroicons-outline:search
                            class="absolute transform -translate-y-1/2 top-1/2 left-2"
                        />
                        <InputText
                            v-model="filters['global'].value"
                            @input="onChangeSearch()"
                            placeholder="Tìm kiếm.."
                        />
                    </div>
                </div>
            </div>
        </template>
        <template #empty> Không tìm thấy dữ liệu. </template>
        <template #loading>Đang tải dữ liệu...</template>

        <Column
            selectionMode="multiple"
            headerStyle="width: 3rem"
            v-if="showCheckbox"
        ></Column>
        <template
            v-if="displayColumns"
            v-for="(column, index) in displayColumns"
        >
            <Column
                :field="column.field"
                filterMatchMode="contains"
                :sortable="false"
                :header="tt('models.' + currentResource + '.' + column.label)"
            >
                <template #body="{ data }">
                    <Image
                        v-if="isImageCell(data, column)"
                        :src="data[column.field]?.static_url"
                        width="80"
                        preview
                    />
                    <Link
                        v-else
                        :href="
                            route(`admin.${currentResource}.form`, {
                                id: data.id,
                            })
                        "
                    >
                        <span
                            v-if="getStyles(data, column)"
                            class="px-2 py-1 text-xs font-medium uppercase whitespace-pre rounded-sm"
                            :style="getStyles(data, column)"
                        >
                            {{ transformCell(data, column) }}
                        </span>
                        <span v-else v-html="transformCell(data, column)">
                        </span>
                    </Link>
                </template>
                <template
                    #filter="{ filterCallback }"
                    v-if="filters[column.field]"
                >
                    <InputText
                        type="text"
                        v-model="filters[column.fields].value"
                        @keydown.enter="filterCallback()"
                        placeholder="Tìm kiếm"
                    />
                </template>
            </Column>
        </template>
    </DataTable>
</template>

<script>
import { FilterMatchMode, FilterOperator } from "primevue/api";
export default {
    props: {
        schema: {
            type: Object,
        },
        columns: {
            type: Array,
            default: () => [],
        },
        config: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            items: null,
            rows: 20,
            totalItems: 0,
            loading: false,
            lazyParams: {},
            timer: null,

            selectedItems: null,
            selectAll: false,

            mergedColumns: this.mergeColumns(),
            filters: this.getFilters(),
            filter_begin_time: null,
            filter_end_time: null,
        };
    },
    computed: {
        admin() {
            return this.bouncer(this.$page.props.admin);
        },
        currentResource() {
            return this.config.resource ?? this.getResource();
        },
        tableName() {
            return (
                this.config.name ??
                this.tt("models.table_list." + this.currentResource)
            );
        },
        hideHeader() {
            return this.config.hideHeader === true || false;
        },
        showCheckbox() {
            return this.config.showCheckbox ?? false;
        },
        showFilter() {
            return this.config.showFilter ?? false;
        },
        formUrl() {
            return (
                this.config.formUrl ??
                this.route(`admin.${this.currentResource}.form`)
            );
        },
        sortByDate() {
            return this.config.sortByDate ?? false;
        },
        displayColumns() {
            return Object.values(this.mergedColumns)
                .filter((x) => x.display)
                .filter(
                    (x) =>
                        x.type !== "text" &&
                        x.field !== "slug" &&
                        x.field !== "locale" &&
                        !x.field.includes("seo_")
                )
                .sort((a, b) => (a.order > b.order ? 1 : -1))
                .slice(0, 8);
        },
        canCreate() {
            return (
                this.config.canCreate ??
                this.admin.can("create", this.currentResource)
            );
        },
        canExport() {
            return (
                Object.keys(this.route().t.routes).includes(
                    this.currentResource + ".export"
                ) &&
                (this.config.canExport ??
                    this.admin.can("export", this.currentResource))
            );
        },
    },
    watch: {
        selectedItems(value) {
            this.$emit("on-select", value);
        },
    },
    mounted() {
        this.loading = true;

        this.lazyParams = {
            page: 0,
            rows: this.$refs["data-table"].rows,
            sortField: null,
            sortOrder: null,
            filters: this.filters,
        };

        this.loadLazyData();
    },
    methods: {
        loadLazyData() {
            this.loading = true;
            if (this.filter_begin_time) {
                this.lazyParams.filter_begin_time = this.filter_begin_time;
            }
            if (this.filter_end_time) {
                this.lazyParams.filter_end_time = this.filter_end_time;
            }
            this.$axios
                .get(
                    this.route(`admin.${this.currentResource}.index`, {
                        ...this.lazyParams,
                        page: this.lazyParams.page + 1,
                    })
                )
                .then((res) => {
                    const data = res.data;
                    this.loading = false;
                    this.items = data.data;
                    this.rows = data.per_page;
                    this.totalItems = data.total;
                });
        },
        onChangeSearch() {
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }

            this.timer = setTimeout(() => {
                this.loadLazyData();
            }, 200);
        },
        onPage(event) {
            this.lazyParams = event;
            this.loadLazyData();
        },
        onSort(event) {
            this.lazyParams = event;
            this.loadLazyData();
        },
        onFilter() {
            this.lazyParams.filters = this.filters;
            this.loadLazyData();
        },
        onSelectAllChange(event) {
            const selectAll = event.checked;

            if (selectAll) {
                this.$axios
                    .get(
                        this.route(`admin.${this.currentResource}.index`, {
                            ...this.lazyParams,
                            page: this.lazyParams.page + 1,
                        })
                    )
                    .then((res) => {
                        this.selectAll = true;
                        this.selectedItems = res.data.data;
                    });
            } else {
                this.selectAll = false;
                this.selectedItems = [];
            }
        },
        onRowSelect() {
            this.selectAll = this.selectedItems.length === this.totalItems;
        },
        onRowUnselect() {
            this.selectAll = false;
        },
        mergeColumns() {
            let schemaColumns = { ...this.schema.columns };

            const columns = this.columns.length
                ? this.columns
                : this.pluck(Object.values(this.schema.columns), "label");

            columns.forEach(function (column, index) {
                let transformColumn = {};

                if (typeof column === "object") {
                    transformColumn = {
                        display: true,
                        order: index,
                        ...column,
                    };
                } else {
                    transformColumn = {
                        display: true,
                        order: index,
                        field: column,
                    };
                }

                if (schemaColumns[transformColumn.field]) {
                    schemaColumns[transformColumn.field] = {
                        ...schemaColumns[transformColumn.field],
                        ...transformColumn,
                    };
                } else {
                    schemaColumns[transformColumn.field] = {
                        type: "text",
                        default: null,
                        label: transformColumn.field,
                        ...transformColumn,
                    };
                }
            });

            schemaColumns["id"].order = 0;

            return schemaColumns;
        },

        getFilters() {
            let filters = {};
            filters.global = {
                value: null,
                matchMode: FilterMatchMode.CONTAINS,
            };

            const columns = this.mergeColumns;

            Object.keys(columns)
                .filter(
                    (x) => x === "id" || columns[x]?.rules?.includes("required")
                )
                .forEach((column) => {
                    filters[column] = {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    };
                });

            return filters;
        },
        getStyles(data, column) {
            const value = data[column.field];
            if (!column.list) return false;

            return column.list.find((x) => x.label === value || x.id === value)
                .styles;
        },
        transformCell(data, column) {
            let value = data[column.field];

            if (column.list) {
                value = column.list.find(
                    (x) => x.label === value || x.id === value
                ).label;
            }

            if (this.mergedColumns[column.field].transform) {
                value = this.mergedColumns[column.field].transform(data);
            } else if (column.type === "date") {
                value = this.toDate(value, "DD/MM/YYYY");
            } else if (column.type === "datetime") {
                value = this.toDate(value);
            } else if (column.type === "decimal") {
                value = this.toMoney(value);
            } else if (
                (column.type === "bigint" || column.type === "integer") &&
                column.field !== "id"
            ) {
                value = this.toNumber(value);
            } else if (column.type === "boolean") {
                value = `<div class="w-3 h-3 ${
                    value ? "bg-green-500" : "bg-orange-300"
                } rounded-full"></div>`;
            } else if (
                column.type === "json" &&
                value &&
                Object.keys(value).length === 0
            ) {
                return null;
            }

            return value;
        },
        isImageCell(data, column) {
            return column.type === "json" && data[column.field]?.static_url;
        },
        capitalize(string) {
            return string?.charAt(0).toUpperCase() + string.slice(1);
        },
    },
};
</script>

<style lang="scss" scoped>
::v-deep(.p-paginator) {
    .p-paginator-current {
        margin-left: auto;
    }
}

::v-deep(.p-progressbar) {
    height: 0.5rem;
    background-color: #d8dadc;

    .p-progressbar-value {
        background-color: #607d8b;
    }
}

::v-deep(.p-datepicker) {
    min-width: 25rem;

    td {
        font-weight: 400;
    }
}

::v-deep(.p-datatable.p-datatable-customers) {
    .p-datatable-header {
        padding: 1rem;
        text-align: left;
        font-size: 1.5rem;
    }

    .p-paginator {
        padding: 1rem;
    }

    .p-datatable-thead > tr > th {
        text-align: left;
    }

    .p-datatable-tbody > tr > td {
        cursor: auto;
    }

    .p-dropdown-label:not(.p-placeholder) {
        text-transform: uppercase;
    }
}
</style>
