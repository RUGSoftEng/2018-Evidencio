<template>
    <div v-if="chartType === 0">
        <bar-chart :label="label10" :chart-data="localChartDataUpper" :options="optionsData"></bar-chart>
    </div>
    <div v-else-if="chartType === 1">
        <pie-chart :label="label10" :chart-data="localChartDataUpper" :options="optionsData"></pie-chart>
    </div>
    <div v-else-if="chartType === 2">
        <polar-chart :label="label10" :chart-data="localChartDataUpper" :options="optionsData"></polar-chart>
    </div>
    <div v-else>
        <doughnut-chart :label="label10" :chart-data="localChartDataUpper" :options="optionsData"></doughnut-chart>
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
        chartType:
            {
          type: Number,
          default: 0
        },
        chartDataUpper: {
          type: Object,
          default: () => {
            return {
              labels: ['January', 'February'],
              datasets: [{
                // label: "Edit Label",
                backgroundColor: ['#0000ff', '#ff0000'],
                data: [40, 20]
              }]
            }
          }
        },
        changed: {
          type: Boolean,
          required: true
        },
      },
      watch: {
        changed: function() {
          this.localChartDataUpper = JSON.parse(JSON.stringify(this.chartDataUpper));
        }
      },
      mounted() {
        this.localChartDataUpper = JSON.parse(JSON.stringify(this.chartDataUpper));
      },
      data() {
        return {
          localChartDataUpper: {},
          optionsData: {
            responsive: true,
            maintainAspectRatio: false,
            legend: false,
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true
                }
              }]
            }
          }
        }
      }
    }

</script>