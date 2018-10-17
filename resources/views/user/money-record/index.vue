<template>
    <list-table ref="listTable" :url="url" :columns="columns"
               :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
        <Row slot="search-block">
            <Col span="4">
                <FormItem label="用户ID">
                    <Input v-model="searchItem.jw.u.id" clearable/>
                </FormItem>
            </Col>
            <Col span="4">
                <FormItem label="用户昵称">
                    <Input v-model="searchItem.jwl.u.usernick" clearable/>
                </FormItem>
            </Col>
            <Col span="4">
                <FormItem label="日期">
                    <DatePicker type="daterange" split-panels v-model="searchItem.r.addtime"/>
                </FormItem>
            </Col>
            <Col span="5">
                <FormItem label="支付方式" prop="mode">
                    <RadioGroup v-model="searchItem.mode" type="button">
                        <Radio v-for="(text, name) in tagItem.payWayText" :label="name" :key="name"
                               @click.native="searchItem.mode === name ? searchItem.mode = '' : ''">{{text}}</Radio>
                    </RadioGroup>
                </FormItem>
            </Col>
            <Col span="5">
                <FormItem label="类型" prop="type">
                    <RadioGroup v-model="searchItem.type" type="button">
                        <Radio v-for="(text, index) in tagItem.typeText" v-if="text" :label="index" :key="index"
                               @click.native="searchItem.type === index ? searchItem.type = '' : ''">{{text}}</Radio>
                    </RadioGroup>
                </FormItem>
            </Col>
        </Row>
    </list-table>
</template>

<script>
    export default {
        name: 'money-record-index',
        data() {
            let searchObj = {
                mode: '',
                type: '',
                r: {addtime: []},
                j: {
                    u: 'id,userName,usernick,real_name,userPhone,real_phone,real_card,isRealAttestation,avatar' +
                    ',wallet,balance,handsel,draw_balance',
                },
                jo: {u:'id,userId'},
                ja: {u:'userInfo'},
                jw: {u: {id: ''}},
                jwl: {u:{usernick: '', }}
            };

            return {
                url: '/api/users/money_records',
                searchItem: this.deepCopy(searchObj),
                localItem: this.deepCopy(searchObj),
                counterModal: false,
                tagItem: {
                    payWayText: {WALLET: '钱包', WXPAY:'微信', ALIPAY:'支付宝', caijin: '彩金'},
                    payWayColor: {WALLET: 'Tomato', WXPAY: 'LimeGreen', ALIPAY:'primary', caijin: 'GoldenRod'},
                    typeText: ['', '奖金进账', '提现', '购彩', '充值'],
                    typeColor: ['', 'success', 'GoldenRod', 'primary', 'LightCoral'],
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
                        title: '用户昵称',
                        key: 'userInfo',
                        width: 150,
                        render: (h, params) => h('user-info', {props: {item: params.row.userInfo}})
                    },
                    {
                        title: '类型',
                        key: 'type',
                        sortable: 'custom',
                        render: (h, params) => {
                            return h('Tag', {
                                props: {color: this.tagItem.typeColor[params.row.type]}
                            }, this.tagItem.typeText[params.row.type])
                        }
                    },
                    {
                        title: '描述',
                        key: 'typename',
                    },
                    {
                        title: '支付方式',
                        key: 'mode',
                        render: (h, params) => {
                            if (params.row.mode) {
                                return h('Tag', {
                                    props: {color: this.tagItem.payWayColor[params.row.mode]}
                                }, this.tagItem.payWayText[params.row.mode])
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
        },
        methods: {
            created() {
                this.$refs.listTable.loadList();
            },
            updated(row) {
                let list = this.$refs.listTable.getList();
                list.splice(list.findIndex((item) => item.id === row.id), 1, row)
            },
            selectOne() {
                return this.$refs.listTable.selectOne()
            },
            deepCopy(obj) {
                return JSON.parse(JSON.stringify(obj))
            }
        }
    };
</script>

