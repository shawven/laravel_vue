<template>
    <Modal v-model="visible" :loading="loading" width="800" :title="title"
           @on-ok="handleSubmit('formItem')" @on-cancel="onCancel">
        <Form ref="formItem" :model="formItem" :label-width="70">
            <Row>
                <Col span="11">
                    <FormItem label="用户名" prop="userName"
                              :rules="{required: true, message: '请输入用户名', trigger: 'blur'}">
                        <Input v-model="formItem.userName" disabled/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="用户昵称" prop="usernick"
                              :rules="{required: true, message: '请输入用户昵称', trigger: 'blur'}">
                        <Input v-model="formItem.usernick" />
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem label="手机号码" prop="userPhone"
                              :rules="{required: true, pattern: /1[3-8]\d{9}/, message: '请输入11位手机号码', trigger: 'blur'}">
                        <Input v-model="formItem.userPhone" />
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="真实号码" prop="real_phone"
                              :rules="{pattern: /1[3-8]\d{9}/, message: '请输入11位真实手机号码', trigger: 'blur'}">
                        <Input v-model="formItem.real_phone" />
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem label="身份证" prop="real_card"
                              :rules="{pattern:/\d{17}[0-9Xx]|\d{15}/, message: '请输入15位或18位身份证', trigger: 'blur'}">
                        <Input v-model="formItem.real_card" />
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="钱包" prop="wallet"
                              :rules="{required: true, message: '请输入钱包', trigger: 'blur'}">
                        <Input v-model="formItem.wallet" disabled/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11" >
                    <FormItem label="实名认证" prop="isRealAttestation">
                        <RadioGroup v-model="formItem.isRealAttestation">
                            <Radio v-for="(text, index) in stateText" v-if="text" :label="index" :key="index">
                                <Tag :color="stateColor[index]">{{text}}</Tag>
                            </Radio>
                        </RadioGroup>
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
            user: {
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
                stateText: ['', '未认证', '已认证'],
                stateColor: ['', 'default', 'success'],
                formItem: {
                    id: '',
                    usernick: '',
                    userName: '',
                    userPassword: '',
                    userPhone: '',
                    wallet: '',
                    addtime: '',
                    updatetime: '',
                    isRealAttestation: '',
                    real_name: '',
                    real_card: '',
                    real_phone: '',
                    avatar: '',
                    handsel: '',
                    balance: '',
                    devicdi: ''
                }
            }
        },
        watch: {
            value(value) {
                this.visible = value;
                this.formItem = value ? {...this.user} : {}
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
                                    this.$emit('updated', {...this.user, ...this.formItem})
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

