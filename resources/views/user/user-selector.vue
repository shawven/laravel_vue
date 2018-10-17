<template>
    <Modal v-model="visible" width="900" title="选择用户" class="top-modal"
           @on-ok="select" @on-cancel="cancel">
        <div style="max-height: 600px; overflow-y: auto;">
            <list-table ref="listTable" :url="url" :columns="columns"
                        :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
                <Row slot="search-block">
                    <Col span="6">
                        <FormItem label="用户ID">
                            <Input v-model="searchItem.id" clearable/>
                        </FormItem>
                    </Col>
                    <Col span="6">
                        <FormItem label="用户昵称">
                            <Input v-model="searchItem.l.usernick" clearable/>
                        </FormItem>
                    </Col>
                    <Col span="6">
                        <FormItem label="手机号码">
                            <Input v-model="searchItem.l.userPhone" clearable/>
                        </FormItem>
                    </Col>
                </Row>
            </list-table>
        </div>
    </Modal>
</template>
<script>

    export default {
        name: 'user-selector',
        props: {
            value: {
                type: Boolean,
                default: false
            },
        },
        data() {
            let searchObj = {
                id: '',
                l: {userName: '', usernick: '', userPhone: ''},
                i: {channel: ''},
                r: {addtime: []}
            };
            return {
                url: '/api/users',
                visible: this.value,
                channels: this.$store.getters.userChannels,
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
                        title: '用户名',
                        key: 'userName',
                        render: (h, params) => {
                            return h('div', [
                                h('Icon', {props: {type: 'person'}}),
                                h('span', ' ' + params.row.userName),
                            ])
                        }
                    },
                    {
                        title: '用户昵称',
                        key: 'usernick',
                        render: (h, params) => h('user-info', {props: {item: params.row}})
                    },
                    {
                        title: '手机号码',
                        key: 'userPhone'
                    },
                    {
                        title: '渠道',
                        key: 'bonus',
                        width: 100,
                        render: (h, params) => {
                            let channel = this.channels.find((channel) => {
                                return channel.mark === params.row.channel
                            });
                            if (channel) {
                                return h('Tag', channel.name)
                            }
                        }
                    },
                    {
                        title: '注册时间',
                        key: 'addtime',
                        sortable: 'custom'
                    }
                ]
            };
        },
        watch: {
            value(value) {
                this.visible = value;
            }
        },
        methods: {
            select() {
                this.visible = false;
                this.$emit('select',{...this.$refs.listTable.getSelect()});
                this.$emit('input', false);
            },
            cancel() {
                this.visible = false;
                this.$emit('input', false);
            },
            deepCopy(obj) {
                return JSON.parse(JSON.stringify(obj))
            }
        }
    };
</script>
