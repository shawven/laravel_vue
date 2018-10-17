<template>
    <Modal v-model="visible" width="700" title="选择用户" class="top-modal"
           @on-ok="select" @on-cancel="cancel">
        <div style="max-height: 600px; overflow-y: auto;">
            <list-table ref="listTable" :url="url" :columns="columns"
                        :search-item="searchItem" @reset="searchItem = deepCopy(localItem)">
                <Row slot="search-block">
                    <Col span="6">
                        <FormItem label="活动ID">
                            <Input v-model="searchItem.id" clearable/>
                        </FormItem>
                    </Col>
                    <Col span="6">
                        <FormItem label="活动名称">
                            <Input v-model="searchItem.l.usernick" clearable/>
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
                l: {name: ''},
            };
            return {
                url: '/api/activities',
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
                        title: '活动状态',
                        key: 'state',
                    },
                    {
                        title: '创建时间',
                        key: 'create_time',
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
