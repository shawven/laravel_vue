<template>
    <div style="width:100%;height:100%;" :id="selector"></div>
</template>

<script>
import echarts from 'echarts';
export default {
    name: 'line-chart',
    props: {
        tip: String,
        selector: String,
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
        },
    },
    mounted() {
        this.init();
    },
    methods: {
        init() {
            let line = echarts.init(document.getElementById(this.selector));
            const option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        label: {
                            backgroundColor: '#6a7985'
                        }
                    }
                },
                grid: {
                    top: '3%',
                    left: '1.2%',
                    right: '1%',
                    bottom: '3%',
                    containLabel: true
                },

                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        axisLabel: {formatter: '{value} ' + this.tip}
                    }
                ],
                legend: {
                    orient: 'vertical',
                    left: 'right',
                    data: ['竞彩篮球', '竞彩足球']
                },
                series: [
                    {
                        name: '竞彩篮球',
                        type: 'line',
                        stack: '总量',
                        areaStyle: {normal: {
                                color: '#ff6830'
                            }},
                        data: this.data.jclq,
                        lable: { formatter: '{c} ' + this.tip}
                    },
                    {
                        name: '竞彩足球',
                        type: 'line',
                        stack: '总量',
                        areaStyle: {normal: {
                                color: '#3eaad5'
                            }},
                        data: this.data.jczq
                    }
                ]
            };

            line.setOption(option);

            window.addEventListener('resize', function () {
                line.resize();
            });
        }
    }
};
</script>
