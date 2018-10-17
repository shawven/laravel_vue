<template>
    <div>
        <list-table ref="listTable" :url="url" :columns="columns"
                    :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
            <div slot="search-block">
                <Row>
                    <Col span="4">
                        <FormItem label="用户ID">
                            <Input v-model="searchItem.user_id" clearable/>
                        </FormItem>
                    </Col>
                    <Col span="4">
                        <FormItem label="用户昵称">
                            <Input v-model="searchItem.jwl.u.usernick" clearable/>
                        </FormItem>
                    </Col>
                    <Col span="4">
                        <FormItem label="订单ID">
                            <Input v-model="searchItem.id" clearable/>
                        </FormItem>
                    </Col>
                    <Col span="4">
                        <FormItem label="订单编号">
                            <Input v-model="searchItem.order_id" clearable/>
                        </FormItem>
                    </Col>
                    <Col span="4">
                        <FormItem label="订单时间">
                            <DatePicker type="daterange" split-panels v-model="searchItem.r.addtime" />
                        </FormItem>
                    </Col>
                    <Col span="4">
                        <FormItem label="渠道">
                            <Select v-model="searchItem.i.channel" multiple style="width:200px" label-in-value>
                                <Option v-for="item in channels" :label="item.name" :value="item.mark" :key="item.mark">
                                    {{item.name}}
                                </Option>
                            </Select>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="4">
                        <FormItem label="彩种" prop="type">
                            <RadioGroup v-model="searchItem.type" type="button">
                                <Radio v-for="(name, type) in orderType" :label="type" :key="type">{{name}}</Radio>
                            </RadioGroup>
                        </FormItem>
                    </Col>
                    <Col span="5">
                        <FormItem label="支付方式" prop="type">
                            <RadioGroup v-model="searchItem.payway" type="button">
                                <Radio v-for="(text, name) in tagItem.payWayText" :label="name" :key="name"
                                       @click.native="searchItem.payway === name ? searchItem.payway = '' : ''">
                                    {{text}}
                                </Radio>
                            </RadioGroup>
                        </FormItem>
                    </Col>
                    <Col span="5">
                        <FormItem label="支付状态" prop="pay_type">
                            <RadioGroup v-model="searchItem.pay_type" type="button">
                                <Radio v-for="(text, index) in tagItem.payTypeText" v-if="text" :label="index" :key="index"
                                       @click.native="searchItem.pay_type === index ? searchItem.pay_type = '' : ''">
                                    {{text}}
                                </Radio>
                            </RadioGroup>
                        </FormItem>
                    </Col>
                    <Col span="5" v-show="showToDraw">
                        <FormItem label="出票状态" prop="to_draw">
                            <RadioGroup v-model="searchItem.to_draw" type="button">
                                <Radio v-for="(text, index) in tagItem.drawText" v-if="text" :label="index" :key="index"
                                       @click.native="searchItem.to_draw === index ? searchItem.to_draw = '' : ''">
                                    {{text}}
                                </Radio>
                            </RadioGroup>
                        </FormItem>
                    </Col>
                    <Col span="5" v-show="showDrawState">
                        <FormItem label="开奖状态" prop="state" :disabled="+searchItem.pay_type === 3">
                            <RadioGroup v-model="searchItem.state" type="button">
                                <Radio v-for="(text, index) in tagItem.stateText" v-if="text" :label="index" :key="index"
                                       @click.native="searchItem.state === index ? searchItem.state = '' : ''">
                                    {{text}}
                                </Radio>
                            </RadioGroup>
                        </FormItem>
                    </Col>
                </Row>
            </div>
            <div slot="button-block">
                <auth-button name="order_create" @click="creatorModal = true" />
                <auth-button name="order_update" @click="editorModal = selectOne()"/>
                <auth-button name="order_export" @click="exportData"/>
            </div>
            <div slot="select-item-block" slot-scope="{item}">
                <editor :url="url" :edit="false" :tag-item="tagItem" v-model="creatorModal" title="新增订单信息" @created="created" />
                <editor :url="url" :item="item" :tag-item="tagItem" v-model="editorModal" title="编辑订单信息" @updated="updated"/>
            </div>
        </list-table>
    </div>
