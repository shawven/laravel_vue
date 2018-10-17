<template>
    <Modal v-model="visible" :loading="loading" width="400" :title="title"
           @on-ok="handleSubmit('formItem')" @on-cancel="onCancel">
        <Form ref="formItem" :model="formItem" :label-width="70">
            <Row>
                <Col span="24">
                    <FormItem label="角色名称" prop="name"
                              :rules="{required: true, message: '请输入角色名', trigger: 'blur'}">
                        <Input v-model="formItem.name"/>
                    </FormItem>
                </Col>
                <Col span="24">
                    <FormItem label="角色描述" prop="desc"
                              :rules="{required: true, message: '请输入角色描述', trigger: 'blur'}">
                        <Input v-model="formItem.desc"/>
                    </FormItem>
                </Col>
            </Row>
            <Row :style="{maxHeight: '500px',overflowY: 'auto'}" v-if="!isSuperAdmin" class="role-menu-tree">
                <Col span="22" offset="2" v-for="(node1) in checkedAuthorities" :key="node1.id" class="my-2">
                    <Row>
                        <span class="font-sm mr-3"><Icon :type="node1.icon"/> {{node1.title}}</span>
                        <i-switch v-model="node1.checked">
                            <Icon type="android-done" slot="open"></Icon>
                            <Icon type="android-close" slot="close"></Icon>
                        </i-switch>
                        <Col span="22" offset="2" v-if="node1.children">
                            <Row v-for="node2 in node1.children" :key="node2.id" class="my-1">
                                <span class="font-sm  mr-3"><Icon :type="node2.icon"/> {{node2.title}}</span>
                                <i-switch v-model="node2.checked">
                                    <Icon type="android-done" slot="open"></Icon>
                                    <Icon type="android-close" slot="close"></Icon>
                                </i-switch>
                                <Col span="22" offset="2" v-if="node2.children">
                                    <Row v-for="node3 in node2.children" :key="node3.id" class="my-1">
                                        <span class="font-sm mr-3"> <Icon :type="node3.icon"/> {{node3.title}}</span>
                                        <i-switch v-model="node3.checked">
                                            <Icon type="android-done" slot="open"></Icon>
                                            <Icon type="android-close" slot="close"></Icon>
                                        </i-switch>
                                        <Col span="22" offset="2" v-if="node3.children">
                                            <Row v-for="node4 in node3.children" :key="node4.id" class="my-1">
                                                <span class="font-sm mr-3"> <Icon :type="node4.icon"/> {{node4.title}}</span>
                                                <i-switch v-model="node4.checked">
                                                    <Icon type="android-done" slot="open"></Icon>
                                                    <Icon type="android-close" slot="close"></Icon>
                                                </i-switch>
                                            </Row>
                                        </Col>
                                    </Row>
                                </Col>
                            </Row>
                        </Col>
                    </Row>
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
            allAuthorities: {
                type: Array,
                default() {
                    return []
                }
            }

        },

        data() {
            return {
                loading: true,
                authorityIds: [],
                checkedAuthorities: [],
                visible: this.value,
                status: {
                    text: ['无效', '有效'],
                    color: ['default', 'success'],
                },
                formItem: {
                    id: '',
                    name: '',
                    desc: '',
                    auth_id: ''
                }
            }
        },
        watch: {
            value(value) {
                this.visible = value;
                if (value) {
                    this.formItem = {...this.item};
                    this.authorityIds = this.item.auth_id
                        ? this.item.auth_id.split(',').map((num) => Number(num))
                        : [];
                    this.checkedAuthorities = this.checkAuthorities(JSON.parse(JSON.stringify(this.allAuthorities)))
                }
            }
        },
        computed: {
            isSuperAdmin() {
                return +this.item.id === this.$store.getters.superAdminRole
            }
        },
        methods: {
            handleSubmit(name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        let url = this.edit ? this.url + '/' + this.formItem.id : this.url;
                        let request = this.edit ? this.$messageHttp.put : this.$messageHttp.post;
                        this.formItem.auth_id = [...new Set(this.getCheckedId(this.checkedAuthorities))].join(',');
                        request(url, this.formItem,
                            () => {
                                if (this.edit) {
                                    this.$emit('updated', {...this.item, ...this.formItem})
                                } else {
                                    this.$emit('created');
                                }
                                this.onCancel();
                            },null,
                            () => this.resetLoading()
                        )
                    } else {
                        this.resetLoading();
                    }
                })
            },
            getCheckedId(authorities) {
                let ids = [];
                authorities.forEach((authority) => {
                    if (authority.checked) {
                        ids.push(authority.id);
                    }
                    if (authority.children && authority.children.length > 0) {
                        ids.push(...this.getCheckedId(authority.children));
                    }
                });
                return ids;
            },
            checkAuthorities(authorities) {
                return authorities.map((authority) => {
                    if (this.authorityIds.includes(authority.id)) {
                        authority.checked = true;
                    }
                    if (authority.children && authority.children.length > 0) {
                        this.checkAuthorities(authority.children)
                    }
                    return authority
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
<style lang="less">
    .role-menu-tree{
        .ivu-switch {
            width: 36px;
            height: 18px;
            line-height: 18px;
        }
        .ivu-switch-inner {
            left: 18px
        }
        .ivu-switch-checked:after {
            left: 19px;
        }
        .ivu-switch-checked {
            .ivu-switch-inner {
                left: 4px
            }
        }
        .ivu-switch:after {
            width: 14px;
            height: 14px;
        }
    }
</style>
