<template>
    <div>
        <list-table ref="listTable" :url="url" :columns="columns"
                    :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
            <div slot="search-block">
                <Row>
                    <Col span="4">
                        <FormItem label="用户ID">
                            <Input v-model="searchItem.jw.u.id" clearable/>
                        </FormItem>
                    </Col>
                    <Col span="4">
                        <FormItem label="用户昵称">
                            <Input v-model="searchItem.jwl.ub.user_nick" clearable/>
                        </FormItem>
                    </Col>
                    <Col span="4">
                        <FormItem label="银行名称">
                            <Input v-model="searchItem.jwl.ub.bank_name"/>
                        </FormItem>
                    </Col>
                    <Col span="4">
                        <FormItem label="提现时间">
                            <DatePicker type="daterange" split-panels v-model="searchItem.r.addtime"></DatePicker>
                        </FormItem>
                    </Col>
                    <Col span="6">
                        <FormItem label="提现状态" prop="mode">
                            <RadioGroup v-model="searchItem.state" type="button">
                                <Radio v-for="(text, index) in stateText" :label="index" :key="index"
                                       @click.native="searchItem.state === index ? searchItem.state = null : ''">{{text}}</Radio>
                            </RadioGroup>
                        </FormItem>
                    </Col>
                </Row>
            </div>
            <withdraw-confirm v-model="withdrawModal" :item="selectItem"  @updated="update"/>
        </list-table>
    </div>
</template>

<script>
    import auditConfirm from './audit-confirm'
    import withdrawConfirm from './withdraw-confirm'

    export default {
        name: 'withdraw-index',
        components: { auditConfirm, withdrawConfirm},
        data() {
            let searchObj = {
                j: {
                    u: 'id,userName,usernick,real_name,userPhone,real_phone,real_card,isRealAttestation,avatar' +
                    ',wallet,balance,handsel,draw_balance',
                    ub:'user_nick,bank_name,bank_card'
                },
                jo: {u:'id,user_id', ub:'l,id,bank_id'},
                ja: {u:'userInfo', ub:'bankInfo'},
                jw: {u: {id: ''}},
                jwl: {ub: {user_nick: '', bank_name: ''}},
                r: {addtime: []},
            };

            let data = {
                url: '/api/users/withdraws',
                selectItem: {userInfo: {}, bankInfo: {}},
                rowIndex: null,
                searchItem: this.deepCopy(searchObj),
                localItem: this.deepCopy(searchObj),
                withdrawModal: false,
                loading: true,
                stateText: ['提现中', '成功', '失败'],
                stateColor: ['gray', 'success', 'default'],
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
                        title: '开户名',
                        render: (h, params) => h('span', params.row.bankInfo.user_nick)
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
                {
                    name:'withdraws_update',
                    click: this.openWithdrawConfirm,
                    disabled: params => params.row.state !== 0
                }
            ]);

            if (operator) data.columns.push(operator);

            return data
        },
        methods: {
            openWithdrawConfirm(params) {
                this.withdrawModal = true;
                this.rowIndex = params.index;
                this.selectItem = {...params.row};
            },
            update(updateItem) {
                this.$refs.listTable.getList().splice(this.rowIndex, 1, updateItem)
            },
            deepCopy(obj) {
                return JSON.parse(JSON.stringify(obj))
            }
        }
    };
</script>

