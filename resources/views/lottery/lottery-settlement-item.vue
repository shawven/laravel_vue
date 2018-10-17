<template>
    <div>
        <Row>
            <Col span="6">
                <Tag color="primary">投注</Tag>
                <span class="font-weight-bold text-dark">{{betPlay}} {{item.multiple}} 倍</span>
            </Col>
            <Col span="6">
                <Tag color="primary">应交税额</Tag>
                <span class="font-weight-bold text-dark">{{item.tax}}</span>
            </Col>
            <Col span="12">
                <Tag color="primary">税后奖金</Tag>
                <span class="font-weight-bold text-dark">
                    {{item.money}} = 2 * {{item.multiple}} * {{getOdds}} {{+item.tax !== 0 ? '* 0.8' : ''}}
                </span>
            </Col>
        </Row>
        <hr class="my-2" style="border:none;border-top:1px solid #e9eaec;">
        <Row class="font-weight-bold">
            <Col span="24">投注分析结果：</Col>
            <Col class="p-2 my-1" span="24"  type="flex" justify="center" align="middle">
                <Col span="20">
                    <Col span="10">双方</Col>
                    <Col span="5">结果</Col>
                    <Col span="3">玩法</Col>
                    <Col span="3">预测</Col>
                    <Col span="3">赔率</Col>
                </Col>
                <Col span="4">总赔率</Col>
            </Col>
            <betTicketSummary ref="sumary" :item="combinations"/>
        </Row>
    </div>
</template>

<script>
    import calculation from '@/views/order/bet/calculation'
    import betTicketSummary from '@/views/order/bet/bet-ticket-summary'

    export default {
        name: "lottery-settlement-item",
        components: {betTicketSummary},
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
                totalOdds: null
            }
        },
        computed: {
            getOdds() {
                return calculation.getTotalOdds(calculation.convert(this.item.ticketsDesc));
            },
            combinations() {
                return calculation.convert(this.item.ticketsDesc)
            },
            betPlay() {
                let arr = [];
                let str = '';
                this.item.betPlay.split(',').forEach((item) => {
                    arr = item.split(':');
                    str +=  arr[1] + '注 ' + arr[0] + '串1,';
                });

                return str.replace(/,$/, '')
            }
        }
    }
</script>
