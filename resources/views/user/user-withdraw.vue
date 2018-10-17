<template>
    <Modal v-model="visible" width="1000" :title="title" @on-ok="onCancel" @on-cancel="onCancel" :footer-hide="true">
        <list-table ref="listTable" :url="withdrawUrl" :columns="columns" :auto-load="false"
                   :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
            <Row slot="search-block">
                <Col span="6">
                    <FormItem label="银行名称">
                        <Input v-model="searchItem.jwl.ub.bank_name" />
                    </FormItem>
                </Col>
                <Col span="12">
                    <FormItem label="提现时间">
                        <DatePicker type="datetimerange" split-panels style="width: 280px"
                                    v-model="searchItem.r.addtime" ></DatePicker>
                    </FormItem>
                </Col>
            </Row>
        </list-table>
        <Modal v-model="editorModal" :loading="editLoading" width="500" title="编辑提现明细"
               class="top-modal" @on-ok="update" @on-cancel="editorModal = false">
            <Form ref="formItem" :model="formItem" :label-width="70">
                <Row>
                    <Col>
                        <FormItem label="类型" prop="state"
                                  :rules="{required: true, type:'number', message: '请选择类型', trigger: 'blur'}">
                            <RadioGroup v-model="formItem.state">
                                <Radio v-for="(text, index) in stateText" v-if="text" :label="index" :key="index">
                                    <Tag :color="stateColor[index]">{{text}}</Tag>
                                </Radio>
                            </RadioGroup>
                        </FormItem>
                    </Col>

                </Row>
                <Row>
                    <Col>
                        <FormItem label="金额" prop="money"
                                  :rules="{required: true, type:'number', message: '请输入金额', trigger: 'blur'}">
                            <InputNumber v-model="formItem.money" :step="0.01"/>
                        </FormItem>
                    </Col>
                </Row>
            </Form>
        </Modal>
    </Modal>
</template>

<script>
    export default {
        name: 'user-withdraw',
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
            let searchObj = {
                j: {ub:'bank_name,bank_card'},
                jo: {ub:'l,id,bank_id'},
                ja: {ub:'bankInfo'},
                jwl: {ub: {bank_name: ''}},
                r: {addtime: []},
            };

            let data = {
                searchItem: this.deepCopy(searchObj),
                localItem: this.deepCopy(searchObj),
                visible: this.value,
                editorModal: false,
                editLoading: true,
                changed: true,
                stateText: ['提现中', '成功', '失败'],
                stateColor: ['gray', 'success', 'default'],
                formItem: {
                    id: '',
                    bank_id: '',
                    user_id: '',
                    money: null,
                    state: '',
                },
                columns: [
                    {
                        type: 'selection',
                        width: 60,
                        align: 'center'
                    },
                    {
                        title: 'ID',
                        width: 70,
                        key: 'id',
                        sortable: 'custom'
                    },
                    {
                        title: '用户名',
                        render: (h) => h('span',  this.user.userName)
                    },
                    {
                        title: '银行名称',
                        render: (h, params) => h('span', params.row.bankInfo.bank_name)
                    },
                    {
                        title: '银行卡号',
                        render: (h, params) => h('span', params.row.bankInfo.bank_card)
                    },
                    {
                        title: '提现金额',
                        key: 'money',
                        sortable: 'custom'
                    },
                    {
                        title: '提现状态',
                        key: 'state',
                        sortable: 'custom',
                        render: (h, params) => {
                            return h('Tag', {
                                props: {color: this.stateColor[params.row.state]}
                            }, this.stateText[params.row.state])
                        }
                    },
                    {
                        title: '提现时间',
                        key: 'addtime',
                        sortable: 'custom'
                    }
                ]
            };

            let operator = this.$authButtonColumn([
                {name:'user_withdraw_update', click: this.openEditor},
                {name:'user_withdraw_delete', click: this.openDelConfirm}
            ]);
            if (operator) data.columns.push(operator);

            return data
        },
        watch: {
            value(value) {
                this.visible = value;
                if (value && this.changed) {
                    this.reload();
                    this.changed = false;
                }
            },
            'user.id'(newVal, oldVal) {
                this.changed = newVal !== oldVal
            }
        },
        computed: {
            withdrawUrl() {
                return this.url + '/' + this.user.id + '/withdraws';
            },
        },
        methods: {
            openEditor(params) {
                this.editorModal = true;
                this.rowIndex = params.index;
                this.formItem = {...params.row};
                this.formItem.money = Number.parseFloat(params.row.money);
            },
            update () {
                this.$refs.formItem.validate((valid) => {
                    if (valid) {
                        let withdrawUrl = this.withdrawUrl + '/' + this.formItem.id ;
                        this.updateItem = {...this.formItem};
                        this.updateItem.money = this.updateItem.money.toFixed(2);
                        this.editLoading = true;
                        this.$messageHttp.put(withdrawUrl, this.updateItem,
                            () => {
                                this.editorModal = false;
                                this.$refs.listTable.getList().splice(this.rowIndex, 1, this.updateItem)
                            }, null,
                            () => this.resetEditorLoading()
                        )
                    } else {
                        this.resetEditorLoading()
                    }
                })
            },
            openDelConfirm (params) {
                this.$modalHttp.delete(this.withdrawUrl + '/' + params.row.id, null, null,
                    () => {
                        this.$Message.success('删除成功');
                        this.reload();
                    }
                )
            },
            resetEditorLoading() {
                this.editLoading = false;
                this.$nextTick(() => this.editLoading = true)
            },
            onCancel() {
                this.visible = false;
                this.$refs.formItem.resetFields();
                this.$emit('input', false);
            },
            reload() {
                this.$refs.listTable.loadList()
            },
            deepCopy(obj) {
                return JSON.parse(JSON.stringify(obj))
            }
        }
    };
</script>

