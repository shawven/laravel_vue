<template>
    <Card>
        <Tabs v-model="type" @on-click="toggle">
            <TabPane label="竞彩足球" icon="ios-football" name="jczq"></TabPane>
            <TabPane label="竞彩篮球" icon="ios-basketball" name="jclq"></TabPane>
        </Tabs>
        <Tabs v-model="period" type="card" class="m-2">
            <TabPane label="正在投注" name="now">
                <Timeline>
                    <TimelineItem v-for="(item, date) in nowItems" :key="date">
                        <lottery-item :date="weekDay(date)" :item="item" :type="type" @reload="getItems()"/>
                    </TimelineItem>
                </Timeline>
            </TabPane>
            <TabPane label="往期投注" name="past">
                <Timeline>
                    <TimelineItem v-for="(item, date) in pastItems" :key="date">
                        <lottery-item :date="weekDay(date)" :item="item" :type="type" @reload="getItems()"/>
                    </TimelineItem>
                    <div class="my-3 text-center">
                        <Page :current="pageData.page" :total="pageData.total" @on-change="changePage"simple/>
                    </div>
                </Timeline>
            </TabPane>
        </Tabs>
        <loading v-show="loading"/>
    </Card>
</template>

<script>
    import basketballOdds from '@/views/match/basketball/odds'
    import footballOdds from '@/views/match/basketball/odds'
    import lotteryItem from './lottery-item'

    export default {
        name: 'lottery-index',
        components: {basketballOdds, footballOdds, lotteryItem},
        data() {
            return {
                pageData: {page: 1, limit: 3, total: 100},
                loading: false,
                type: 'jczq',
                period: 'now',
                nowItems: {},
                pastItems: {}
            }
        },

        mounted() {
            this.getItems()
        },
        methods: {
            toggle(type) {
                this.type = type;
                this.getItems();
            },
            weekDay(date) {
                return date + ' 星期' + '日一二三四五六'.charAt(new Date(this.date).getDay());
            },
            getItems() {
                this.loading = true;
                this.$http.get(`/api/lotteries?type=${this.type}&page=${this.pageData.page}&limit=${this.pageData.limit}`)
                    .then((result) => {
                        let data = result.data.data;
                        this.nowItems = data.now;
                        this.pastItems = data.past;
                        this.loading = false;
                    }).catch((error) => {
                        this.$http.handler.handleError(error);
                        this.loading = false;
                    })
            },
            changePage(page) {
                this.pageData.page = page;
                this.getItems()
            }
        }
    };
</script>