</template>
<script>
    import editor from './editor';
    import bet from './bet/index';
    import orderStatus from './order-status'
    import dateUtil from '@/libs/dateUtil'

    export default {
        name: 'order-index',
        components: { editor, bet, orderStatus},
        props: {
            userId: {
                type: null,
                default: null,
            }
        },
        data() {
            let searchObj = {
                user_id: '',
                id: '',
                order_id: '',
                type: '',
                state: '',
                pay_type: '',
                to_draw: '',
                i: {channel: []},
                r: {addtime: []},
                j: {u: 'id,userName,usernick,real_name,userPhone,real_phone,real_card,isRealAttestation,avatar' +
                        ',wallet,balance,handsel,draw_balance'
                },
                jo: {u:'l,id,user_id'},
                ja: {u:'userInfo'},
                jwl: {u:{usernick: ''}}
            };
            return {
                creatorModal: false,
                editorModal: false,
                url: 'api/users/orders',
                channels: this.$store.getters.userChannels,
                searchItem: {...this.deepCopy(searchObj), user_id: this.userId, pay_type: 2},
                localItem: this.deepCopy(searchObj),
                orderType:{
                    jclq: '竞彩篮球',
                    jczq: '竞彩足球'
                },
                showToDraw: true,
                showDrawState: true,
                tagItem: {
                    payWayText: {WALLET: '钱包', WXPAY:'微信', ALIPAY:'支付宝', caijin: '彩金'},
                    payWayColor: {WALLET: 'Tomato', WXPAY: 'LimeGreen', ALIPAY:'primary', caijin: 'GoldenRod'},
                    stateText: ['', '开奖中', '未中奖', '已中奖'],
                    stateColor: ['', 'gray', 'default', 'success'],
                    drawText: ['出票中', '', '出票失败', '出票成功'],
                    drawColor: ['gray', '', 'default', 'success'],
                    payTypeText: ['支付中', '支付失败', '支付成功'],
                    payTypeColor: ['gray', 'default', 'success'],
                },
                columns: [
                    {
                        type: 'selection',
                        width: 60,
                        align: 'center'
                    },
                    {
                        title: 'ID',
                        key: 'id',
                        width: 100,
                        sortable: 'custom',
                        render: (h, params) => h(bet, {props: {title: '投注详情', item: params.row}})
                    },
                    {
                        title: '用户昵称',
                        key: 'userInfo',
                        width: 150,
                        render: (h, params) => h('user-info', {props: {item: params.row.userInfo}})
                    },
                    {
                        title: '彩种',
                        key: 'type',
                        width: 100,
                        render: (h, params) => h('span',this.orderType[params.row.type])
                    },
                    {
                        title: '玩法',
                        width: 100,
                        key: 'play_method',
                        render: (h, params) => {
                            let arr = [];
                            let str = '';
                            params.row.play_method.split(',').forEach((item) => {
                                arr = item.split(':');
                                str +=  arr[1] + '注 ' + arr[0] + '串1,';
                            });

                            return h('span', str.replace(/,$/, ''))
                        }
                    },
                    {
                        title: '倍数',
                        width: 70,
                        key: 'beishu',
                    },
                    {
                        title: '订单状态',
                        width: 390,
                        render: (h, params) => h(orderStatus, {props:{item: params.row}})
                    },
                    {
                        title: '订单金额',
                        key: 'total_money',
                        width: 110,
                        sortable: 'custom',
                        render: (h, params) => h('span', params.row.total_money + ' ￥')
                    },
                    {
                        title: '支付方式',
                        key: 'payway',
                        width: 100,
                        render: (h, params) => h('Tag', {
                            props: {color: this.tagItem.payWayColor[params.row.payway]}
                        }, this.tagItem.payWayText[params.row.payway])
                    },
                    {
                        title: '渠道',
                        key: 'bonus',
                        width: 100,
                        render: (h, params) => {
                            let channel = this.channels.find((channel) => {
                                return channel.mark === params.row.channel
                            });
                            if (channel) {
                                return h('Tag', channel.name)
                            }
                        }
                    },
                    {
                        title: '下单时间',
                        key: 'addtime',
                        width: 170,
                        sortable: 'custom'
                    },
                    {
                        title: '更新时间',
                        key: 'updatetime',
                        width: 170,
                        sortable: 'custom'
                    }
                ]
            };
        },
        watch: {
            userId (value) {
                this.searchItem.user_id = value;
                this.$refs.listTable.loadList();
            },
            'searchItem.pay_type'(value) {
                if (+value === 2) {
                    this.showToDraw = true;
                    this.showDrawState = true;
                } else {
                    this.showToDraw = false;
                    this.showDrawState = false;
                    this.searchItem.to_draw = '';
                    this.searchItem.state = '';
                }
            }
        },
        methods: {
            exportData () {
                let columns = [
                    {
                        title: 'ID',
                        key: 'id',
                    },
                    {
                        title: '订单编号',
                        key: 'order_id',
                    },
                    {
                        title: '用户昵称',
                        key: 'nickname',
                    },
                    {
                        title: '彩种',
                        key: 'type',
                    },
                    {
                        title: '玩法',
                        key: 'play_method',
                    },
                    {
                        title: '倍数',
                        key: 'beishu',
                    },
                    {
                        title: '支付方式',
                        key: 'payway',
                    },
                    {
                        title: '支付状态',
                        key: 'pay_type',
                    },
                    {
                        title: '出票状态',
                        key: 'to_draw',
                    },
                    {
                        title: '开奖状态',
                        key: 'state',
                    },
                    {
                        title: '订单金额',
                        key: 'total_money',
                    },
                    {
                        title: '预计奖金',
                        key: 'bonus',
                    },
                    {
                        title: '渠道',
                        key: 'channel',
                    },
                    {
                        title: '下单时间',
                        width: 300,
                        key: 'addtime',
                    }
                ];

                let data = this.deepCopy(this.$refs.listTable.getList()).map((row) => {
                    row.nickname = row.userInfo.usernick;
                    row.type = this.orderType[row.type];
                    let arr = row.play_method.split(':');
                    row.play_method = arr[0] + '串' + arr[1];
                    row.payway = this.tagItem.payWayText[row.payway];
                    row.pay_type = this.tagItem.payTypeText[row.pay_type];
                    row.to_draw = this.tagItem.drawText[row.to_draw];
                    row.state = this.tagItem.stateText[row.state];
                    let channel = this.channels.find((channel) => channel.mark === row.channel);
                    row.channel = channel ? channel.name: '';
                    return row;
                });

                this.$Modal.info({
                    width: 300,
                    title: '数据导出提示',
                    content: '仅导出当前浏览的表格数据',
                    closable: true,
                    onOk: () => {
                        this.$refs.listTable.exportCsv({
                            filename: '用户订单' + dateUtil.formatDate(new Date) ,
                            original: false,
                            columns: columns,
                            data: data
                        });
                    }
                });
            },
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
            },
            console(value) {
                console.log(value);
            }
        }
    };
</script>
