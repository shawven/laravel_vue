<template>
    <Modal v-model="visible" width="800" :title="title"  @on-cancel="onCancel" :footer-hide="true">
        <list-table ref="listTable" :url="bankUrl" :columns="columns" :auto-load="false" :show-operation="false"/>
        <Modal v-model="editorModal" :loading="editLoading" width="500" title="编辑银行卡"
               class="top-modal" @on-ok="update" @on-cancel="editorModal = false">
            <Form ref="formItem" :model="formItem" :label-width="70">
                <Row>
                    <Col span="11">
                        <FormItem label="用户昵称" prop="user_nick"
                                  :rules="{required: true, message: '请输入用户昵称', trigger: 'blur'}">
                            <Input v-model="formItem.user_nick" disabled/>
                        </FormItem>
                    </Col>
                    <Col span="11" offset="2">
                        <FormItem label="状态" prop="isRealAttestation">
                            <RadioGroup v-model="formItem.state">
                                <Radio v-for="(text, index) in stateText" v-if="text" :label="index" :key="index">
                                    <Tag :color="stateColor[index]">{{text}}</Tag>
                                </Radio>
                            </RadioGroup>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="11">
                        <FormItem label="银行名称" prop="bank_name"
                                  :rules="{required: true, message: '请输入银行名称', trigger: 'blur'}">
                            <Input v-model="formItem.bank_name" />
                        </FormItem>
                    </Col>
                    <Col span="11" offset="2">
                        <FormItem label="银行卡号" prop="bank_card"
                                :rules="{required: true, message: '请输入银行卡号', trigger: 'blur'}">
                            <Input v-model="formItem.bank_card" />
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="11">
                        <FormItem label="预留手机" prop="bank_phone"
                                  :rules="{required: true, pattern: /1[3-8]\d{9}/, message: '请输入11位手机号码', trigger: 'blur'}">
                            <Input v-model="formItem.bank_phone" />
                        </FormItem>
                    </Col>
                </Row>
            </Form>
        </Modal>
    </Modal>
</template>

<script>
    export default {
        name: 'user-bank',
        props: {
            url: String,
            title: String,
            user: {
                type: Object,
                default() {
                    return {};
                }
            },
            value: {
                type: Boolean,
                default: false
            },
        },
        data() {
            let data = {
                rowIndex: '',
                visible: this.value,
                editorModal: false,
                editLoading: true,
                loading: false,
                changed: true,
                stateText: ['', '有效', '无效'],
                stateColor: ['', 'success', 'default'],
                formItem: {
                    id: '',
                    user_id: '',
                    user_nick: '',
                    bank_name: '',
                    bank_card: '',
                    bank_phone: '',
                    state: '',
                    addtime: '',
                    updatetime: ''
                },
                columns: [
                    {
                        title: 'ID',
                        width: 70,
                        key: 'id',
                        sortable: 'custom'
                    },
                    {
                        title: '昵称',
                        key: 'user_nick',
                    },
                    {
                        title: '银行名称',
                        key: 'bank_name'
                    },
                    {
                        title: '银行卡号',
                        key: 'bank_card'
                    },
                    {
                        title: '预留手机',
                        key: 'bank_phone'
                    },
                    {
                        title: '状态',
                        key: 'state',
                        sortable: 'custom',
                        render: (h, params) => {
                            return h('Tag', {
                                props: {color: this.stateColor[params.row.state]}
                            }, this.stateText[params.row.state])
                        }
                    },
                    {
                        title: '添加时间',
                        key: 'addtime',
                        sortable: 'custom'
                    },
                ]
            };

            let operator = this.$authButtonColumn([
                {name:'user_bank_update', click: this.openEditor},
                {name:'user_bank_delete', click: this.openDelConfirm}
            ]);
            if (operator) data.columns.push(operator);


            return data
        },
        watch: {
            value(value) {
                this.visible = value;
                if (value && this.changed) {
                    this.getBanks();
                    this.changed = false;
                }
            },
            'user.id'(newVal, oldVal) {
                this.changed = newVal !== oldVal
            }
        },
        computed: {
           bankUrl() {
               return this.url + '/' + this.user.id + '/banks';
           }
        },
        methods: {
            openEditor(params) {
                this.editorModal = true;
                this.rowIndex = params.index;
                this.formItem = {...params.row};
            },
            update () {
                this.$refs.formItem.validate((valid) => {
                    if (valid) {
                        let bankUrl = this.bankUrl + '/' + this.formItem.id ;
                        this.$messageHttp.put(bankUrl, this.formItem,
                            () => {
                                this.editorModal = false;
                                this.getBanks().splice(this.rowIndex, 1, this.formItem)
                            }, null,
                            () => this.resetEditorLoading()
                        )
                    } else {
                        this.resetEditorLoading()
                    }
                })
            },
            openDelConfirm (params) {
                this.$modalHttp.delete(this.bankUrl + '/' + params.row.id, null, null,
                    () => {
                        this.$Message.success('删除成功');
                        this.getBanks().splice(params.index, 1)
                    }
                )
            },
            resetEditorLoading() {
                this.editLoading = false;
                this.$nextTick(() => this.editLoading = true)
            },
            getBanks() {
                return this.$refs.listTable.loadList()
            },
            onCancel() {
                this.visible = false;
                this.$refs.formItem.resetFields();
                this.$emit('input', false);
            },
        }
    };
</script>

