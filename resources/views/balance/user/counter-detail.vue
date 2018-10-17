<template>
    <div>
        <Row v-for="(text, index) in tagItem.typeText"
             v-if="index > 0 && countData[index] && +countData[index].total > 0"
             :key="index" :gutter="16" type="flex" justify="center" align="middle">
            <Col span="4">
                <Tag :color="tagItem.typeColor[index]" type="dot">{{text}}</Tag>
            </Col>
            <Col span="20">
                <Row type="flex" align="middle" justify="center">
                    <Col span="6">
                        <Tag color="primary" type="border">合计</Tag>
                        <span class="text-dark font-sm">
                        {{getMoney(countData[index].total)}}
                    </span>
                    </Col>
                    <Col span="18">
                        <template v-if="index === 3">
                            <Row v-for="(child, payWayKey) in countData[index].parts" v-if="+child.total > 0" :key="payWayKey"
                                 type="flex" align="middle" justify="center">
                                <Col span="6">
                                    <Tag :color="tagItem.payWayColor[payWayKey]">{{tagItem.payWayText[payWayKey]}}</Tag>
                                    <span class="text-dark font-sm">
                                    {{getMoney(child.total)}}
                                </span>
                                </Col>
                                <Col span="18">
                                    <Col span="12" v-for="(value, partKey) in child.parts" v-if="+value > 0" :key="'' + payWayKey + partKey">
                                        <Tag type="border">{{tagItem.partText[partKey]}}</Tag>
                                        <span class="text-dark font-sm">{{getMoney(value)}}</span>
                                    </Col>
                                </Col>
                            </Row>
                        </template>
                        <template v-else>
                            <Col span="6" v-for="(value, key) in countData[index].parts" v-if="+value > 0"
                                 :key="'' + index + key">
                                <template v-if="!(key === 'WALLET' && index === 4)">
                                    <Tag :color="tagItem.payWayColor[key]">{{tagItem.payWayText[key]}}</Tag>
                                    <span class="text-dark font-sm">{{getMoney(value)}}</span>
                                </template>
                            </Col>
                        </template>
                    </Col>
                </Row>
            </Col>
        </Row>
    </div>
</template>

<script>
    export default {
        name: "counter-detail",
        props: {
            data: {
                type: Object,
                default() {
                    return {}
                }
            }
        },
        data() {
            return {
                visible: this.value,
                tagItem: {
                    payWayText: {WALLET: '钱包', WXPAY:'微信', ALIPAY:'支付宝', caijin: '彩金'},
                    payWayColor: {WALLET: 'Tomato', WXPAY: 'LimeGreen', ALIPAY:'primary', caijin: 'GoldenRod'},
                    typeText: ['', '返奖', '提现', '购彩', '充值'],
                    typeColor: ['', 'success', 'GoldenRod', 'primary', 'LightCoral'],
                    partText: {handsel: '彩金余额', draw_money: '提现余额'},
                }
            }
        },
        computed: {
            countData() {
                return this.data;
            },
        },
        methods: {
            deepCopy(obj) {
                return JSON.parse(JSON.stringify(obj))
            },
            getMoney(val) {
                let money = Number.parseFloat(val);
                money = Number.isNaN(money) ? 0.00 : money;
                return money.toFixed(2) + ' ￥';
            },
        }
    }
</script>

