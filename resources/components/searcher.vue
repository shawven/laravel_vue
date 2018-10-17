<template>
    <div v-show="show" class="search-block"  @keydown.enter="$emit('search')">
        <Form :model="searchItem" label-position="top" inline>
            <Row type="flex" justify="center" align="middle">
                <Col :lg="19" :md="18" :sm="16" :xs="14" class="search-form-items">
                    <slot></slot>
                </Col>
                <Col :lg="5" :md="6" :sm="8" :xs="10" class="button text-center">
                    <Button type="primary" icon="ios-search" :loading="loading" class="m-1"
                            @click="$emit('search')">查询
                    </Button>
                    <Button :type="assistType" :icon="assistIcon" class="m-1"
                            @click="loading ? $emit('cancel') : $emit('reset')">{{assistText}}
                    </Button>
                </Col>
            </Row>
        </Form>
    </div>
</template>

<script>
    export default {
        name: 'searcher',
        props: {
            searchItem: {
                type: Object,
                default() {
                    return {}
                }
            },
            loading: {
                type: Boolean,
                default: false,
            }
        },
        data() {
            return {
                buttons: []
            }
        },
        mounted() {
           this.$nextTick(() => {
               this.buttons = document.querySelectorAll('.search-form-items .ivu-form-item-content');
           })
        },
        computed: {
            show() {
                return Object.keys(this.buttons).length !== 0;
            },
            assistType () {
                return this.loading ? 'dashed' : 'default';
            },
            assistIcon () {
                return this.loading ? 'md-close' : 'md-refresh';
            },
            assistText () {
                return this.loading ? '停止' : '重置';
            }
        },
    };
</script>
