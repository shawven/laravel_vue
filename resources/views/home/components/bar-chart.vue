<template>
    <div style="width:100%;height:100%;" :id="selector"></div>
</template>

<script>
import echarts from 'echarts';
export default {
    name: 'bar-chart',
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
            let bar = echarts.init(document.getElementById(this.selector));
            const option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow',
                    }
                },
                grid: {
                    top: 0,
                    left: '2%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                legend: {
                    left: 'right',
                    data: ['竞彩篮球', '竞彩足球']
                },
                xAxis: {
                    type: 'value',
                    boundaryGap: [0, 0.01],
                    axisLabel: {formatter: '{value} ' + this.tip}
                },
                yAxis: {
                    type: 'category',
                    data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
                    nameTextStyle: {
                        color: '#c3c3c3'
                    }
                },
                series: [
                    {
                        name: '竞彩篮球',
                        type: 'bar',
                        stack: '总量',
                        data: this.data.jclq,
                        itemStyle: {normal: {color: '#ff6830'}},
                        label: {normal: {show: true, position: 'insideRight'}},
                    },
                    {
                        name: '竞彩足球',
                        type: 'bar',
                        stack: '总量',
                        data: this.data.jczq,
                        itemStyle: {normal: {color: '#3eaad5'}},
                        label: {normal: {show: true, position: 'insideRight'}},
                    }
                ]
            };

            bar.setOption(option);

            window.addEventListener('resize', function () {
                bar.resize();
            });
        }
    }
};
</script>
