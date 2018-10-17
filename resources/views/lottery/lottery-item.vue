<template>
    <div>
        <p class="font-sm font-weight-bold">{{date}}</p>
        <Card class="mt-2">
            <Row type="flex" justify="start" align="middle" :gutter="16">
                <Col span="8">
                    <template v-if="item.lottery.percent === 100">
                        <Col span="4">已完结</col><Col span="20"><Progress :percent="100"/></Col>
                    </template>
                    <template v-else-if="item.lottery.percent > 0">
                        <Col span="4">已结算</Col><Col span="20"><Progress :percent="item.lottery.percent"  status="active"/></Col>
                    </template>
                    <template v-else>
                        <Col span="4">无订单</Col><Col span="20"><Progress :percent="0" status="wrong"/></Col>
                    </template>
                </Col>
                <Col span="8">
                    <Row>
                        <Col span="8"><Tag color="primary">总订单数</Tag> {{item.lottery.total}}</Col>
                        <Col span="8"><Tag color="success">已发放数</Tag> {{item.lottery.settled}}</Col>
                        <Col span="8"><Tag color="warning">待发放数</Tag> {{item.lottery.total - item.lottery.settled}}</Col>
                    </Row>
                </Col>
                <Col span="8">
                    <Row>
                        <Col class="text-center font-lg" :style="{cursor: 'pointer'}">
                            <Icon class="w-100" :type="expandType" @click.native="defaultDisplay = !defaultDisplay"></Icon>
                        </Col>
                    </Row>
                </Col>
            </Row>
            <Row class="mt-3">
                <transition name="transition-drop">
                    <Tabs type="card" v-show="defaultDisplay">
                        <TabPane label="本期赛事" >
                            <Table v-if="item.items.length" :data="item.items" :columns="itemColumns"></Table>
                        </TabPane>
                        <TabPane label="中奖订单" v-if="item.lottery.total">
                            <Table v-if="item.orders.length" :data="item.orders" :columns="orderColumns"></Table>
                        </TabPane>
                        <TabPane label="中奖用户"  v-if="item.lottery.total">
                            <Table v-if="item.users.length" :data="item.users" :columns="userColumns"></Table>
                        </TabPane>
                    </Tabs>
                </transition>
            </Row>
        </Card>
        <lotterySettlement v-model="settlementModal" :item="settleItem" :type="type"/>
    </div>
</template>

