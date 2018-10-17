<template>
    <list-table ref="listTable" :url="url" :columns="columns"
                :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
        <div slot="search-block">
            <Row>
                <Col span="4">
                    <FormItem label="用户id">
                        <Input v-model="searchItem.user_id" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="用户昵称">
                        <Input v-model="searchItem.jwl.u.usernick" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="活动ID">
                        <Input v-model="searchItem.activity_id" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="活动名称">
                        <Input v-model="searchItem.jwl.a.name" clearable/>
                    </FormItem>
                </Col>
            </Row>
        </div>
        <div slot="button-block">
            <auth-button name="join-activity" @click="joinModal = true"/>
            <auth-button name="activity_record_delete" @click="openDelConfirm"/>
        </div>
        <div>
            <join-activity v-model="joinModal" :url="url" @created="created"/>
        </div>
    </list-table>
</template>

<script>
    import joinActivity from './join-activity'

    export default {
        components: {joinActivity},
        name: "activity-record-index",
        data() {
            let searchObj = {
                id: '',
                user_id: '',
                activity_id: '',
                j: {
                    u: 'id,userName,usernick,real_name,userPhone,real_phone,real_card,isRealAttestation,avatar' +
                        ',wallet,balance,handsel,draw_balance',
                    a: 'name,desc,number'
                },
                jo: {u:'l,id,user_id', a: 'l,id,activity_id'},
                ja: {u:'userInfo', a: 'activityInfo'},
                jwl: {u:{usernick: ''}, a: {name: ''}}
            };

            return {
                url: '/api/activities/records',
                creatorModal: false,
                editorModal: false,
                joinModal: false,
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
                        title: '用户昵称',
                        key: 'userInfo',
                        width: 150,
                        render: (h, params) => h('user-info', {props: {item: params.row.userInfo}})
                    },
                    {
                        title: '活动名称',
                        render: (h, params) => h('span', params.row.activityInfo.name)
                    },
                    {
                        title: '活动描述',
                        render: (h, params) => h('span', params.row.activityInfo.desc)
                    },
                    {
                        title: '可参加次数',
                        render: (h, params) => {
                            return h('Tag', {
                                props: {color: 'primary'}
                            }, params.row.activityInfo.number ? params.row.activityInfo.number + ' 次' : '无限')
                        }
                    },
                    {
                        title: '参与时间',
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
