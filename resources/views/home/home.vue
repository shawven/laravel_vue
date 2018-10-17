<template>
    <div class="home-main">
        <Row :gutter="10">
            <Col :md="24" :lg="8">
                <Row class-name="home-page-row1" :gutter="10">
                    <Col :md="12" :lg="24" :style="{marginBottom: '10px'}">
                        <Card>
                            <Row type="flex" class="user-infor">
                                <Col span="8">
                                    <Row class-name="made-child-con-middle" type="flex" align="middle">
                                        <img class="avator-img" :src="userInfo.avatar" />
                                    </Row>
                                </Col>
                                <Col span="16" style="padding-left:6px;">
                                    <Row class-name="made-child-con-middle" type="flex" align="middle">
                                        <div>
                                            <b class="card-user-infor-name">{{userInfo.nickname}}</b>
                                            <p>{{userInfo.login_ip_address}}（{{userInfo.login_ip}}）</p>
                                        </div>
                                    </Row>
                                </Col>
                            </Row>
                            <div class="line-gray"></div>
                            <Row class="margin-top-8">
                                <Col span="8"><p class="notwrap">上次登录时间:</p></Col>
                                <Col span="16" class="padding-left-8">{{userInfo.last_login_time}}</Col>
                            </Row>
                            <Row class="margin-top-8">
                                <Col span="8"><p class="notwrap">上次登录IP:</p></Col>
                                <Col span="16" class="padding-left-8">{{userInfo.last_login_ip_address}}（{{userInfo.last_login_ip}}）</Col>
                            </Row>
                        </Card>
                    </Col>
                </Row>
            </Col>
            <Col :md="24" :lg="16">
                <Row :gutter="5">
                    <Col :xs="24" :sm="12" :md="4" :style="{marginBottom: '10px'}">
                        <infor-card
                            id-name="today_user_created_count"
                            :end-val="data.user.today"
                            iconType="ios-contact"
                            color="#2d8cf0"
                            intro-text="今日新增用户"
                        ></infor-card>
                    </Col>
                    <Col :xs="24" :sm="12" :md="5" :style="{marginBottom: '10px'}">
                        <infor-card
                            id-name="thisWeek_user_created_count"
                            :end-val="data.user.thisWeek"
                            iconType="ios-contacts"
                            color="#64d572"
                            :iconSize="50"
                            intro-text="本周新增用户"
                        ></infor-card>
                    </Col>
                    <Col :xs="24" :sm="12" :md="5" :style="{marginBottom: '10px'}">
                        <infor-card
                            id-name="thisMonth_user_created_count"
                            :end-val="data.user.thisMonth"
                            iconType="md-contacts"
                            color="#ffd572"
                            intro-text="本月新增用户"
                        ></infor-card>
                    </Col>
                    <Col :xs="24" :sm="12" :md="5" :style="{marginBottom: '10px'}">
                        <infor-card
                            id-name="thisQuarter_user_created_count"
                            :end-val="data.user.thisQuarter"
                            iconType="ios-people"
                            color="#f25e43"
                            intro-text="本季度新增用户"
                        ></infor-card>
                    </Col>
                    <Col :xs="24" :sm="12" :md="5" :style="{marginBottom: '10px'}">
                        <infor-card
                                id-name="thisYear_user_created_count"
                                :end-val="data.user.thisYear"
                                iconType="ios-people"
                                color="#f25e43"
                                intro-text="本年度新增用户"
                        ></infor-card>
                    </Col>
                </Row>
                <Row :gutter="5">
                    <Col :xs="24" :sm="12" :md="4" :style="{marginBottom: '10px'}">
                        <infor-card
                                id-name="yesterday_user_created_count"
                                :end-val="data.user.yesterday"
                                iconType="ios-contact"
                                color="#2d8cf0"
                                intro-text="昨日新增用户"
                        ></infor-card>
                    </Col>
                    <Col :xs="24" :sm="12" :md="5" :style="{marginBottom: '10px'}">
                        <infor-card
                                id-name="lastWeek_user_created_count"
                                :end-val="data.user.lastWeek"
                                iconType="ios-contacts"
                                color="#64d572"
                                :iconSize="50"
                                intro-text="上周周新增用户"
                        ></infor-card>
                    </Col>
                    <Col :xs="24" :sm="12" :md="5" :style="{marginBottom: '10px'}">
                        <infor-card
                                id-name="lastMonth_user_created_count"
                                :end-val="data.user.lastMonth"
                                iconType="md-contacts"
                                color="#ffd572"
                                intro-text="上月新增用户"
                        ></infor-card>
                    </Col>
                    <Col :xs="24" :sm="12" :md="5" :style="{marginBottom: '10px'}">
                        <infor-card
                                id-name="lastQuarter_user_created_count"
                                :end-val="data.user.lastQuarter"
                                iconType="ios-people"
                                color="#f25e43"
                                intro-text="上季度新增用户"
                        ></infor-card>
                    </Col>
                    <Col :xs="24" :sm="12" :md="5" :style="{marginBottom: '10px'}">
                        <infor-card
                                id-name="lastYear_user_created_count"
                                :end-val="data.user.lastYear"
                                iconType="ios-people"
                                color="#f25e43"
                                intro-text="上年度新增用户"
                        ></infor-card>
                    </Col>
                </Row>
            </Col>
        </Row>
        <Row class="mt-3">
            <Col class="tabs-style">
                <Tabs type='card' @on-click="current = $event">
                    <TabPane icon='md-barcode' label="本周" name="thisWeek"></TabPane>
                    <TabPane icon='md-barcode' label="上周" name="lastWeek"></TabPane>
                    <TabPane icon='ios-timer' label="今天" name="today"></TabPane>
                    <TabPane icon='ios-timer' label="昨天" name="yesterday"></TabPane>
                    <TabPane icon='ios-calendar' label="本月" name="thisMonth"></TabPane>
                    <TabPane icon='ios-calendar' label="上月" name="lastMonth"></TabPane>
                    <TabPane icon='ios-stats' label="本季度" name="thisQuarter"></TabPane>
                    <TabPane icon='md-stats' label="上季度" name="lastQuarter"></TabPane>
                    <TabPane icon='md-sunny' label="本年度" name="thisYear"></TabPane>
                    <TabPane icon='md-sunny' label="上年度" name="lastYear"></TabPane>
                    <TabPane icon='md-contract' label="所有"  name="all"></TabPane>
                    <TabPane icon='md-search' label="自定义" name="custom">
                        <Card>
                            <Row v-show="showSelector" type="flex" justify="center" align="middle" class="bg-white">
                                <Col span="8" offset="4">
                                    <DatePicker type="datetime" placeholder="起始时间" transfer style="width: 200px;"
                                                :options="timeOptions" v-model="searchTime.startTime" />
                                    <DatePicker type="datetime" placeholder="结束时间" transfer
                                                style="width: 200px; margin-left: 1rem"
                                                :options="timeOptions" v-model="searchTime.endTime"/>
                                </Col>
                                <Col span="4">
                                    <Button type="primary" icon="android-search" :loading="loading" class="m-1"
                                            @click="search">查询
                                    </Button>
                                    <Button type="default" icon="android-refresh" class="m-1"
                                            @click="searchTime = {}">重置
                                    </Button>
                                </Col>
                            </Row>
                        </Card>
                    </TabPane>
                </Tabs>
            </Col>
        </Row>
        <Row :gutter="10" :style="{marginTop: showSelector ? '8px' : '-34px'}">
            <Col :md="24" :lg="8" :style="{marginBottom: '10px'}">
                <Card>
                    <p slot="title" class="card-title"><Icon type="ios-pulse-strong"></Icon> 下单数量占比</p>
                    <div class="data-source-row">
                        <pie-chart :data="data.order[current].category.orders" title="订单占比" tip="单" selector="percentage1"/>
                    </div>
                </Card>
            </Col>
            <Col :md="24" :lg="8" :style="{marginBottom: '10px'}">
                <Card>
                    <p slot="title" class="card-title"><Icon type="ios-pulse-strong"></Icon> 购彩金额占比</p>
                    <div class="data-source-row">
                        <pie-chart :data="data.order[current].category.totalMoney" title="金额占比" tip="元" selector="percentage2"/>
                    </div>
                </Card>
            </Col>
            <Col :md="24" :lg="8">
                <Card>
                    <p slot="title" class="card-title"><Icon type="android-wifi"></Icon> 订单总计</p>
                    <div class="data-source-row">
                        <gauge-chart :data="data.order[current]"/>
                    </div>
                </Card>
            </Col>
        </Row>
        <template v-if="current === 'thisWeek' || current === 'lastWeek'">
            <Row class="margin-top-10" :gutter="10">
                <Col :md="24" :lg="12" :style="{marginBottom: '10px'}">
                    <Card>
                        <p slot="title" class="card-title"><Icon type="android-map"></Icon> 每日下单数量</p>
                        <div class="data-source-row">
                            <bar-chart :data="data.order[current].category.orders.week" tip="单" selector="bar-char1"/>
                        </div>
                    </Card>
                </Col>
                <Col :md="24" :lg="12" :style="{marginBottom: '10px'}">
                    <Card>
                        <p slot="title" class="card-title"><Icon type="android-map"></Icon> 每日下单金额</p>
                        <div class="data-source-row">
                            <bar-chart :data="data.order[current].category.totalMoney.week" tip="元" selector="bar-char2"/>
                        </div>
                    </Card>
                </Col>
            </Row>
            <Row class="margin-top-10">
                <Card>
                    <p slot="title" class="card-title"><Icon type="ios-shuffle-strong"></Icon>每日下单数量</p>
                    <div class="line-chart-con">
                        <line-chart :data="data.order[current].category.orders.week" tip="单" selector="line-chart1"/>
                    </div>
                </Card>
            </Row>
            <Row class="margin-top-10">
                <Card>
                    <p slot="title" class="card-title"><Icon type="ios-shuffle-strong"></Icon>每日购彩金额</p>
                    <div class="line-chart-con">
                        <line-chart :data="data.order[current].category.totalMoney.week" tip="元" selector="line-chart2"/>
                    </div>
                </Card>
            </Row>
        </template>
    </div>
