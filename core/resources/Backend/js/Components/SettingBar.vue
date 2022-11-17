<template>
    <div class="col-span-full lg:col-span-4">
        <div class="overflow-hidden card">
            <div class="card-body !p-0">
                <Accordion
                    :activeIndex="Array.from(Array(20).keys())"
                    :multiple="true"
                >
                    <AccordionTab header="Cài đặt thông tin">
                        <Link
                            class="item"
                            :href="
                                route('admin.settings.form', {
                                    id: 'general',
                                })
                            "
                            :class="{
                                active: isUrl('admin.settings.form', {
                                    id: 'general',
                                }),
                            }"
                        >
                            Thông tin chung
                        </Link>
                        <Link
                            :href="route('admin.settings.meta-pages.index')"
                            class="item"
                            :class="{
                                active: isUrl('admin.settings.meta-pages.*'),
                            }"
                        >
                            Meta
                        </Link>
                        <Link
                            class="item"
                            :href="
                                route('admin.settings.form', {
                                    id: 'robots_txt',
                                })
                            "
                            :class="{
                                active: isUrl('admin.settings.form', {
                                    id: 'robots_txt',
                                }),
                            }"
                        >
                            Robots.txt
                        </Link>
                        <Link
                            :href="route('admin.settings.redirects.index')"
                            class="item"
                            :class="{
                                active: isUrl('admin.settings.redirects.*'),
                            }"
                        >
                            Redirects
                        </Link>
                    </AccordionTab>
                    <AccordionTab header="Email">
                        <Link
                            class="item"
                            :href="
                                route('admin.settings.form', {
                                    id: 'smtp',
                                })
                            "
                            :class="{
                                active: isUrl('admin.settings.form', {
                                    id: 'smtp',
                                }),
                            }"
                        >
                            Cấu hình SMTP
                        </Link>
                        <Link
                            class="item"
                            :href="
                                route('admin.settings.form', {
                                    id: 'notification',
                                })
                            "
                            :class="{
                                active: isUrl('admin.settings.form', {
                                    id: 'notification',
                                }),
                            }"
                        >
                            Thông báo
                        </Link>
                    </AccordionTab>
                </Accordion>
            </div>
        </div>
    </div>
</template>
