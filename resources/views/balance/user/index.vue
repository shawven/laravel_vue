<template>
    <list-table ref="listTable" :url="url" :columns="columns"
                :search-item="searchItem" @clear="searchItem = deepCopy(localItem)">
        <Row slot="search-block">
            <Col span="4">
                <FormItem label="日期">
                    <DatePicker type="date" v-model="searchItem.date" clearable/>
                </FormItem>
            </Col>
        </Row>
        <div slot="button-block">
            <auth-button name="user_money_records" @click="counterModal = true"/>
        </div>
        <counter v-model="counterModal"></counter>
    </list-table>
</template>

<script>
    import counter from './counter'
    import detail from './detail'

    export default {
        components: {detail, counter},
        name: "balance-user-index",
        data() {
            let searchObj = {
                date: ''
            };
            return {
                url: '/api/balance/user_records',
                counterModal: false,
                searchItem: this.deepCopy(searchObj),
                localItem: this.deepCopy(searchObj),
                columns: [
                    {
                        title: '日期',
                        key: 'date',
                        sortable: 'custom'
                    },
                    {
                        title: '钱包余额',
                        key: 'type',
                        sortable: 'custom',
                        render: (h, params) =>  this.getChangeTag(h, params.row.final_total, params.row.change_total)
                    },
                    {
                        title: '充值余额',
                        key: 'type',
                        sortable: 'custom',
                        render: (h, params) => this.getChangeTag(h, params.row.final_balance, params.row.change_balance)
                    },
                    {
                        title: '可提现余额',
                        key: 'type',
                        sortable: 'custom',
                        render: (h, params) => this.getChangeTag(h, params.row.final_draw_balance, params.row.change_draw_balance)
                    },
                    {
                        title: '彩金余额',
                        key: 'type',
                        sortable: 'custom',
                        render: (h, params) => this.getChangeTag(h, params.row.final_handsel, params.row.change_handsel)
                    },
                    {
                        title: '详情',
                        type: 'expand',
                        align: 'center',
                        width: 80,
                        render: (h, params) => {
                            return h(detail, {
                                props: {
                                    row: params.row
                                }
                            },)
                        }
                    }
                ]
            }
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
            },
            getChangeTag(h, final, change) {
                let minus = String(change).charAt(0) === '-';

                let children = [
                    h('span', {
                        style: {
                            marginRight: '5px'
                        }
                    }, final),
                ];

                if (change !== '0.00') {
                    children.push(
                        h('Icon', {
                            style: {
                                color: minus ? 'red' : 'green',
                                marginRight: '5px'
                            },
                            props: {
                                type: minus ? 'ios-arrow-thin-down' : 'ios-arrow-thin-up'
                            }
                        }),
                        h('span', {
                            style: {
                                color: minus ? 'red' : 'green'
                            }
                        }, minus ? change.replace(/^-/, '') : change)
                    )
                }

                return h('span',children);
            }
        }
    }
</script>

<style scoped>

</style>
