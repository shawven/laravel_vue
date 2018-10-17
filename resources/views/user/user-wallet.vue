<template>
    <Modal v-model="visible" width="400" :title="title"  @on-cancel="onCancel" :footer-hide="true">
        <Card :bordered="false" :title="'设备ID: ' + item.devicdid" dis-hover class="user-wallet-card">
            <Row>
                <Col span="4"><Tag color="primary">钱包</Tag></Col><Col span="20">{{item.wallet}}</Col>
            </Row>
            <Row>
                <Col span="4"><Tag color="primary">彩金</Tag></Col><Col span="20">{{item.handsel}}</Col>
            </Row>
            <Row>
                <Col span="4"><Tag color="primary">余额</Tag></Col><Col span="20">{{item.balance}}</Col>
            </Row>
        </Card>
    </Modal>
</template>

<script>
    export default {
        name: 'user-wallet',
        props: {
            title: String,
            item: {
                type: Object,
                default: {},
            },
            value: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                visible: this.value
            }
        },
        watch: {
            value(value) {
                this.visible = value;
            }
        },
        methods: {
            onCancel() {
                this.visible = false;
                this.$emit('input', false);
            }
        }
    };
</script>

<style lang="less">
    .user-wallet-card {
        .ivu-card-body .ivu-row {
            margin: 0.5rem;
        }
    }
</style>
