<script>

  import { Bar } from 'vue-chartjs';

  export default {
    props : {
      label: {
        type: String
      },
      data: {}
    },
    extends: Bar,
    mounted () {
      // Overwriting base render method with actual data.
      this.renderChart(this.data);
    },
  }

</script>