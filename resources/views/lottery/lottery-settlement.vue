<template>
    <Modal v-model="visible" :loading="modalLoading" width="700" title="派发奖励"
           @on-ok="handleSubmit" @on-cancel="onCancel">
        <lotterySettlementItem v-for="(item, index) in data.success" :key="index" :item="item"/>
        <Row v-for="(item, index) in data.errors" :key="index" :item="item">
            {{item.nickname}} 的 {{item.orderId}} 订单： {{item.message}}
        </Row>
        <loading v-show="contextLoading"/>
    </Modal>
</template>

<script>
    import lotterySettlementItem from './lottery-settlement-item'

    export default {
        name: "lottery-settlement",
        components: {lotterySettlementItem},
        props: {
            value: {
                type: Boolean,
                default: false
            },
            type: String
        },
        data () {
            return {
                modalLoading: true,
                contextLoading: false,
                visible: this.value,
                data: {
                    errors: [],
                    success:[]
                }
            }
        },
        watch: {
            value(value) {
                this.visible = value;
                if (value) {
                    this.init()
                }
            }
        },
        methods: {
            init() {
                this.contextLoading = true;
                this.$http.get('/api/lotteries/settlement/summary', {type: this.type, orderIds:2062})
                    .then((result) => {
                        this.data = result.data.data;
                        this.contextLoading = false
                    })
                    .catch((error) => {
                        this.$http.handler.handleError(error);
                        this.contextLoading = false
                    })
            },
            handleSubmit () {
                this.$messageHttp.post(
                    '/api/lotteries/settlement',
                    {type: this.type, orderIds:2055},
                    () => this.$emit('reload'), null,
                    () => this.resetLoading()
                )
            },
            onCancel() {
                this.visible = false;
                this.$emit('input', false);
            },
            resetLoading() {
                this.modalLoading = false;
                this.$nextTick(() => this.modalLoading = true)
            },
        }
    }
</script>
