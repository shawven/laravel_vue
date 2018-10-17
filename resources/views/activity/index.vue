<template>
    <list-table ref="listTable" :url="url" :columns="columns"
                :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
        <div slot="search-block">
            <Row>
                <Col span="4">
                    <FormItem label="活动ID">
                        <Input v-model="searchItem.id" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="活动名称">
                        <Input v-model="searchItem.name" clearable/>
                    </FormItem>
                </Col>
            </Row>
        </div>
    </list-table>
</template>

<script>
    export default {
        name: "activity-index",
        data() {
            let searchObj = {id: '', name: ''};
            return {
                url: '/api/activities',
                creatorModal: false,
                editorModal: false,
                searchItem: {...this.deepCopy(searchObj)},
                localItem: this.deepCopy(searchObj),
                stateText: ['否', '是'],
                stateColor: ['default', 'green'],
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
                        title: '活动名称',
                        key: 'name',
                    },
                    {
                        title: '活动描述',
                        key: 'desc',
                    },
                    {
                        title: '可参加次数',
                        key: 'number',
                        render: (h, params) => {
                            return h('Tag', {
                                props: {color: 'primary'}
                            }, params.row.number ? params.row.number + ' 次' : '无限')
                        }
                    },
                    {
                        title: '长期活动',
                        key: 'forever',
                        render: (h, params) => h('Tag', {
                            props: {color: this.stateColor[params.row.forever]}
                        }, this.stateText[params.row.forever])
                    },
                    {
                        title: '是否有效',
                        key: 'state',
                        render: (h, params) => h('Tag', {
                            props: {color: this.stateColor[params.row.state]}
                        }, this.stateText[params.row.state])
                    },
                    {
                        title: '活动金额',
                        key: 'amount',
                        render: (h, params) => h('span', params.row.amount + ' ￥')
                    },
                    {
                        title: '开始时间',
                        key: 'start_time',
                        sortable: 'custom'
                    },
                    {
                        title: '结束时间',
                        key: 'end_time',
                        sortable: 'custom'
                    },
                    {
                        title: '创建时间',
                        key: 'create_time',
                        sortable: 'custom'
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
            }
        }
    }
</script>

<style scoped>

</style>
