<template>
    <Steps :current="current" size="small" :status="stepStatus">
        <Step :title="payStatus" icon="ios-cash"></Step>
        <template v-if="drawVisible">
            <Step :title="drawStatus" icon="ios-print"></Step>
        </template>
        <template v-if="stateVisible">
            <Step :title="resultStatus" icon="logo-yen"></Step>
        </template>
    </Steps>
</template>

<script>
    export default {
        name: "order-status",
        props: {
            item: {
                type: Object,
                default() {
                    return {}
                }
            }
        },
        data() {
            return {
                stepStatus: 'finish',
                payVisible: true,
                drawVisible: true,
                stateVisible: true,
                stateText: ['', '开奖中', '未中奖', '已中奖'],
                stateColor: ['', 'gray', 'default', 'success'],
                drawText: ['出票中', '', '出票失败', '出票成功'],
                drawColor: ['gray', '', 'default', 'success'],
                payTypeText: ['支付中', '支付失败', '支付成功'],
                payTypeColor: ['gray', 'default', 'success'],
            }
        },
        mounted() {
            // 支付中或者支付失败
            if (+this.item.pay_type !== 2 )  {
                // 支付失败
                if (+this.item.pay_type === 1) {
                    this.stepStatus = 'error';
                }
                this.drawVisible = false;
                this.stateVisible= false;
            }
            // 出票中或者失败
            if (+this.item.to_draw !== 3) {
                // 支付失败
                if (+this.item.to_draw === 2) {
                    this.stepStatus = 'error';
                }
                this.stateVisible= false;
            }
            // 未中奖
            if (+this.item.state === 2) {
                this.stepStatus = 'error';
            }
        },
        computed: {
            current() {
                // 显示支付结果
                let current = 0;
                // 支付成功->显示出票结果
                if (this.item.pay_type === 2) {
                    current = 1
                }
                // 出票成功->显示中奖结果
                if (this.item.to_draw === 3) {
                    current = 2
                }
                return current;
            },
            payStatus() {
                return this.payTypeText[this.item.pay_type]
            },
            drawStatus() {
                return this.drawText[this.item.to_draw]
            },
            resultStatus() {
                return this.stateText[this.item.state]
            }
        },
        methods: {
        }
    }
</script>

<style scoped>

</style>
