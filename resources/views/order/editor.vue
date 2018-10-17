<template>
    <Modal v-model="visible" :loading="loading" width="600" :title="title"
           @on-ok="handleSubmit('formItem')" @on-cancel="onCancel">
        <Form ref="formItem" :model="formItem" :label-width="70">
            <Row>
                <Col span="11">
                    <FormItem label="玩法" prop="play_method"
                              :rules="{required: true, message: '请输入玩法', trigger: 'blur'}">
                        <Input v-model="formItem.play_method"/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                <FormItem label="注数" prop="zhushu"
                          :rules="{required: true, type:'number', message: '请输入注数', trigger: 'blur'}">
                    <InputNumber v-model="formItem.zhushu" :step="1"/>
                </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem label="订单金额" prop="total_money"
                              :rules="{required: true, type:'number', message: '请输入订单金额', trigger: 'blur'}">
                        <InputNumber v-model="formItem.total_money" :step="0.01"/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="预计奖金" prop="bonus"
                              :rules="{required: true, type:'number', message: '请输入预计奖金', trigger: 'blur'}">
                        <InputNumber v-model="formItem.bonus" :step="0.01"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem label="税额" prop="tax"
                              :rules="{required: true, type:'number', message: '请输入税额', trigger: 'blur'}">
                        <InputNumber v-model="formItem.tax" :step="0.01"/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="税后奖金" prop="afterTaxBonus"
                              :rules="{required: true, type:'number', message: '请输入税后奖金', trigger: 'blur'}">
                        <InputNumber v-model="formItem.afterTaxBonus" :step="0.01"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col>
                    <FormItem label="支付状态" prop="pay_type"
                              :rules="{required: true, type:'number', message: '请选择支付状态', trigger: 'blur'}">
                        <RadioGroup v-model="formItem.pay_type">
                            <Radio v-for="(text, index) in tagItem.payTypeText" v-if="text" :label="index" :key="index">
                                <Tag :color="tagItem.payTypeColor[index]">{{text}}</Tag>
                            </Radio>
                        </RadioGroup>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col>
                    <FormItem label="出票状态" prop="to_draw"
                              :rules="{required: true, type:'number', message: '请选择出票状态', trigger: 'blur'}">
                        <RadioGroup v-model="formItem.to_draw">
                            <Radio v-for="(text, index) in tagItem.drawText" v-if="text" :label="index" :key="index">
                                <Tag :color="tagItem.drawColor[index]">{{text}}</Tag>
                            </Radio>
                        </RadioGroup>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col>
                    <FormItem label="开奖状态" prop="state"
                              :rules="{required: true, type:'number', message: '请选择开奖状态', trigger: 'blur'}">
                        <RadioGroup v-model="formItem.state">
                            <Radio v-for="(text, index) in tagItem.stateText" v-if="text" :label="index" :key="index">
                                <Tag :color="tagItem.stateColor[index]">{{text}}</Tag>
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
            tagItem: Object,
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
                    user_id: '',
                    order_id: '',
                    sid: '',
                    total_money: null,
                    type: '',
                    guoguan: '',
                    beishu: '',
                    play_method: '',
                    payway: '',
                    paytime: '',
                    state: '',
                    addtime: '',
                    updatetime: '',
                    payTypeName: '',
                    bonus: null,
                    bet: '',
                    to_draw: '',
                    pay_type: '',
                    zhushu: null,
                    tax: null,
                    afterTaxBonus: null
                }
            }
        },
        watch: {
            value(value) {
                this.visible = value;
                if (value && this.edit) {
                    let item = {...this.item};
                    item.total_money = Number.parseFloat(item.total_money);
                    item.bonus = Number.parseFloat(item.bonus);
                    item.tax = Number.parseFloat(item.tax);
                    item.afterTaxBonus = Number.parseFloat(item.afterTaxBonus);
                    item.zhushu = Number.parseInt(item.zhushu);
                    delete item.bet;
                    this.formItem = item
                }

            }
        },
        methods: {
            handleSubmit (name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        let url = this.edit ? this.url + '/' + this.formItem.id : this.url;
                        let request = this.edit ? this.$messageHttp.put : this.$messageHttp.post;
                        let tempItem = this.transform();
                        request(url, tempItem,
                            () => {
                                if (this.edit) {
                                    this.$emit('updated', {...this.item, ...tempItem})
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
            transform() {
                let tempItem = {...this.formItem};
                tempItem.total_money = tempItem.total_money.toFixed(2);
                tempItem.bonus = tempItem.bonus.toFixed(2);
                tempItem.tax = tempItem.tax.toFixed(2);
                tempItem.afterTaxBonus = tempItem.afterTaxBonus.toFixed(2);
                return tempItem;
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

