<template>
    <list-table ref="listTable" :url="url" :columns="columns"
                :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
        <div slot="search-block">
            <Row>
                <Col span="4">
                    <FormItem label="名称">
                        <Input v-model="searchItem.name" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="标示">
                        <Input v-model="searchItem.mark" clearable/>
                    </FormItem>
                </Col>
            </Row>
        </div>
        <div slot="button-block">
            <auth-button name="user_channel_create" @click="creatorModal = true"/>
            <auth-button name="user_channel_update" @click="editorModal = selectOne()" />
        </div>
        <div slot="select-item-block" slot-scope="{item}">
            <editor :url="url" :edit="false" v-model="creatorModal" title="添加渠道"  @created="created"/>
            <editor :url="url" :item="item" v-model="editorModal" title="编辑渠道"  @updated="created"/>
        </div>
    </list-table>
</template>

<script>
    import editor from './editor'

    export default {
        components: { editor},
        name: "user-channel-index",
        data() {
            let searchObj = {name: '', mark: ''};
            return {
                url: '/api/users/channels',
                creatorModal: false,
                editorModal: false,
                searchItem: {...this.deepCopy(searchObj)},
                localItem: this.deepCopy(searchObj),
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
                        title: '标示',
                        key: 'mark',
                    },
                    {
                        title: '名称',
                        key: 'name',
                    },
                    {
                        title: '推广链接',
                        key: 'app_promotion_url',
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
