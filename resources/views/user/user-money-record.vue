<template>
    <Modal v-model="visible" width="1100" :title="title" @on-ok="onCancel" @on-cancel="onCancel" :footer-hide="true">
        <list-table ref="listTable" :url="mrUrl" :columns="columns" :auto-load="false"
               :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
            <Row slot="search-block">
                <Col span="12">
                    <FormItem label="日期">
                        <DatePicker type="datetimerange" split-panels style="width: 280px" v-model="searchItem.r.addtime"/>
                    </FormItem>
                </Col>
            </Row>
        </list-table>
        <Modal v-model="editorModal" :loading="editLoading" width="500" title="编辑资金明细"
               class="top-modal" @on-ok="update" @on-cancel="editorModal = false">
            <Form ref="formItem" :model="formItem" :label-width="70">
                <Row>
                    <Col>
                        <FormItem label="类型" prop="type"
                                  :rules="{required: true, type:'number', message: '请选择类型', trigger: 'blur'}">
                            <RadioGroup v-model="formItem.type">
                                <Radio v-for="(text, index) in typeText" v-if="text" :label="index" :key="index">
                                    <Tag :color="typeColor[index]">{{text}}</Tag>
                                </Radio>
                            </RadioGroup>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col>
                        <FormItem label="类型描述" prop="typename"
                                  :rules="{required: true, message: '请输入类型描述', trigger: 'blur'}">
                            <Input v-model="formItem.typename" />
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="11">
                        <FormItem label="支付方式" prop="mode"
                                  :rules="{required: true, message: '请输入支付方式', trigger: 'blur'}">
                            <Input v-model="formItem.mode" />
                        </FormItem>
                    </Col>
                    <Col span="11" offset="2">
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
        name: 'user-money-record',
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
                typeText: ['', '奖金进账', '提现', '购彩', '充值'],
                typeColor: ['', 'success', 'GoldenRod', 'primary', 'LightCoral'],
                formItem: {
                    id: '',
                    userId: '',
                    type: '',
                    typename: '',
                    mode: '',
                    money: 0,
                    addtime: ''
                },
                columns: [
                    {
                        title: 'ID',
                        width: 70,
                        key: 'id',
                        sortable: 'custom'
                    },
                    {
                        title: '类型',
                        key: 'type',
                        sortable: 'custom',
                        render: (h, params) => {
                            return h('Tag', {
                                props: {color: this.typeColor[params.row.type]}
                            }, this.typeText[params.row.type])
                        }
                    },
                    {
                        title: '类型描述',
                        key: 'typename',
                    },
                    {
                        title: '支付方式',
                        key: 'mode',
                        render: (h, params) => {
                            if (params.row.mode) {
                                return h('Tag', {
                                    props: {color: this.payWayColor[params.row.mode]}
                                }, this.payWayText[params.row.mode])
                            }
                        }
                    },
                    {
                        title: '金额',
                        key: 'money',
                        sortable: 'custom',
                        render: (h, params) => h('span', params.row.money + ' ￥')
                    },
                    {
                        title: '操作前',
                        key: 'pre_total',
                        render: (h, params) => h('span', params.row.pre_total + ' ￥')
                    },
                    {
                        title: '操作后',
                        key: 'after_total',
                        render: (h, params) => h('span', params.row.after_total + ' ￥')
                    },
                    {
                        title: '时间',
                        key: 'addtime',
                        sortable: 'custom'
                    }
                ]
            };

            let operator = this.$authButtonColumn([
                {name:'user_money_record_update', click: this.openEditor},
                {name:'user_money_record_delete', click: this.openDelConfirm}
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
            mrUrl() {
                return this.url + '/' + this.user.id + '/money_records';
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
                        let mrUrl = this.mrUrl + '/' + this.formItem.id ;
                        this.updateItem = {...this.formItem};
                        this.updateItem.money = this.updateItem.money.toFixed(2);
                        this.$messageHttp.put(mrUrl, this.updateItem,
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
                this.$modalHttp.delete(this.mrUrl + '/' + params.row.id, {}, null, null,
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

