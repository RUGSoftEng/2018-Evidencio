<script>
import { Doughnut } from "vue-chartjs";

export default {
  props: {
    label: {
      type: String
    },
    data: {}
  },
  extends: Doughnut,
  mounted() {
    // Overwriting base render method with actual data.
    this.renderChart(this.data);
  }
};
</script>