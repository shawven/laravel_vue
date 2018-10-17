<template>
    <Row type="flex" justify="center" align="middle" >
        <Col>
            <Page :total="total" :page-size="limit" :page-size-opts="pageSizeOpts" :current="current"
                  placement="top" show-sizer show-elevator show-total
                  @on-change="$emit('change-page', $event)" @on-page-size-change="$emit('change-limit', $event)">
            </Page>
        </Col>
    </Row>
</template>

<script>
    export default {
        name: 'pagination',
        props: {
            current: {
                type: Number,
                default: 1
            },
            total: {
                type: Number,
                default: 0
            },
            limit: {
                type: Number,
                default: 10
            }
        },
        data() {
            return {
                pageSizeOpts: [10, 20, 30, 50]
            }
        }
    };
</script>

<style lang="less">
    .ivu-table-footer {
        height: 52px;
    }
    .ivu-page {
        font-size: 12px;
        .ivu-page-options-elevator input {
            text-align: center !important;
        }
    }

</style>
