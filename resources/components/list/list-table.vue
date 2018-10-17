<template>
    <div class="list-table">
        <transition name="fade">
            <Card class="operation-block" v-show="isShowOperation">
                <div class="search-block">
                    <searcher :search-item="searchItem" :loading="loading" @search="search" @cancel="cancel"
                              @reset="$emit('reset')">
                        <slot name="search-block"></slot>
                    </searcher>
                </div>
                <div class="button-block">
                    <slot name="button-block"></slot>
                </div>
                <div class="modal-block">
                    <slot></slot>
                    <slot name="select-item-block" :item="selectRow" :list="list"></slot>
                </div>
            </Card>
        </transition>
        <Table ref="table" :columns="columns" :data="list" :loading="loading" size="large" highlight-row
               @on-sort-change="onSortChange" @on-selection-change="onSelectChange" @on-row-click="clickCurrentRow" >
            <loading slot="loading"/>
            <pagination slot="footer" v-if="startPage" :total="pageData.total" :limit="pageData.limit"
                        :current="pageData.current" @change-page="onChangePage" @change-limit="onChangeSize"/>
        </Table>
    </div>
</template>
<script>
    import searchUtil from '@/libs/searchUtil';
    import loading from '@/components/loading';
    import searcher from '@/components/searcher';
    import Header from './list-header';
    import pagination from './list-pagination';

    export default {
        name: 'list-table',
        components: {searcher, Header, loading, pagination},
        props: {
            url: {
                type: String,
                required: true
            },
            primary: {
                type: String,
                default: 'id',
            },
            columns: {
                type: Array,
                default () {
                    return [];
                }
            },
            searchItem: {
                type: Object,
                default () {
                    return {};
                }
            },
            showOperation: {
                type: Boolean,
                default: true,
            },
            autoLoad: {
                type: Boolean,
                default: true
            },
            startPage:  {
                type: Boolean,
                default: true
            },
            startSort:  {
                type: Boolean,
                default: true
            },
            pageInfo: {
                type: Object,
                default() {
                    return {current:1, page: 1, limit: 10, total: 0}
                }
            },
            sort: {
                type: Array,
                default() {
                    return [{id: 'desc'}]
                }
            }
        },
        data () {
            return {
                list: [],
                selectRow: {},
                selectRowIds: [],
                isShowOperation: this.showOperation,
                lastSearchItem: '',
                loading: false,
                sortBy: this.deepCopy(this.sort),
                pageData: Object.assign({}, this.pageInfo)
            }
        },
        mounted () {
            if (this.autoLoad) {
                this.$nextTick(() => {this.loadList()});
            }
        },
        watch: {
            showOperation(value) {
                this.isShowOperation = value;
            }
        },
        methods: {
            exportCsv(option) {
                this.$refs.table.exportCsv(option)
            },
            /* 获取列表数据*/
            getList() {
                return this.list;
            },
            /* 获取已选择行*/
            getSelect() {
                return this.selectRow
            },
            /* 获取已选择行id*/
            getSelectId() {
                return this.selectRow[this.primary]
            },
            /* 获取已选择所有行的id数组*/
            getSelectIds() {
                return this.selectRowIds;
            },
            /* 是否选择一项*/
            selectOne() {
                if (this.selectRowIds.length !== 1) {
                    this.$Message.warning('请选择一项');
                    return false;
                }
                return true;
            },
            /* 是否选择多项*/
            selectMulti() {
                if (this.selectRowIds.length < 1) {
                    this.$Message.warning('请至少选择一项');
                    return false;
                }
                return true;
            },
            /* 选项发生改变*/
            onSelectChange(selection) {
                let length = selection.length;
                if (length === 1) {
                    this.selectRow = selection[0];
                    let index = this.list.findIndex((item) => item[this.primary] === selection[0][this.primary]);
                    this.$refs.table.highlightCurrentRow(index)
                } else if (length !== 1) {
                    this.selectRow = {};
                    this.$refs.table.clearCurrentRow();
                }
                this.selectRowIds = length > 0 ? selection.map((item) => item[this.primary]) : [];
            },
            /* 点击单前行高亮同时选择当前行*/
            clickCurrentRow(row, index) {
                this.$refs.table.selectAll(false);
                this.$refs.table.toggleSelect(index)
            },

            /* 改变页数*/
            onChangePage(page) {
                this.pageData.page = page;
                this.pageData.current = page;
                this.loadList()
            },
            /* 改变每页大小*/
            onChangeSize(limit) {
                this.pageData.limit = limit;
                this.loadList()
            },
            /* 点击排序*/
            onSortChange(column) {
                let sortItem = {[column.key]: column.order};
                let index = this.sort.length >= 1 ? this.sort.length -1 : 0;

                this.sortBy.splice(index, 1, sortItem);
                this.loadList()
            },
            search() {
                if (this.lastSearchItem !== JSON.stringify(this.searchItem)) {
                    this.pageData.page = 1;
                    this.pageData.current = 1;
                }
                this.loadList()
            },
            /* 加载列表数据, 返回promise*/
            loadList() {
                this.lastSearchItem = JSON.stringify(this.searchItem);
                return searchUtil.getList(this.url, this).then((list) => {
                    this.$emit('complete', list);
                    return list;
                })
            },
            /* 取消操作*/
            cancel() {
                searchUtil.stop('已停止请求！')
            },
            deepCopy(obj) {
                return JSON.parse(JSON.stringify(obj));
            }
        }
    };
</script>
<style lang="less">
    .operation-block {
        background: white;
        border: 1px solid #dddee1;
        margin-bottom: 0.5rem !important;
    }
    .button-block {
        button {
            margin: 0.25rem !important;
        }
        button:first-child {
            margin-left: 0!important;
        }
    }
</style>
