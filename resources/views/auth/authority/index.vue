<template>
    <Card>
        <Row>
            <Col>
                <Button class="float-left" type="primary" icon="md-refresh" @click="clearRoutesCache">刷新菜单</Button>
            </Col>
        </Row>
        <Row>
            <Col span="6">
                <loading v-show="loading" class="auth-menu-loading"/>
            </Col>
        </Row>
        <Row class="auth-menu">
            <Col span="6">
                <Tree ref="tree" :data="data" :render="renderContent" />
            </Col>
            <Col span="6" offset="6" class="auth-menu-editor" v-show="showDetail">
                <Form ref="formItem" :model="formItem" :label-width="70">
                    <FormItem :label="isMenu ? '菜单预览' : '按钮预览'">
                        <template v-if="isMenu">
                            <P class="font-lg"><Icon :type="formItem.icon"/> {{formItem.title}}</P>
                        </template>
                        <template v-else>
                            <Button :type="formItem.type ? formItem.type : 'default'" :icon="formItem.icon">{{formItem.title}}</Button>
                        </template>
                    </FormItem>
                    <FormItem :label="isMenu ? '菜单标题' : '按钮标题'" prop="title"
                              :rules="{required: true, message: '请输入' + (isMenu ? '菜单标题' : '按钮标题'), trigger: 'blur'}">
                        <Input v-model="formItem.title" clearable/>
                    </FormItem>
                    <FormItem :label="isMenu ? '菜单名称' : '按钮名称'" prop="name"
                              :rules="{required: true, message: '请输入唯一的' + (isMenu ? '菜单名称，用于路由标示' : '按钮名称用于权限判断'), trigger: 'blur'}">
                        <Input v-model="formItem.name" :placeholder="(isMenu ? 'vue路由对象的name值' : '按钮权限的name值')" clearable/>
                    </FormItem>
                    <FormItem :label="isMenu ? '菜单图标' : '按钮图标'" prop="icon"
                              :rules="{required: false, message: '请输入' + (isMenu ? '菜单图标' : '按钮图标'), trigger: 'blur'}">
                        <Input v-model="formItem.icon" clearable
                               :placeholder="(isMenu ? '菜单文字前的图标' : '按钮文字前的图标') + '，复制图标字段识别'">
                            <span slot="prepend"><Icon :type="formItem.icon"/></span>
                        </Input>
                    </FormItem>
                    <FormItem v-show="formItem.level > 1 && isMenu" label="访问URL"
                              :rules="{required: true, message: '请输入请求的访问路由URL', trigger: 'blur'}">
                        <Input v-model="formItem.path" placeholder="路由URL，显示在浏览器地址栏" clearable>
                            <span slot="prepend">{{hostUrl}}</span>
                        </Input>
                    </FormItem>
                    <FormItem v-show="formItem.level > 1" label="资源URL"
                              :rules="{required: true, message: '请输入请求的API资源URL', trigger: 'blur'}">
                        <Input v-model="formItem.resource" placeholder="需要消费的服务端API资源URL，会用于权限校验" clearable>
                            <span slot="prepend">{{hostUrl}}</span>
                        </Input>
                    </FormItem>
                    <FormItem  v-show="+formItem.level === 2" label="组件路径"
                               :rules="{required: true, message: '请输入组件相对路径, 相对于views，不包含index', trigger: 'blur'}">
                        <Input v-model="formItem.component" placeholder="前端路由组件的相对文件夹路径" clearable>
                            <span slot="prepend">views/</span>
                            <span slot="append">/index.vue</span>
                        </Input>
                    </FormItem>
                    <FormItem label="排序" prop="sort"
                              :rules="{required: false, type:'number', message: '请输入数字', trigger: 'blur'}">
                        <InputNumber v-model="formItem.sort" />
                    </FormItem>
                    <FormItem label="状态" prop="status"
                              :rules="{required: false, type:'number', message: '请选择状态', trigger: 'blur'}">
                        <i-switch size="large" v-model="formItem.status" :true-value="1" :false-value="0">
                            <span slot="open">有效</span>
                            <span slot="close">无效</span>
                        </i-switch>
                    </FormItem>
                    <FormItem class="text-center" v-can="'auth_update'">
                        <Button type="primary" icon="md-checkmark" class="m-2"
                                @click="handleSubmit" :loading="submitLoading">提交</Button>
                        <Button type="default" icon="md-refresh" class="m-2"
                                @click="$refs.formItem.resetFields()">重置</Button>
                    </FormItem>
                </Form>
            </Col>
            <Col span="4" offset="13" class="auth-menu-prepare-button-card"  v-show="showDetail">
                <Card title="预选按钮">
                    <Row type="flex" justify="center" align="top">
                        <Col :lg="10" :md="12" :sm="24" class="mx-1 text-center">
                            <Button type="primary" icon="ios-eye" @click="selectPrepareButton('show')">查看</Button>
                            <Button type="info" icon="ios-create" @click="selectPrepareButton('create')">添加</Button>
                            <Button type="warning" icon="md-create"  @click="selectPrepareButton('update')">编辑</Button>
                            <Button type="error" icon="ios-trash"  @click="selectPrepareButton('delete')">删除</Button>
                        </Col>
                        <Col :lg="8" :md="10" :sm="20" class="mx-1 text-center">
                            <Button type="primary"  @click="selectPrepareButton('primary')">主色</Button>
                            <Button type="info"  @click="selectPrepareButton('info')">浅色</Button>
                            <Button type="warning"  @click="selectPrepareButton('warning')">黄色</Button>
                            <Button type="error"  @click="selectPrepareButton('error')">红色</Button>
                            <Button type="success"  @click="selectPrepareButton('success')">绿色</Button>
                            <Button type="default"  @click="selectPrepareButton('default')">默认</Button>
                            <Button type="dashed"  @click="selectPrepareButton('dashed')">虚线</Button>
                        </Col>
                    </Row>
                    <Row class="text-center mt-3">
                        <Icon type="ios-heart"/>
                        <a href="https://www.iviewui.com/components/icon#%E7%A4%BA%E4%BE%8B" target="_blank">按钮大全</a>
                    </Row>
                </Card>
            </Col>
        </Row>
    </Card>

