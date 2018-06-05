<script>
  import { Pie, mixins } from 'vue-chartjs';
  const { reactiveProp } = mixins;

  export default {
    props : {
      label: {
        type: String
      },
      data: {}
    },
    mixins: [reactiveProp],
    extends: Pie,
    mounted () {
      // Overwriting base render method with actual data.
      this.renderChart(this.chartData, this.options);
    }
  }
</script>