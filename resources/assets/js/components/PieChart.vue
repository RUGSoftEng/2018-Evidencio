<script>

  import { Pie } from 'vue-chartjs';

  export default {
    props : {
      label: {
        type: String
      },
      data: {}
    },
    extends: Pie,
    mounted () {
      // Overwriting base render method with actual data.
      this.renderChart(this.data);
    }
  }

</script>