</template>
<script>
    export default {
        name: 'authority-index',
        data() {
            return {
                hostUrl: location.protocol + '//' + location.host,
                authorityUrl: '/api/authorities',
                loading: false,
                submitLoading: false,
                isMenu: true,
                showDetail: false,
                data: [],
                currentNode: {},
                buttonProps: {
                    type: 'default',
                    size: 'small',
                },
                formItem: {
                    id: null,
                    title: '',
                    name: '',
                    path: '',
                    resource: '',
                    icon: '',
                    type: '',
                    component: '',
                    parent_id: '',
                    level: null,
                    status: 1,
                    sort: 1,
                    expand: true,
                },
                prepareButtons:{
                    show: {title: '查看', icon: 'eye', type: 'primary'},
                    create: {title: '添加',icon: 'compose', type: 'info'},
                    update: {title: '编辑',icon: 'android-create', type: 'warning'},
                    delete: {title: '删除',icon: 'trash-a', type: 'error'},
                    primary: { type: 'primary'},
                    info: { type: 'info'},
                    dashed: { type: 'dashed'},
                    success: { type: 'success'},
                    warning: { type: 'warning'},
                    error: { type: 'error'},
                    default: { type: 'default'}
                }
            };
        },
        mounted() {
            this.$nextTick(() => { this.getMenus() });
        },
        watch: {
            'formItem.icon'(value) {
                let match = new RegExp('<Icon type="(.*)"').exec(value);
                this.formItem.icon = match ? match[1] : value;
            }
        },
        methods: {
            handleSubmit () {
                this.submitLoading = true;
                this.$refs.formItem.validate((valid) => {
                    if (valid) {
                        let url = this.formItem.id ? this.authorityUrl + '/' + this.formItem.id : this.authorityUrl;
                        let request = this.formItem.id ? this.$messageHttp.put : this.$messageHttp.post;
                        this.filterFormItem();
                        request(url, this.formItem,
                            (result) => {
                                this.formItem.id = result.data.id;
                                for (let key in this.formItem) {
                                    this.$set(this.currentNode.node, key, this.formItem[key]);
                                }
                            }, null,
                            () => {this.submitLoading = false})
                    } else {
                        this.submitLoading = false;
                    }
                })
            },
            filterFormItem() {
                if (this.formItem.path) this.formItem.path = this.formItem.path.replace(/\/$/, '');
                if (this.formItem.component) this.formItem.component = this.formItem.component.replace(/^\//, '');
                if (this.formItem.resource) {
                    this.formItem.resource = this.formItem.resource.replace(/\/$/, '');
                    this.formItem.resource = this.formItem.resource.startsWith('/')
                        ? this.formItem.resource
                        : '/' +  this.formItem.resource
                }
            },
            openDelConfirm (root, node, data) {
                this.$modalHttp.delete(this.authorityUrl + '/' + data.id, null, null,
                    () => {
                        this.$Message.success('删除成功');
                        this.remove(root, node, data)
                    }
                )
            },
            getMenus() {
                this.loading = true;
                this.$http.get(this.authorityUrl)
                    .then((result) => {
                        this.data = this.renderRootContent(result.data.data.list);
                        this.loading = false
                    })
                    .catch((error) => {
                        this.$http.handler.handleError(error);
                        this.loading = false
                    })
            },
            clearRoutesCache() {
                this.loading = true;
                this.$store.dispatch('getLatestRoutes').then(() => this.loading = false)
            },
            renderRootContent(data) {
                return [{
                    title: '菜单列表',
                    level: 0,
                    expand: true,
                    children: data,
                    render: (h, { root, node, data }) => {
                        let rootOperation = [
                            h('span',{
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold'
                                }
                            }, [
                                h('Icon', {
                                    props: {
                                        type: 'ios-folder'
                                    },
                                    style: {
                                        marginRight: '8px'
                                    }
                                }),
                                h('span', data.title)
                            ])
                        ];

                        if (this.$can('auth_create')) {
                            rootOperation.push( h('span', {
                                style: {
                                    display: 'inline-block',
                                    float: 'right',
                                }
                            }, [
                                h('Button', {
                                    props: {
                                        ...this.buttonProps,
                                        icon: 'ios-add',
                                        type: 'primary'
                                    },
                                    style: {
                                        width: '52px'
                                    },
                                    on: {
                                        click: () => { this.append(root, node, data) }
                                    }
                                })
                            ]))
                        }
                        return h('span', {
                            style: {
                                display: 'inline-block',
                                width: '100%'
                            }
                        }, rootOperation);
                    }
                }]
            },
            renderContent (h, {root, node, data}) {
                let canCreate = this.$can('auth_create');
                let canDelete = this.$can('auth_delete');

                let buttonGroup = [];
                if (data.level <= 3 && canCreate) {
                    buttonGroup.push(h('Button', {
                        props: {
                            ...this.buttonProps,
                            icon: 'ios-add'
                        },
                        style: {
                            marginRight: '8px'
                        },
                        on: {
                            click: () => { this.append(root, node, data) }
                        }
                    }))
                }
                if (canDelete) {
                    buttonGroup.push(h('Button', {
                        props: Object.assign({}, this.buttonProps, {
                            icon: 'ios-remove'
                        }),
                        on: {
                            click: () => { this.openDelConfirm(root, node, data) }
                        }
                    }));
                }

                let operation = [
                    h('span', {
                        'class': ['ivu-tree-title', {'ivu-tree-title-selected': data.selected}],
                        style: {
                            fontSize: '14px'
                        },
                        on: {
                            click: () => { this.select(root, node, data) }
                        }
                    }, [
                        h('Icon', {
                            props: {
                                type: data.icon
                            },
                            style: {
                                marginRight: '8px'
                            }
                        }),
                        h('span', data.title)
                    ]),
                ];

                if (canCreate && canDelete) {
                    operation.push(h('span', {
                        style: {
                            display: 'inline-block',
                            float: 'right',
                        }
                    }, buttonGroup));
                }

                return h('span', {
                    style: {
                        display: 'inline-block',
                        width: '100%'
                    }
                }, operation);
            },
            select(root, node, data) {
                this.showDetail = true;
                this.isMenu = data.level <= 2;
                this.formItem = this.deepCopy(data);
                this.currentNode = node;
                data.selected = true;
                root.forEach((item) => {
                    if (item.nodeKey !== 0 && item.nodeKey !== node.nodeKey) {
                        item.node.selected = false;
                    }
                })
            },
            append (root, node, data) {
                const children = data.children || [];
                let child = {
                    id: null,
                    name: '',
                    path: '',
                    resource: '',
                    icon: '',
                    type: '',
                    component: '',
                    title: '菜单名称',
                    level: data.level + 1,
                    parent_id: data.id,
                    status: 1,
                    sort: data.children ? data.children.length + 1 : 1,
                    expand: true,
                };
                children.push(child);
                this.$set(data, 'children', children);
                node.node.expand = true;
                this.$Message.success('已添加一个节点，赶快去看看吧！');
            },
            remove (root, node, data) {
                const parentKey = root.find(el => el === node).parent;
                const parent = root.find(el => el.nodeKey === parentKey).node;
                const index = parent.children.indexOf(data);
                if (this.currentNode.node.id === data.id) {
                    this.showDetail = false;
                    this.formItem = {}
                }
                parent.children.splice(index, 1);
            },
            selectPrepareButton(name) {
                let button = this.prepareButtons[name];
                if (button.title) this.formItem.title = button.title;
                if (button.icon) this.formItem.icon = button.icon;
                if (button.type) this.formItem.type = button.type;
            },
            deepCopy(obj) {
                return JSON.parse(JSON.stringify(obj))
            }
        }
    };
</script>

<style lang="less">
    .auth-menu {
        margin-top: 1rem;
        height: 700px;
        overflow-y: auto;
        &-loading {
            position: absolute;
            height: calc(~'700px + 1rem');
            width: calc(~'100% + 42px');
        }
        &-editor{
            position: fixed;
        }
        &-prepare-button-card {
            position: fixed;
            button {
                margin-bottom: 0.5rem !important;
            }
        }
    }
</style>
