<template>
    <div class="info-card">
        <slot name="header"></slot>
        <Card class="operation-block" v-show="isShowOperation">
            <div class="search-block">
                <searcher :search-item="searchItem" :loading="loading" @search="loadData" @cancel="cancel"
                          @reset="$emit('reset')">
                    <slot name="search-block"></slot>
                </searcher>
            </div>
        </Card>
        <div>
            <slot></slot>
            <loading v-show="loading"/>
        </div>
    </div>
</template>
<script>
    import searchUtil from '@/libs/searchUtil';
    import loading from '@/components/loading';
    import searcher from '@/components/searcher';

    export default {
        name: 'info-card',
        components: {searcher,loading},
        props: {
            url: {
                type: String,
                required: true
            },
            autoLoad: {
                type: Boolean,
                default: true
            },
            showOperation: {
                type: Boolean,
                default: true,
            },
            searchItem: {
                type: Object,
                default () {
                    return {};
                }
            }
        },
        data () {
            return {
                data: {},
                loading: false,
                isShowOperation: this.showOperation,
            }
        },
        watch: {
            showOperation(value) {
                this.isShowOperation = value;
            }
        },
        mounted () {
            if (this.autoLoad) {
                this.$nextTick(() => {this.loadData()});
            }
        },
        methods: {
            /* 取消操作*/
            cancel() {
                searchUtil.stop('操作被取消！')
            },
            getData() {
                return this.data;
            },
            loadData() {
                this.loading = true;
                let params = searchUtil.queriesBuilder.build(this);
                return this.$http.get(this.url, {params})
                    .then((result) => {
                        this.loading = false;
                        this.$emit('complete', result.data.data)
                    })
                    .catch((error) => {
                        this.loading = false;
                        this.$http.handler.handleError(error)
                    })
            },
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
