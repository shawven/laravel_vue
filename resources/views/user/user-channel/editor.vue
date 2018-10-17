<template>
    <Modal v-model="visible" :loading="loading" width="600" :title="title"
           @on-ok="handleSubmit('formItem')" @on-cancel="onCancel">
        <Form ref="formItem" :model="formItem" :label-width="70">
            <Row>
                <Col span="11">
                    <FormItem label="标示" prop="mark"
                              :rules="{required: true, message: '请输入标示', trigger: 'blur'}">
                        <Input v-model="formItem.mark"/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="名称" prop="name"
                              :rules="{required: true, message: '请输入名称', trigger: 'blur'}">
                        <Input v-model="formItem.name"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col>
                    <FormItem label="推广链接">
                        <Input v-model="formItem.app_promotion_url"/>
                    </FormItem>
                </Col>
            </Row>
        </Form>
    </Modal>
</template>

<script>
    export default {
        name: 'editor',
        props: {
            url: String,
            title: String,
            edit: {
                type: Boolean,
                default: true
            },
            item: {
                type: Object,
                default() {
                    return {}
                }
            },
            value: {
                type: Boolean,
                default: false
            },
        },

        data () {
            return {
                loading: true,
                visible: this.value,
                formItem: {
                    id: '',
                    mark: '',
                    name: '',
                    app_promotion_url: '',
                }
            }
        },
        watch: {
            value(value) {
                this.visible = value;
                this.formItem = value ? {...this.item} : {}
            }
        },
        methods: {
            handleSubmit (name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        let url = this.edit ? this.url + '/' + this.formItem.id : this.url;
                        let request = this.edit ? this.$messageHttp.put : this.$messageHttp.post;
                        request(url, this.formItem,
                            () => {
                                if (this.edit) {
                                    this.$emit('updated', {...this.item, ...this.formItem})
                                } else {
                                    this.$emit('created');
                                }
                                this.onCancel();
                            }, null,
                            () => this.resetLoading()
                        )
                    } else {
                        this.resetLoading();
                    }
                })
            },
            onCancel() {
                this.visible = false;
                this.$refs.formItem.resetFields();
                this.$emit('input', false);
            },
            resetLoading() {
                this.loading = false;
                this.$nextTick(() => this.loading = true)
            },
        }
    };
</script>

