<template>
    <Modal v-model="visible" :loading="loading" width="600" :title="title"
           @on-ok="handleSubmit('formItem')" @on-cancel="onCancel">
        <Form ref="formItem" :model="formItem" :label-width="70">
            <Row>
                <Col span="11">
                    <FormItem label="用户名" prop="username"
                              :rules="{required: true, message: '请输入用户名', trigger: 'blur'}">
                        <Input v-model="formItem.username"/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="用户昵称" prop="nickname">
                        <Input v-model="formItem.nickname"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem label="用户邮箱" prop="email"
                              :rules="{required: false, type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur'}">
                        <Input v-model="formItem.email"/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="手机号码" prop="mobile"
                              :rules="{required: false, pattern: /1[3-8]\d{9}/, message: '请输入11位且有效手机号码', trigger: 'blur'}">
                        <Input v-model="formItem.mobile"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem label="真实姓名" prop="real_name"
                              :rules="{required: true, message: '请输入真实姓名', trigger: 'blur'}">
                        <Input v-model="formItem.real_name"/>
                    </FormItem>
                </Col>
                <Col span="11" >
                    <FormItem label="所属部门" prop="department">
                        <Input v-model="formItem.department"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col>
                    <FormItem label="账号状态" prop="status"
                              :rules="{required: true, type:'number', message: '请选择状态', trigger: 'blur'}">
                        <RadioGroup v-model="formItem.status">
                            <Radio v-for="(text, index) in status.text" v-if="text" :label="index" :key="index">
                                <Tag :color="status.color[index]">{{text}}</Tag>
                            </Radio>
                        </RadioGroup>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col>
                    <FormItem label="拥有角色">
                        <CheckboxGroup v-model="roleIds" disable>
                            <template v-if="list[0]">
                                <Checkbox v-for="(role, index) in list[0].allRoles" :key="index" :label="role.id">
                                    <Tag color="primary">{{role.name}}</Tag>
                                </Checkbox>
                            </template>

                        </CheckboxGroup>
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
            list: {
                type: Array,
                default() {
                    return []
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
                roleIds: [],
                status: {
                    text: ['冻结', '正常'],
                    color: ['default', 'success'],
                },
                formItem: {
                    id: '',
                    username: '',
                    userNick: '',
                    real_name: '',
                    mobile: '',
                    email: '',
                    department: '',
                    avatar: '',
                    role_id: '',
                    status: 1
                }
            }
        },
        watch: {
            value(value) {
                this.visible = value;
                if (value) {
                    this.formItem = this.edit ? {...this.item} : {...this.formItem};
                    this.roleIds = this.item.role_id
                        ? this.item.role_id.split(',').map((num) => Number(num) )
                        : [];
                }
            }
        },
        methods: {
            handleSubmit (name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        let url = this.edit ? this.url + '/' + this.formItem.id : this.url;
                        let request = this.edit ? this.$messageHttp.put : this.$messageHttp.post;
                        let params = {...this.formItem, ...{role_id: this.roleIds.join(',')}};
                        request(url, params,
                            (result) => {
                                if (this.edit) {
                                    this.$emit('updated', {...this.item, ...this.formItem})
                                } else {
                                    this.$emit('created', {...this.formItem, ...result.data});
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

