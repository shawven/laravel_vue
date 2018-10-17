<template>
    <div>
        <list-table ref="listTable" :url="url" :columns="columns"
                    :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
            <Row slot="search-block">
                <Col span="4">
                    <FormItem label="用户ID">
                        <Input v-model="searchItem.id" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="用户昵称">
                        <Input v-model="searchItem.l.usernick" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="手机号码">
                        <Input v-model="searchItem.l.userPhone" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="注册时间">
                        <DatePicker type="daterange" split-panels v-model="searchItem.r.addtime" />
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="渠道">
                        <Select v-model="searchItem.i.channel" multiple style="width:200px" label-in-value>
                            <Option v-for="item in channels" :label="item.name" :value="item.mark" :key="item.mark">{{item.name}}</Option>
                        </Select>
                    </FormItem>
                </Col>
            </Row>
            <div slot="button-block">
                <auth-button name="user_update" @click="editorModal = selectOne()"/>
                <auth-button name="user_orders" @click="redirectOrders" />
                <auth-button name="user_money_records" @click="moneyRecordModal = selectOne()" />
                <auth-button name="user_recharges" @click="rechargeModal = selectOne()" />
                <auth-button name="user_withdraws" @click="withdrawModal = selectOne()" />
                <auth-button name="user_banks" @click="bankModal = selectOne()"/>
            </div>
            <div slot="select-item-block" slot-scope="{item}">
                <editor :url="url" :user="item" v-model="editorModal" title="编辑用户信息" @updated="updated"/>
                <user-bank :url="url" :user="item" v-model="bankModal" title="银行卡列表"/>
                <user-money-record :url="url" :user="item" v-model="moneyRecordModal" title="资金明细"/>
                <user-recharge :url="url" :user="item" v-model="rechargeModal" title="充值记录"/>
                <user-withdraw :url="url" :user="item" v-model="withdrawModal" title="提现记录"/>
            </div>
        </list-table>
    </div>
</template>
<script>
    import editor from './editor';
    import userBank from './user-bank';
    import userMoneyRecord from './user-money-record';
    import userRecharge from './user-recharge';
    import userWithdraw from './user-withdraw';

    export default {
        name: 'user-index',
        components: { editor, userBank, userMoneyRecord, userRecharge, userWithdraw},
        data() {
            let searchObj = {
                id: '',
                l: {userName: '', usernick: '', userPhone: ''},
                i: {channel: ''},
                r: {addtime: []}
            };
            return {
                url: '/api/users',
                editorModal: false,
                bankModal: false,
                orderModal: false,
                moneyRecordModal: false,
                rechargeModal: false,
                withdrawModal: false,
                channels: this.$store.getters.userChannels,
                searchItem: this.deepCopy(searchObj),
                localItem: this.deepCopy(searchObj),
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
                        sortable: 'custom'
                    },
                    {
                        title: '用户名',
                        key: 'userName',
                        render: (h, params) => {
                            return h('div', [
                                h('Icon', {props: {type: 'person'}}),
                                h('span', ' ' + params.row.userName),
                            ])
                        }
                    },
                    {
                        title: '用户昵称',
                        key: 'usernick',
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
                            let channel = this.channels.find((channel) => {
                                return channel.mark === params.row.channel
                            });
                            if (channel) {
                                return h('Tag', channel.name)
                            }
                        }
                    },
                    {
                        title: '注册时间',
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
            redirectOrders(){
                if (this.selectOne()) {
                    this.$router.push({name: 'orders', params: {userId: this.$refs.listTable.getSelectId()}})
                }
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
