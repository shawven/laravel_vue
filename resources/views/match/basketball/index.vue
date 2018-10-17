<template>
    <div>
        <list-table ref="listTable" :url="url" :columns="columns" title="竞彩篮球列表" :sort="[{date: 'desc'}]"
                    :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
            <Row slot="search-block">
                <Col span="4">
                    <FormItem label="ID">
                        <Input v-model="searchItem.id" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="期号">
                        <Input v-model="searchItem.bind" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="联赛">
                        <Input v-model="searchItem.lg" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="客场">
                        <Input v-model="searchItem.awaysxname" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="主场">
                        <Input v-model="searchItem.homesxname" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="日期">
                        <DatePicker type="date" v-model="searchItem.date"></DatePicker>
                    </FormItem>
                </Col>
            </Row>
            <div  slot="button-block">
                <auth-button name="match_basketball_create" @click="creatorModal = true" />
                <auth-button name="match_basketball_update" @click="editorModal = selectOne()"/>
            </div>
            <div>
                <editor :url="url" :edit="false" v-model="creatorModal" title="新增篮球篮球"  @created="created"/>
            </div>
            <div slot="select-item-block" slot-scope="{item}">
                <editor :url="url" :item="item" v-model="editorModal" title="编辑篮球篮球" @updated="updated"/>
            </div>
        </list-table>
    </div>
</template>
<script>
    import odds from './odds';
    import editor from './editor';

    export default {
        name: 'basketball-index',
        components: { odds, editor},
        data() {
            let searchObj = {
                id: '',
                bind: '',
                homesxname: '',
                awaysxname: '',
                lg: '',
                date: '',
                endtime: '',
                deadline: '',
                j: {bbo: 'id,sf,rfsf,dxf,sfc_z,sfc_k'},
                jo: {bbo: 'l,match_id,id'},
                ja: {bbo: 'odds'}
            };
            return {
                url: '/api/matches/basketball',
                creatorModal: false,
                editorModal: false,
                searchItem: this.deepCopy(searchObj),
                localItem : this.deepCopy(searchObj),
                columns: [
                    {
                        type: 'selection',
                        width: 60,
                        align: 'center'
                    },
                    {
                        title: 'ID',
                        key: 'id',
                        sortable: 'custom'
                    },
                    {
                        title: '期号',
                        key: 'bind',
                        sortable: 'custom'
                    },
                    {
                        title: '联赛',
                        key: 'lg',
                        sortable: 'custom'
                    },
                    {
                        title: '主场',
                        key: 'homesxname'
                    },
                    {
                        title: '客场',
                        key: 'awaysxname'
                    },
                    {
                        title: '详情',
                        type: 'expand',
                        align: 'center',
                        width: 80,
                        render: (h, params) => {
                            return h(odds, {
                                props: {
                                    row: params.row
                                }
                            },)
                        }
                    },
                    {
                        title: '状态',
                        key: 'state',
                        width: 90,
                        sortable: true,
                        render: (h, params) => {
                            let textArray = ['无效', '有效'];
                            return h('Tag', {
                                props: {
                                    color: params.row.state === 1 ? 'success' : 'default'
                                }
                            }, textArray[params.row.state])
                        }
                    },
                    {
                        title: '日期',
                        key: 'date',
                        sortable: 'custom',
                        sortType: 'desc'
                    },
                    {
                        title: '截止日期',
                        key: 'endtime',
                        sortable: 'custom'
                    },
                    {
                        title: '比赛时间',
                        key: 'beginning',
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
