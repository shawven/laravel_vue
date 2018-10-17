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
                    <FormItem label="用户名">
                        <Input v-model="searchItem.username" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="真实姓名">
                        <Input v-model="searchItem.real_name" clearable/>
                    </FormItem>
                </Col>
                <Col span="4">
                    <FormItem label="手机号码">
                        <Input v-model="searchItem.mobile" clearable/>
                    </FormItem>
                </Col>
                <Col span="6">
                    <FormItem label="注册时间">
                        <DatePicker type="datetimerange" split-panels class="w-100"
                                    v-model="searchItem.r.create_time" ></DatePicker>
                    </FormItem>
                </Col>
            </Row>
            <div  slot="button-block">
                <auth-button name="admin_create" @click="creatorModal = true"/>
                <auth-button name="admin_update" @click="editorModal = selectOne()"/>
                <auth-button name="admin_delete" @click="openDelConfirm"/>
                <auth-button name="admin_reset_password" @click="selectOne() && resetPassword()"/>
            </div>
            <div slot="select-item-block" slot-scope="{item, list}">
                <editor :edit="false" :list="list" :url="url" v-model="creatorModal"
                        title="添加员工" @created="created" ></editor>
                <editor :item="item" :list="list" :url="url" v-model="editorModal"
                        title="编辑员工" @updated="updated"></editor>
            </div>
        </list-table>
    </div>
</template>
<script>
    import editor from './editor';

    export default {
        name: 'admin-index',
        components: { editor},
        data() {
            let searchObj = {
                id: '',
                usernick: '',
                userPhone: '',
                r: {addtime: []}
            };
            return {
                url: '/api/admins',
                creatorModal: false,
                editorModal: false,
                searchItem: this.deepCopy(searchObj),
                localItem: this.deepCopy(searchObj),
                status: {
                    text: ['冻结', '正常'],
                    color: ['default', 'success'],
                },
                columns: [
                    {
                        type: 'selection',
                        width: 60,
                        align: 'center'
                    },
                    {
                        title: 'ID',
                        key: 'id',
                        width: 70,
                        sortable: 'custom'
                    },
                    {
                        title: '用户名',
                        key: 'username',
                        render: (h, params) => {
                            return h('div', [
                                h('Icon', {props: {type: 'person'}}),
                                h('span', ' ' + params.row.username),
                            ])
                        }
                    },
                    {
                        title: '用户昵称',
                        key: 'nickname',
                        render: (h, params) => {
                            return h('div', [
                                h('Avatar', params.row.avatar ? {props: {src: params.row.avatar}} : params.row.nickname.charAt(0)),
                                h('span', ' ' + (params.row.nickname)),
                            ])
                        }
                    },
                    {
                        title: '真实姓名',
                        key: 'real_name',
                    },
                    {
                        title: '手机号',
                        key: 'mobile',
                        sortable: 'custom'
                    },
                    {
                        title: '邮箱',
                        key: 'email',
                    },
                    {
                        title: '部门',
                        key: 'department',
                    },
                    {
                        title: '角色',
                        key: 'roles',
                        render: (h, params) => {
                            if (params.row.roles) {
                                let tags = [];
                                for(let key in params.row.roles) {
                                    tags.push(h('Tag', {
                                        props: {
                                            color: 'primary'
                                        }
                                    }, params.row.roles[key].name))
                                }
                                return h('div', tags);
                            }
                        },
                    },
                    {
                        title: '状态',
                        key: 'status',
                        sortable: 'custom',
                        render: (h, params) => {
                            return h('Tag', {
                                props: {color: this.status.color[params.row.status]}
                            }, this.status.text[params.row.status])
                        }
                    },
                    {
                        title: '最近登录时间',
                        key: 'login_time',
                        sortable: 'custom'
                    },
                    {
                        title: '最近登录地点',
                        key: 'login_ip_address',
                    },
                    {
                        title: '注册时间',
                        key: 'create_time',
                        sortable: 'custom'
                    },
                ]
            };
        },
        methods: {
            created(row) {
                this.$refs.listTable.loadList().then(() => {
                    this.resetPassword(row);
                })
            },
            updated(row) {
                let list = this.$refs.listTable.getList();
                list.splice(list.findIndex((item) => item.id === row.id), 1, row)
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
            resetPassword(selectRow) {
                let row = selectRow || this.$refs.listTable.getSelect();
                this.$modalHttp.prompt('/api/admins/' + row.id + '/password', {},
                    'password', '重置' + row.username + '的密码', '请输入密码')
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
