<template>
    <div style="width:100%;height:100%;" id="user_flow"></div>
</template>

<script>
import echarts from 'echarts';
export default {
    name: 'gauge-chart',
    props: {
        data: {
            type: Object,
            default() {
                return {}
            }
        }
    },
    watch: {
        data() {
            this.init()
        }
    },
    mounted() {
        this.init();
    },
    methods: {
        init() {
            let gauge = echarts.init(document.getElementById('user_flow'));

            const option = {
                tooltip: {
                    formatter: '{a} <br/>{b} : {c}'
                },
                series: [
                    {
                        name: '订单数量',
                        type: 'gauge',
                        min: 0,
                        max: 1000,
                        detail: {
                            formatter: '{value} 单',
                            fontSize: 18,
                            offsetCenter: [0, '50px']
                        },
                        data: [{value: 0, name: '订单数量'}],
                        center: ['25%', '50%'],
                        radius: '80%',
                        title: {
                            offsetCenter: [0, '80px']
                        },
                        axisLine: {
                            lineStyle: {
                                // color: [],
                                width: 20
                            }
                        },
                        splitLine: {
                            length: 20
                        }
                    },
                    {
                        name: '订单总额',
                        type: 'gauge',
                        min: 0,
                        max: 100000,
                        detail: {
                            formatter: '{value} ￥',
                            fontSize: 18,
                            offsetCenter: [0, '50px']
                        },
                        data: [{value: 0, name: '购彩金额'}],
                        center: ['75%', '50%'],
                        radius: '80%',
                        title: {
                            offsetCenter: [0, '80px']
                        },
                        axisLine: {
                            lineStyle: {
                                // color: [],
                                width: 20
                            }
                        },
                        splitLine: {
                            length: 20
                        }
                    }
                ]
            };

            option.series[0].data[0].value = this.data.orders;
            option.series[1].data[0].value = this.data.totalMoney;
            gauge.setOption(option);

            window.addEventListener('resize', function () {
                gauge.resize();
            });
        }
    }
};
</script>
