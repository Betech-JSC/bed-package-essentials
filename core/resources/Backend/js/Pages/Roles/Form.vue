<template layout>
    <Form v-model="formData" v-slot="{ form }">
        <div class="card">
            <div class="card-header">Thông tin chung</div>
            <div class="card-body">
                <Field
                    disabled
                    v-model="form.name"
                    :field="{
                        type: 'text',
                        name: 'name',
                        label: 'Loại tài khoản',
                    }"
                />
                <Field
                    v-model="form.title"
                    :field="{
                        type: 'text',
                        name: 'title',
                        label: 'Tên vai trò',
                    }"
                />
            </div>
        </div>
        <div class="card">
            <table class="table">
                <thead>
                    <th>Đối tượng</th>
                    <th>Xem</th>
                    <th>Thêm</th>
                    <th>Sửa</th>
                    <th>Xóa</th>
                    <th>Khôi phục</th>
                    <!-- <th>Khác</th> -->
                </thead>
                <tbody>
                    <tr v-for="(group, index) of Object.keys(form.permissions)">
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
                        <!-- <td width="100px"></td> -->
                    </tr>
                </tbody>
            </table>
        </div>
    </Form>
</template>
<script>
export default {
    props: ["item", "schema"],
    data() {
        return {
            formData: this.item,
        };
    },
};
</script>
