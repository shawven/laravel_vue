<template>
    <div>
        <list-table ref="listTable" :url="url" :columns="columns" @complete="getAllAuthorities"
                    :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
            <Row slot="search-block">
                <Col span="4">
                    <FormItem label="角色ID">
                        <Input v-model="searchItem.id" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="角色名">
                        <Input v-model="searchItem.name" clearable/>
                    </FormItem>
                </Col>
                <Col span="6">
                    <FormItem label="创建时间">
                        <DatePicker type="datetimerange" split-panels class="w-100"
                                    v-model="searchItem.r.create_time" ></DatePicker>
                    </FormItem>
                </Col>
            </Row>
            <div slot="button-block">
                <auth-button name="role_create" @click="creatorModal = true"/>
                <auth-button name="role_update" @click="editorModal = selectOne()"/>
                <auth-button name="role_delete" @click="openDelConfirm"/>
            </div>
            <div slot="select-item-block" slot-scope="{item}">
                <editor :edit="false" :url="url" v-model="creatorModal" :all-authorities="allAuthorities"
                        title="添加角色" @created="created" ></editor>
                <editor :item="item" :url="url" v-model="editorModal" :all-authorities="allAuthorities"
                        title="编辑角色" @updated="updated"></editor>
            </div>
        </list-table>
    </div>
</template>
<script>
    import editor from './editor';

    export default {
        name: 'role-index',
        components: { editor},
        data() {
            let searchObj = {
                id: '',
                name: '',
                r: {create_time: []}
            };
            return {
                url: '/api/roles',
                creatorModal: false,
                editorModal: false,
                allAuthorities: [],
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
                        title: '角色名称',
                        key: 'name',
                    },
                    {
                        title: '角色描述',
                        key: 'desc',
                    },
                    {
                        title: '创建时间',
                        key: 'create_time',
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
                let findRow = list.findIndex((item) => item.id === row.id);
                list.splice(findRow, 1, row)
            },
            openDelConfirm () {
                if (this.$refs.listTable.selectMulti()) {
                    let ids = this.$refs.listTable.getSelectIds().join(',');
                    this.$modalHttp.delete(this.url + '/' + ids, null, null,
                        () => {
                            this.$Message.success('删除成功');
                            let list = this.$refs.listTable.getList();
                            ids.split(',').forEach((id) => {
                                let index = list.findIndex((item) => Number(item.id) === Number(id));
                                list.splice(index, 1)
                            })
                        }
                    )
                }
            },
            getAllAuthorities() {
                this.$http.get('/api/authorities')
                    .then((result) => this.allAuthorities = result.data.data.list)
                    .catch((error) => this.$http.handler.handleError(error))
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
