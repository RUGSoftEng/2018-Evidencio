<template>
    <div v-if="chartType === 0">
        <bar-chart :label="label10" :data="this.chartData"></bar-chart>
    </div>
    <div v-else-if="chartType === 1">
        <pie-chart :label="label10" :data="this.chartData"></pie-chart>
    </div>
    <div v-else-if="chartType === 2">
        <polar-chart :label="label10" :data="this.chartData"></polar-chart>
    </div>
    <div v-else>
        <doughnut-chart :label="label10" :data="this.chartData"></doughnut-chart>
    </div>

</template>

<script>
    import BarChart from "./BarChart";
    import PieChart from "./PieChart";
    import PolarChart from "./PolarChart";
    import DoughnutChart from "./DoughnutChart";
    export default {
      components: {
        BarChart,
        PieChart,
        PolarChart,
        DoughnutChart
      },
      props: {
        label10: {
          type: String,
          default: 'This is a label I want to create'
        },
        chartType: //this.$parent.chartTypeNumber
            {
          type: Number,
          default: 0//this.$parent.chartTypeNumber,
        },
        chartData: {}
      },
      // methods: {
      //   refreshChartPreview() {
      //     switch (type) {
      //       case 0:
      //         BarChart.
      //     }
      //   }
      // }
    }

</script>