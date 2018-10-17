<template>
    <Modal v-model="visible" width="600" :title="title" @on-ok="onCancel" @on-cancel="onCancel" :footer-hide="true">
        <list-table ref="listTable" :url="rechargeUrl" :columns="columns" :auto-load="false"
                   :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
            <Row slot="search-block">
                <Col span="12">
                    <FormItem label="日期">
                        <DatePicker type="datetimerange" split-panels style="width: 280px" v-model="searchItem.r.addtime"/>
                    </FormItem>
                </Col>
            </Row>
        </list-table>
        <Modal v-model="editorModal" :loading="editLoading" width="500" title="编辑充值记录"
               class="top-modal" @on-ok="update" @on-cancel="editorModal = false">
            <Form ref="formItem" :model="formItem" :label-width="70">
                <Row>
                    <Col span="11" >
                        <FormItem label="金额" prop="money"
                                  :rules="{required: true, type:'number', message: '请输入金额', trigger: 'blur'}">
                            <InputNumber v-model="formItem.money" :step="0.01"/>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <FormItem label="状态" prop="state"
                              :rules="{required: true, type:'number', message: '请选择类型', trigger: 'blur'}">
                        <RadioGroup v-model="formItem.state">
                            <Radio v-for="(text, index) in stateText" v-if="text" :label="index" :key="index">
                                <Tag :color="stateColor[index]">{{text}}</Tag>
                            </Radio>
                        </RadioGroup>
                    </FormItem>
                </Row>
            </Form>
        </Modal>
    </Modal>
</template>

<script>
    export default {
        name: 'user-recharge',
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
                r: {addtime: []},
            };

            let data = {
                searchItem: this.deepCopy(searchObj),
                localItem: this.deepCopy(searchObj),
                visible: this.value,
                editorModal: false,
                editLoading: true,
                changed: true,
                payWayText: {WALLET: '钱包', WXPAY:'微信', ALIPAY:'支付宝', caijin: '彩金'},
                payWayColor: {WALLET: 'Tomato', WXPAY: 'LimeGreen', ALIPAY:'primary', caijin: 'GoldenRod'},
                stateText: ['交易中', '成功', '失败'],
                stateColor: ['default', 'success', 'error'],
                formItem: {
                    id: '',
                    money: 0,
                    state: '',
                    user_id: '',
                    addtime: '',
                    updatetime: '',
                    payway: ''
                },
                columns: [
                    {
                        title: 'ID',
                        width: 70,
                        key: 'id',
                        sortable: 'custom'
                    },
                    {
                        title: '金额',
                        key: 'money',
                        sortable: 'custom'
                    },
                    {
                        title: '支付方式',
                        key: 'payway',
                        render: (h, params) => {
                            return h('Tag', {
                                props: {color: this.payWayColor[params.row.payway]}
                            }, this.payWayText[params.row.payway])
                        }
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
                    //
                    // {
                    //     title: '添加时间',
                    //     key: 'addtime',
                    //     sortable: 'custom'
                    // }
                ]
            };
            let operator = this.$authButtonColumn([
                {name:'user_recharge_update', click: this.openEditor},
                {name:'user_recharge_delete', click: this.openDelConfirm}
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
            rechargeUrl() {
                return this.url + '/' + this.user.id + '/recharges';
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
                        let rechargeUrl = this.rechargeUrl + '/' + this.formItem.id ;
                        this.updateItem = {...this.formItem};
                        this.updateItem.money = this.updateItem.money.toFixed(2);
                        this.$messageHttp.put(rechargeUrl, this.updateItem,
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
                this.$modalHttp.delete(this.rechargeUrl + '/' + params.row.id, {}, null, null,
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

