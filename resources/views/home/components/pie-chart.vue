<template>
    <div style="width:100%;height:100%;" :id="selector"></div>
</template>

<script>
import echarts from 'echarts';

export default {
    name: 'pie-chart',
    props: {
        tip: String,
        title: String,
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
            this.init();
        }
    },
    mounted() {
        this.init();
    },
    methods: {
        init() {
            let pie = echarts.init(document.getElementById(this.selector));
            const option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b} : {c} ({d}%)'
                },
                legend: {
                    left: 'right',
                    data: ['竞彩篮球', '竞彩足球']
                },
                series: [
                    {
                        name: this.title,
                        type: 'pie',
                        radius: '66%',
                        center: ['50%', '60%'],
                        data: [
                            {value: this.data.jclq, name: '竞彩篮球', itemStyle: {normal: {color: '#ff6830'}}},
                            {value: this.data.jczq, name: '竞彩足球', itemStyle: {normal: {color: '#3eaad5'}}},
                            // {value: 0, name: '大乐透', itemStyle: {normal: {color: '#f27ee0'}}},
                            // {value: 0, name: '双色球', itemStyle: {normal: {color: '#ab8df2'}}},
                            // {value: 0, name: '快3', itemStyle: {normal: {color: '#e14f60'}}}
                        ],
                        label: {normal: {formatter: `{c}${this.tip} {d}%`, show: true, position: 'top'}},
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            };
            pie.setOption(option);
            window.addEventListener('resize', function () {
                pie.resize();
            });
        }
    }
};
</script>
