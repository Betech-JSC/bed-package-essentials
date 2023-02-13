<template layout>
    <div class="lg:p-6">
        <Form v-model="formData" v-slot="{ form }" :config="{ addGrid: false }">
            <div class="card">
                <div class="card-header">Thông tin chung</div>
                <div class="card-body">
                    <Field
                        v-model="form.name"
                        :field="{
                            type: 'text',
                            name: 'name',
                            label: 'Tên vai trò',
                        }"
                    />
                </div>
            </div>
            <div class="card">
                <table class="table">
                    <thead>
                        <th>Đối tượng</th>
                        <th>
                            <div>Xem</div>
                            <div
                                @click="toggle('index')"
                                class="pt-1 text-xs font-normal capitalize select-none link"
                            >
                                Chọn tất cả
                            </div>
                        </th>
                        <th>
                            <div>Thêm</div>
                            <div
                                @click="toggle('form')"
                                class="pt-1 text-xs font-normal capitalize select-none link"
                            >
                                Chọn tất cả
                            </div>
                        </th>
                        <th>
                            <div>Sửa</div>
                            <div
                                @click="toggle('store')"
                                class="pt-1 text-xs font-normal capitalize select-none link"
                            >
                                Chọn tất cả
                            </div>
                        </th>
                        <th>
                            <div>Xóa</div>
                            <div
                                @click="toggle('destroy')"
                                class="pt-1 text-xs font-normal capitalize select-none link"
                            >
                                Chọn tất cả
                            </div>
                        </th>
                        <th>
                            <div>Khôi phục</div>
                            <div
                                @click="toggle('store')"
                                class="pt-1 text-xs font-normal capitalize select-none link"
                            >
                                Chọn tất cả
                            </div>
                        </th>
                        <th>
                            <div>Khác</div>
                            <div
                                @click="toggle('other')"
                                class="pt-1 text-xs font-normal capitalize select-none link"
                            >
                                Chọn tất cả
                            </div>
                        </th>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(group, index) of Object.keys(
                                form.permissions
                            )"
                        >
                            <td class="font-semibold">
                                {{ tt("models.table_list." + group) }}
                            </td>

                            <td width="100px">
                                <Field
                                    class="flex justify-center"
                                    v-if="
                                        form.permissions[group][
                                            `admin.${group}.index`
                                        ] !== undefined
                                    "
                                    v-model="
                                        form.permissions[group][
                                            `admin.${group}.index`
                                        ]
                                    "
                                    :field="{
                                        type: 'checkbox',
                                        label: false,
                                    }"
                                />
                            </td>

                            <td width="100px">
                                <Field
                                    class="flex justify-center"
                                    v-if="
                                        form.permissions[group][
                                            `admin.${group}.form`
                                        ] !== undefined
                                    "
                                    v-model="
                                        form.permissions[group][
                                            `admin.${group}.form`
                                        ]
                                    "
                                    :field="{
                                        type: 'checkbox',
                                        label: false,
                                    }"
                                />
                            </td>

                            <td width="100px">
                                <Field
                                    class="flex justify-center"
                                    v-if="
                                        form.permissions[group][
                                            `admin.${group}.store`
                                        ] !== undefined
                                    "
                                    v-model="
                                        form.permissions[group][
                                            `admin.${group}.store`
                                        ]
                                    "
                                    :field="{
                                        type: 'checkbox',
                                        label: false,
                                    }"
                                />
                            </td>

                            <td width="100px">
                                <Field
                                    class="flex justify-center"
                                    v-if="
                                        form.permissions[group][
                                            `admin.${group}.destroy`
                                        ] !== undefined
                                    "
                                    v-model="
                                        form.permissions[group][
                                            `admin.${group}.destroy`
                                        ]
                                    "
                                    :field="{
                                        type: 'checkbox',
                                        label: false,
                                    }"
                                />
                            </td>

                            <td width="100px">
                                <Field
                                    class="flex justify-center"
                                    v-if="
                                        form.permissions[group][
                                            `admin.${group}.restore`
                                        ] !== undefined
                                    "
                                    v-model="
                                        form.permissions[group][
                                            `admin.${group}.restore`
                                        ]
                                    "
                                    :field="{
                                        type: 'checkbox',
                                        label: false,
                                    }"
                                />
                            </td>
                            <td>
                                <Field
                                    class="flex justify-center"
                                    v-for="(permission, index) in Object.keys(
                                        form.permissions[group]
                                    ).filter(
                                        (x) =>
                                            !x.includes('.index') &&
                                            !x.includes('.form') &&
                                            !x.includes('.store') &&
                                            !x.includes('.destroy') &&
                                            !x.includes('.restore')
                                    )"
                                    v-model="
                                        form.permissions[group][permission]
                                    "
                                    :field="{
                                        type: 'checkbox',
                                        name: permission,
                                    }"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Form>
    </div>
</template>
<script>
export default {
    props: ["item", "schema"],
    data() {
        return {
            formData: this.item,
        };
    },
    methods: {
        toggle(action) {
            if (action === "other") {
                Object.keys(this.formData.permissions).forEach((x) =>
                    Object.keys(this.formData.permissions[x]).forEach((y) => {
                        if (
                            !y.includes(".index") &&
                            !y.includes(".form") &&
                            !y.includes(".store") &&
                            !y.includes(".destroy") &&
                            !y.includes(".restore")
                        ) {
                            this.formData.permissions[x][y] = true;
                        }
                    })
                );
            } else {
                Object.keys(this.formData.permissions).forEach((x) =>
                    Object.keys(this.formData.permissions[x]).forEach((y) => {
                        if (y.includes("." + action)) {
                            this.formData.permissions[x][y] = true;
                        }
                    })
                );
            }
        },
    },
};
</script>
