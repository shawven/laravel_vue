<template>
    <Collapse class="my-2" :class="classes()">
        <Panel v-for="ticket in tickets" :key="ticket.id">
            <div class="d-lg-inline">
                <span class="mx-1">投注单: {{ticket.order_id}}</span>
                <span class="mx-1" v-if="getOdds()">赔率：{{getOdds()}}</span>
                <span class="mx-1" v-if="ticket.state !== undefined && ticket.state !== null">
                        {{ticketStateText[ticket.state]}}
                </span>
                <span class="mx-1" v-if="+ticket.state !== 1">状态码：{{ticket.code}}； 消息：{{ticket.message}}</span>
            </div>
            <div slot="content">
                <Row>
                    <Col span="24" v-for="(val, index) in ticket.Odds.split('//')" :key="index">{{val}}</Col>
                </Row>
                <betTicketSummary :item="summary.ticketsDesc ? summary.ticketsDesc[ticket.id] : []"/>
            </div>
        </Panel>
    </Collapse>
</template>

<script>
    import calculation from './calculation'
    import betTicketSummary from './bet-ticket-summary'

    export default {
        name: "bet-ticket",
        components: {betTicketSummary},
        props: {
            tickets: {
                type: Array,
                default() {
                    return [];
                }
            },
            summary: {
                type: Object,
                default() {
                    return {};
                }
            }
        },
        data() {
            return {
                ticketStateText: ['调用成功', '出票成功'],
            }
        },
        methods: {
            classes() {
                if (this.getOdds() > 0) {
                    return 'border border-green'
                }
            },
            getOdds() {
                return calculation.getTotalOdds(calculation.convert(this.summary.ticketsDesc));
            }
        }
    }
</script>

