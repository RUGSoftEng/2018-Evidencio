<script>
import { Bar } from "vue-chartjs";

  import { Bar, mixins } from 'vue-chartjs';
  const { reactiveProp } = mixins;

  export default {
    mixins: [reactiveProp],
    props : {
      label: {
        type: String
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    },
    extends: Bar,
    mounted () {
      // Overwriting base render method with actual data.
      this.renderChart(this.chartData, this.options);
    },
    // data() {
    //   return {
    //     options: {responsive: true, maintainAspectRatio: false}
    //   };
    // }
  }
};
</script>