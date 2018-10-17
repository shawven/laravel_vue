<template>
    <Modal v-model="visible" width="900" title="资金明细统计" @on-cancel="onCancel" :footer-hide="true">
        <info-card ref="infoCard" :search-item="searchItem" :url="url" :showOperation="!isToday"
                   @complete="loadData" @reset="searchItem = deepCopy(localItem)">
            <Tabs @on-click="toggle = $event" v-model="toggle" slot="header">
                <TabPane label="今日" icon="ios-search-strong" name="today"></TabPane>
                <TabPane label="搜索" icon="stats-bars" name="history"></TabPane>
            </Tabs>
            <Row slot="search-block">
                <Col span="6">
                    <FormItem label="起始日期">
                        <DatePicker type="datetime" v-model="searchItem.startTime"/>
                    </FormItem>
                </Col>
                <Col span="6">
                    <FormItem label="截止日期">
                        <DatePicker type="datetime" v-model="searchItem.endTime"/>
                    </FormItem>
                </Col>
            </Row>
            <div>
                <div class="my-2">
                    <counter-detail :data="statisticalData"></counter-detail>
                </div>
            </div>
        </info-card>
    </Modal>
</template>

<script>
    import dateUtil from '@/libs/dateUtil'
    import infoCard from '@/components/info-card'
    import counterDetail from './counter-detail';

    export default {
        name: "counter",
        components: {infoCard, counterDetail},
        props:{
            value: {
                type: Boolean,
                default: false
            }
        },
        data() {
            let searchObj = {
                startTime: '',
                endTime: ''
            };
            return {
                toggle: 'today',
                current: 'yesterday',
                visible: this.value,
                data: {},
                url: '/api/count/users/money_records',
                searchItem: {
                    ...this.deepCopy(searchObj),
                    startTime: dateUtil.formatDate((new Date).setDate((new Date).getDate() - 1)) + '00:00:00',
                    endTime: dateUtil.formatDate(new Date) + '00:00:00'
                },
                localItem: this.deepCopy(searchObj),
            }
        },
        watch: {
            value(value) {
                this.visible = value;
            }
        },
        computed: {
            isToday() {
                return this.toggle === 'today';
            },
            statisticalData(){
                return this.data[this.toggle]
            }
        },
        methods: {
            onCancel() {
                this.visible = false;
                this.$emit('input', false);
            },
            deepCopy(obj) {
                return JSON.parse(JSON.stringify(obj))
            },
            getMoney(val) {
                let money = Number.parseFloat(val);
                money = Number.isNaN(money) ? 0.00 : money;
                return money.toFixed(2) + ' ￥';
            },
            getDate(date){
                return dateUtil.formatDate(date)
            },
            loadData(data) {
                this.data = {...this.data, ...data};
            },
        }
    }
</script>

<style lang="less">
    .tabs-style {
        > .ivu-tabs.ivu-tabs-card > .ivu-tabs-bar {
            .ivu-tabs-tab{
                border-radius: 0;
                background: #fff;
            }
            .ivu-tabs-tab-active{
                border-top: 1px solid #3399ff;
            }
            .ivu-tabs-tab-active:before{
                content: '';
                display: block;
                width: 100%;
                height: 1px;
                background: #3399ff;
                position: absolute;
                top: 0;
                left: 0;
            }
        }
    }
</style>