</template>

<script>
import pieChart from './components/pie-chart.vue';
import barChart from './components/bar-chart.vue';
import lineChart from './components/line-chart.vue';
import gaugeChart from './components/gauge-chart.vue';
import inforCard from './components/inforCard.vue';

export default {
    name: 'home',
    components: {pieChart, barChart, lineChart, gaugeChart, inforCard},
    data () {
        return {
            userInfo: this.$store.getters.userInfo,
            searchTime: {startTime: null, endTime: null},
            timeOptions: {
                disabledDate: (date) => date && date.valueOf() > Date.now()
            },
            loading: false,
            current: 'thisWeek',
            data: {
                order: {
                    all: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                jczq: 0
                            },
                            totalMoney: {
                                jclq: 0,
                                jczq: 0
                            }
                        }
                    },
                    today: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                jczq: 0
                            },
                            totalMoney: {
                                jclq: 0,
                                jczq: 0
                            }
                        }
                    },
                    yesterday: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                jczq: 0
                            },
                            totalMoney: {
                                jclq: 0,
                                jczq: 0
                            }
                        }
                    },
                    thisWeek: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                week: {
                                    jclq: [0, 0, 0, 0, 0, 0, 0],
                                    jczq: [0, 0, 0, 0, 0, 0, 0]
                                },
                                jczq: 0
                            },
                            totalMoney: {
                                jclq: 0,
                                week: {
                                    jclq: [0, 0, 0, 0, 0, 0, 0],
                                    jczq: [0, 0, 0, 0, 0, 0, 0]
                                },
                                jczq: 0
                            }
                        }
                    },
                    thisMonth: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                jczq: 0
                            },
                            totalMoney: {
                                jclq: 0,
                                jczq: 0
                            }
                        }
                    },
                    thisQuarter: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                jczq: 0
                            },
                            totalMoney: {
                                jclq: 0,
                                jczq: 0
                            }
                        }
                    },
                    thisYear: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                jczq: 0
                            },
                            totalMoney: {
                                jclq: 0,
                                jczq: 0
                            }
                        }
                    },
                    lastWeek: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                jczq: 0,
                                week: {
                                    jclq: [0, 0, 0, 0, 0, 0, 0],
                                    jczq: [0, 0, 0, 0, 0, 0, 0]
                                }
                            },
                            totalMoney: {
                                jclq: 0,
                                jczq: 0,
                                week: {
                                    jclq: [0, 0, 0, 0, 0, 0, 0],
                                    jczq: [0, 0, 0, 0, 0, 0, 0]
                                }
                            }
                        }
                    },
                    lastMonth: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                jczq: 0
                            },
                            totalMoney: {
                                jclq: 0,
                                jczq: 0
                            }
                        }
                    },
                    lastQuarter: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                jczq: 0
                            },
                            totalMoney: {
                                jclq: 0,
                                jczq: 0
                            }
                        }
                    },
                    lastYear: {
                        orders: 0,
                        totalMoney: 0,
                        category: {
                            orders: {
                                jclq: 0,
                                jczq: 0
                            },
                            totalMoney: {
                                jclq: 0,
                                jczq: 0
                            }
                        }
                    }
                },
                user: {
                    all: 0,
                    today:0,
                    yesterday:0,
                    thisWeek:0,
                    thisMonth:0,
                    thisQuarter:0,
                    thisYear:0,
                    lastWeek:0,
                    lastMonth:0,
                    lastQuarter:0,
                    lastYear:0
                }
            }
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.$http.get('api/count/home').then((result) => {
                this.data = result.data.data;
                this.data.order.custom = {
                    orders: 0,
                        totalMoney: 0,
                        category: {
                        orders: {
                            jclq: 0,
                                jczq: 0
                        },
                        totalMoney: {
                            jclq: 0,
                                jczq: 0
                        }
                    }
                };
            }).catch((error) => this.$http.handler.handleError(error));
        })
    },
    computed: {
        showSelector() {
            return this.current === 'custom'
        }
    },
    methods: {
        search() {
            let startTime = this.getTimeStamp(this.searchTime.startTime);
            let endTime = this.getTimeStamp(this.searchTime.endTime);
            this.loading = true;
            this.$http.get('api/count/home/orders', {params:{startTime, endTime}})
                .then((result) => {
                    this.data.order.custom = result.data.data;
                    this.loading = false;
                })
                .catch((error) => {
                    this.$http.handler.handleError(error);
                    this.loading = false;
                })
        },
        getTimeStamp(date){
            if(!date) return null;
            return Math.floor((new Date(date).getTime() / 1000))
        }
    }
};
</script>

<style lang="less">
    @import "../../styles/common.less";
    .user-infor{
        height: 120px;
    }
    .avator-img{
        display: block;
        width: 80%;
        max-width: 100px;
        height: auto;
    }
    .card-user-infor-name{
        font-size: 2em;
        color: #2d8cf0;
    }
    .card-title{
        color: #abafbd;
    }
    .made-child-con-middle{
        height: 100%;
    }
    .to-do-list-con{
        height: 145px;
        overflow: auto;
    }
    .to-do-item{
        padding: 2px;
    }
    .infor-card-con{
        height: 100px;
    }
    .infor-card-icon-con{
        height: 100%;
        color: white;
        border-radius: 3px 0 0 3px;
    }
    .map-con{
        height: 305px;
    }
    .map-incon{
        height: 100%;
    }
    .data-source-row{
        height: 200px;
    }
    .line-chart-con{
        height: 150px;
    }
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

