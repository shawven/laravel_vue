<template>
    <div>
        <Table :data="row.parts" :columns="columns"></Table>
        <counter-detail :data="row.details.count_data" class="mt-3"></counter-detail>
    </div>
</template>

<script>
    import counterDetail from './counter-detail';
    export default {
        name: "detail",
        components: {counterDetail},
        props:{
            row: {
                type: Object,
                default() {
                    return {}
                }
            }
        },
        data() {
            return {
                typeText: ['', '充值', '购彩', '返奖', '提现'],
                typeColor: ['', 'LightCoral', 'primary', 'green', 'GoldenRod'],
                columns: [
                    {
                        title: '类型',
                        key: 'type',
                        render: (h, params) => h('Tag',
                            {props: {color: this.typeColor[params.row.type]}},
                            this.typeText[params.row.type]
                        )
                    },
                    {
                        title: '总余额',
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
                    }
                ]
            }

        },
        methods: {
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

