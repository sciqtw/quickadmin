<template>
    <div class="q-chart">
        <v-chart  class="chart" :option="options" autoresize />
    </div>
</template>

<script>
    import * as echarts from "echarts";
    import VChart, { THEME_KEY } from "vue-echarts";
    import { ref, defineComponent } from "vue";
    export default {
        name: "v-Chart",
        [THEME_KEY]: "dark",
        components:{
            'v-chart':VChart
        },
        props:{
            options:{
                type:Object,
                default:() => {}
            }
        },
        setup: () => {
            const option = ref({
                grid: {
                    top: "20px",
                    bottom: "30px",
                    right: "10px",
                    containLabel: true
                },
                title: {
                    text: "Traffic Sources",
                    left: "center"
                },
                tooltip: {
                    trigger: "item",
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient: "vertical",
                    left: "left",
                    data: ["Direct", "Email", "Ad Networks", "Video Ads", "Search Engines"]
                },
                series: [
                    {
                        name: "Traffic Sources",
                        type: "pie",
                        radius: "55%",
                        center: ["50%", "60%"],
                        data: [
                            { value: 335, name: "Direct" },
                            { value: 310, name: "Email" },
                            { value: 234, name: "Ad Networks" },
                            { value: 135, name: "Video Ads" },
                            { value: 1548, name: "Search Engines" }
                        ],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: "rgba(0, 0, 0, 0.5)"
                            }
                        }
                    }
                ]
            })

            const ttt = ref({
                grid: {
                    top: "20px",
                    bottom: "30px",
                    right: "10px",
                    containLabel: true
                },
                xAxis: {
                    type: "category",
                    data: [],
                    offset: 5,
                    axisLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    }
                },
                yAxis: {
                    type: "value",
                    offset: 20,
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLine: {
                        show: false
                    }
                },
                tooltip: {
                    trigger: "axis",
                    formatter: (comp) => {
                        const [serie] = comp;

                        return `${serie.seriesName}：${serie.value}`;
                    },
                    axisPointer: {
                        show: true,
                        status: "shadow",
                        z: -1,
                        shadowStyle: {
                            color: "#E6F7FF"
                        },
                        type: "shadow"
                    },
                    extraCssText: "width:120px; white-space:pre-wrap"
                },
                series: [
                    {
                        name: "付款笔数",
                        type: "bar",
                        data: [],
                        itemStyle: {
                            normal: {
                                color: "#4165d7"
                            }
                        }
                    },
                    {
                        type: "bar",
                        xAxisIndex: 0,
                        barGap: "-100%",
                        data: [],
                        itemStyle: {
                            normal: {
                                color: "#f1f1f9"
                            }
                        },
                        zlevel: -1
                    }
                ]
            });

            return {
                option,
                ttt
            };
        },
        mounted() {

        },

    };
</script>
<style scoped>
    .chart {
        height: 200px;
    }
</style>
