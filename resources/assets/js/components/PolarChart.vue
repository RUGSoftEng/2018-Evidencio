<script>
  import { PolarArea, mixins } from 'vue-chartjs';
  const { reactiveProp } = mixins;

  export default {
    extends: PolarArea,
    props: {
      options: {}
    },
    mixins: [reactiveProp],
    mounted () {
      // Overwriting base render method with actual data.
      this.renderChart(this.chartData, this.options);
    }
  }
</script>