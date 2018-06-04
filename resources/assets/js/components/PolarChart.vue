<script>

  import { PolarArea, mixins } from 'vue-chartjs';
  const { reactiveProp } = mixins;

  export default {
    props : {
      label: {
        type: String
      },
      data: {}
    },
    mixins: [reactiveProp],
    extends: PolarArea,
    mounted () {
      // Overwriting base render method with actual data.
      this.renderChart(this.chartData, this.options);
    }
  }

</script>