<script>
    import basketballOdds from '@/views/match/basketball/odds'
    import footballOdds from '@/views/match/football/odds'
    import lotterySettlement from './lottery-settlement'

    export default {
        name: "lottery-item",
        components: {lotterySettlement},
        props: {
            date: String,
            type: String,
            item: {
                type: Object,
                default() {
                    return {}
                }
            }
        },
        data() {
            return {
                buttonLoading: false,
                defaultDisplay: false,
                settlementModal: false,
                settleItem: {},
                orderColumns: [
                    {
                        title: 'ID',
                        key: 'id',
                        sortable: 'custom'
                    },
                    {
                        title: '订单编号',
                        key: 'order_id',
                        sortable: 'custom'
                    },
                    {
                        title: '用户昵称',
                        render: (h, params) => h('user-info', {props: {item: params.row.userInfo}})
                    },
                    {
                        title: '过关方式',
                        key: 'guoguan',
                    },
                    {
                        title: '玩法',
                        key: 'play_method',
                    },
                    {
                        title: '倍数',
                        key: 'beishu',
                        sortable: 'custom'
                    },
                    {
                        title: '发奖状态',
                        key: 'winning',
                        sortable: 'custom',
                        render: (h, params) => {
                            let tagItem = {
                                drawText: ['未发放', '已发放'],
                                drawColor: ['warning', 'success']
                            };
                            return h('Tag', {
                                props: {color: tagItem.drawColor[+params.row.settled]}
                            }, tagItem.drawText[+params.row.settled])
                        }
                    },
                    {
                        title: '订单金额',
                        key: 'total_money',
                        sortable: 'custom',
                    },
                    {
                        title: '预计奖金',
                        key: 'bonus',
                        sortable: 'custom',
                    },
                    {
                        title: '下单时间',
                        key: 'addtime',
                        sortable: 'custom'
                    },
                    {
                        title: '操作',
                        render: (h, params) => {
                            if (!params.row.settled) {
                                return h('Button', {
                                    props: {
                                        type: 'primary',
                                        icon: 'ios-paw',
                                        size: 'small'
                                    },
                                    on: {
                                        click: () => this.showSettlement(params)
                                    }
                                }, '派奖')
                            }
                        }
                    }
                ],
                userColumns: [
                    {
                        title: 'ID',
                        key: 'id',
                        sortable: 'custom'
                    },
                    {
                        title: '用户昵称',
                        render: (h, params) => h('user-info', {props: {item: params.row}})
                    },
                    {
                        title: '手机号码',
                        key: 'userPhone'
                    },
                    {
                        title: '钱包',
                        key: 'wallet',
                        render: (h, params) => h('span', params.row.wallet + ' ￥')

                    },
                    {
                        title: '余额',
                        key: 'balance',
                        render: (h, params) => h('span', params.row.balance + ' ￥')
                    },
                    {
                        title: '彩金',
                        key: 'handsel',
                        render: (h, params) => h('span', params.row.handsel + ' ￥')
                    },
                    {
                        title: '渠道',
                        key: 'bonus',
                        width: 100,
                        render: (h, params) => {
                            let channel = this.$store.getters.userChannels.find((channel) => {
                                return channel.mark === params.row.channel
                            });
                            if (channel) {
                                return h('Tag', channel.name)
                            }
                        }
                    },
                    {
                        title: '中奖订单',
                        type: 'expand',
                        align: 'center',
                        width: 150,
                        render: (h, params) => {
                            return h('Table', {
                                props: {
                                    data: params.row.orders,
                                    columns: this.orderColumns
                                }
                            })
                        }
                    }
                ],
                basketballColumns: [
                    {
                        title: 'ID',
                        key: 'id',
                        sortable: 'custom'
                    },
                    {
                        title: '期号',
                        key: 'bind',
                        sortable: 'custom'
                    },
                    {
                        title: '联赛',
                        key: 'lg',
                        sortable: 'custom'
                    },
                    {
                        title: '主场',
                        key: 'homesxname'
                    },
                    {
                        title: '客场',
                        key: 'awaysxname'
                    },
                    {
                        title: '日期',
                        key: 'date',
                        sortable: 'custom'
                    },
                    {
                        title: '详情',
                        type: 'expand',
                        align: 'center',
                        width: 80,
                        render: (h, params) => {
                            return h(basketballOdds, {
                                props: {
                                    row: params.row
                                }
                            },)
                        }
                    }
                ],
                footballColumns: [
                    {
                        title: 'ID',
                        key: 'id',
                        sortable: 'custom'
                    },
                    {
                        title: '期号',
                        key: 'bind',
                        sortable: 'custom'
                    },
                    {
                        title: '联赛',
                        key: 'lg',
                        sortable: 'custom'
                    },
                    {
                        title: '主场',
                        key: 'homesxname'
                    },
                    {
                        title: '客场',
                        key: 'awaysxname'
                    },

                    {
                        title: '日期',
                        key: 'date',
                        sortable: 'custom'
                    },
                    {
                        title: '详情',
                        type: 'expand',
                        align: 'center',
                        width: 80,
                        render: (h, params) => {
                            return h(footballOdds, {
                                props: {
                                    row: params.row
                                }
                            },)
                        }
                    },
                ]
            }
        },
        computed: {
            itemColumns() {
                if (this.type === 'jclq') {
                    return this.basketballColumns;
                }
                if (this.type === 'jczq') {
                    return this.footballColumns;
                }
            },
            expandType() {
                return this.defaultDisplay ? 'ios-arrow-up' : 'ios-arrow-down'
            }
        },
        methods: {
            showSettlement(params) {
                this.settleItem = params.row;
                this.settlementModal = true;
            }
        }
    }
</script>
