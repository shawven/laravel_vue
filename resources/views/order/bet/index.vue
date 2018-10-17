<template>
    <Poptip width="700" :title="title" @on-popper-show="showTip" @on-popper-hide="visible = false" placement="right">
        <Icon type="ios-print"/> <a>{{item.id}}</a>
        <Card :bordered="false" dis-hover class="order-bet-card" slot="content"
              :style="{maxHeight: '600px', overflowY: 'auto'}" >
            <div slot="title" class="d-flex align-items-center justify-content-between">
                <p>{{'订单ID：' + item.id}}</p>
                <Row class="w-100 font-weight-bold text-dark">
                    <Col>
                        {{matchName}}： <Icon :type="matchIcon"/>
                        {{item.zhushu}} 注 {{item.beishu}} 倍  （{{play}}）
                    </Col>
                </Row>
            </div>
            <Row>
                <Col span="12">
                    <Tag color="primary">支付金额</Tag><span class="font-weight-bold text-dark">
                    {{item.total_money}}
                </span>
                </Col>
                <Col span="12">
                    <Tag color="primary">支付方式</Tag>
                    <Tag type="border" :color="payWayColor[item.payway]">{{payWayText[item.payway]}}</Tag>
                </Col>
            </Row>
            <Row>
                <Col span="12">
                    <Tag color="primary">应交税额</Tag><span class="font-weight-bold text-dark"> {{item.tax}}</span>
                </Col>
                <Col span="12">
                    <Tag color="primary">税后奖金</Tag><span class="font-weight-bold text-dark"> {{item.afterTaxBonus}}</span>
                </Col>
            </Row>
            <bet-ticket :tickets="item.tickets" :summary="summary"/>
            <Row class="px-3">
                <Col span="22">
                    <component :is="component" :item="summary" class="text-center px-2 my-1"/>
                </Col>
            </Row>
            <loading v-show="loading"/>
        </Card>
    </Poptip>
</template>

<script>
    import betTicket from './bet-ticket';

    const basketballDetail = () => import('./bet-item/basketball') ;
    const footballDetail = () => import('./bet-item/football');

    export default {
        name: 'bet-index',
        components: {betTicket, basketballDetail, footballDetail},
        props: {
            title: String,
            item: {
                type: Object,
                default: {},
            }
        },
        data() {
            return {
                summary: {},
                component: '',
                loading: false,
                visible: false,
                payWayText: {WALLET: '钱包', WXPAY:'微信', ALIPAY:'支付宝', caijin: '彩金'},
                payWayColor: {WALLET: 'Tomato', WXPAY: 'LimeGreen', ALIPAY:'primary', caijin: 'GoldenRod'},
                names: {
                    jclq: '竞彩篮球',
                    jczq: '竞彩足球'
                },
                icons: {
                    jclq: 'ios-basketball',
                    jczq: 'ios-football'
                },
            }
        },
        computed: {
            matchName() {
                return this.names[this.item.type];
            },
            matchIcon() {
                return this.icons[this.item.type];
            },
            play() {
                let arr = [];
                let str = '';
                this.item.play_method.split(',').forEach((item) => {
                    arr = item.split(':');
                    str +=  arr[1] + '注 ' + arr[0] + '串1,';
                });

                return  str.replace(/,$/, '');
            },
        },
        methods: {
            showTip() {
                this.loading = true;
                this.$http.get(`/api/users/orders/${this.item.id}/summary`)
                    .then((result) => {
                        this.summary = result.data.data.success[0];
                        this.loading = false;
                    })
                    .catch((error) => {
                        this.$http.handler.handleError(error);
                        this.loading = false;
                    });

                switch (this.item.type) {
                    case 'jclq':
                        this.component = 'basketballDetail';
                        break;
                    case 'jczq':
                        this.component = 'footballDetail';
                        break;
                    default:
                }
            }
        }
    };
</script>

<style lang="less">
    .order-bet-card {
        max-height: 750px;
        overflow-y: auto;
        .ivu-card-body .ivu-row {
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
        }
    }
    .ivu-collapse>.ivu-collapse-item>.ivu-collapse-header {
        padding-left: 16px;
        color: #1c2438;
    }
</style>
