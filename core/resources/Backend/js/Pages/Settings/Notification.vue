<template layout>
    <WrapSetting>
        <Form
            v-model="formData"
            v-slot="{ form }"
            :config="{ canDestroy: false, addGrid: false }"
        >
            <div class="card">
                <div class="card-header">Thông báo liên hệ</div>
                <div class="card-body">
                    <InlineMessage severity="info">
                        Khi website nhận được liên hệ mới, hệ thống sẽ tự động gửi thông báo tới email của
                        bạn.
                    </InlineMessage>
                    <Field
                        v-model="form.notification_production_to"
                        :field="{
                            type: 'tags',
                            name: 'notification_production_to',
                            label: 'Email môi trường thật (Production)',
                            help: 'Tips: Có thể nhập nhiều email, các email được ngăn cách bởi dấu phẩy',
                        }"
                    />
                    <Field
                        v-model="form.notification_staging_to"
                        :field="{
                            type: 'tags',
                            name: 'notification_staging_to',
                            label: 'Email môi trường test  (Local/Staging)',
                            help: 'Tips: Có thể nhập nhiều email, các email được ngăn cách bởi dấu phẩy',
                        }"
                    />
                </div>
            </div>
            <!-- <div class="card">
                    <div class="card-header">Thông báo lỗi</div>
                    <div class="card-body">
                        <Field
                            v-model="form.notification_to"
                            :field="{
                                type: 'email',
                                name: 'notification_to',
                                label: 'Email',
                                help: 'Tips: Có thể nhập nhiều email, các email được ngăn cách bởi dấu phẩy',
                            }"
                        />
                        <hr />
                        <Field
                            v-model="form.discord_webhook"
                            :field="{
                                type: 'text',
                                name: 'discord_webhook',
                                label: 'Discord Webhook',
                            }"
                        />
                        <hr />
                        <Field
                            v-model="form.slack_webhook"
                            :field="{
                                type: 'text',
                                name: 'slack_webhook',
                                label: 'Slack Webhook',
                            }"
                        />
                    </div>
                </div> -->

            <Field
                class="hidden"
                disabled
                v-model="form.id"
                :field="{ default: 'notification' }"
            />
        </Form>
    </WrapSetting>
</template>
<script>
import WrapSetting from "@Core/Components/WrapSetting.vue";
export default {
    components: { WrapSetting },
    props: ["item", "schema"],
    data() {
        return {
            formData: this.item,
        };
    },
};
</script>
