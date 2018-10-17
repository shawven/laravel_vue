<template>
    <list-table ref="listTable" :url="url" :columns="columns"
                :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
        <div slot="search-block">
            <Row>
                <Col span="4">
                    <FormItem label="渠道名称">
                        <Input v-model="searchItem.jwl.uc.name" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="渠道标示">
                        <Input v-model="searchItem.jwl.uc.mark" clearable/>
                    </FormItem>
                </Col>
            </Row>
        </div>

        <div slot="button-block">
            <auth-button name="system_balance_create" @click="creatorModal = true"/>
        </div>
    </list-table>
</template>

<script>
    export default {
        name: "app-download-index",
        data() {
            let searchObj = {
                j: {uc: 'id,mark,name,app_promotion_url,create_time,update_time'},
                jo: {uc: 'l,id,channel_id'},
                jwl: {uc: {name: '', mark: ''}},
                ja: {uc: 'channelInfo'}
            };
            return {
                url: '/api/apps/downloads',
                searchItem: {...this.deepCopy(searchObj)},
                localItem: this.deepCopy(searchObj),
                creatorModal: false,
                editorModal: false,
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
                        title: 'ID',
                        width: 70,
                        key: 'id',
                        sortable: 'custom'
                    },
                    {
                        title: '推广链接',
                        key: 'host'
                    },
                    {
                        title: '渠道名称',
                        render: (h, params) => h('span', params.row.channelInfo.name)
                    },
                    {
                        title: '渠道标示',
                        render: (h, params) => h('span', params.row.channelInfo.mark)
                    },
                    {
                        title: 'IP',
                        key: 'ip',
                        sortable: 'custom',
                    },
                    {
                        title: '地址',
                        key: 'address',
                    },
                    {
                        title: '系统',
                        key: 'system',
                    },
                    {
                        title: '浏览器',
                        key: 'browser',
                    },
                    {
                        title: '记录时间',
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
