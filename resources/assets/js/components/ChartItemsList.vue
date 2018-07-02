<template>
    <div v-if="currentStepData !== undefined">
        <div class="row" style="margin: 0px;">
            <button type="button" class="btn btn-primary ml-2" @click="addChartItem" :disabled="availableResultsUpper.length == 0" :title="addLabelButtonTitle"
                style="margin-left: 0px; margin-top: 10px;">Add item</button>
        </div>
        <div class="row list-group" id="chartItemAdder">
            <div class="col">
                <chart-item-edit v-for="(item, index) in currentStepData.labels" :key="index" :index-item="index" :chart-item-label="item"
                    :chart-item-color="currentStepData.datasets[0].backgroundColor[index]" :chart-item-value="currentStepData.datasets[0].data[index]"
                    :chart-item-reference="itemReferenceUpper[index]" :available-results="availableResultsUpper" @toggle1="selectCard($event)"
                    @refresh-chart-data-lower="refreshData($event)" @remove-chart-item="toggleUpperRemoveChartItem($event)"></chart-item-edit>
            </div>
        </div>
    </div>
</template>

<script>
import ChartItemEdit from "./ChartItemEdit.vue";

export default {
  components: {
    ChartItemEdit
  },
  props: {
    currentStepData: {
      type: Object,
      required: false,
      default: () => {
          return {
            labels: [],
            datasets: [{
                backgroundColor: [],
                data: []
            }]
          }
      }
    },
    itemReferenceUpper: {
      type: Array,
      required: false,
      default: () => {
          return [];
      }
    },
    availableResultsUpper: {
      type: Array,
      required: true
    }
  },
  computed: {
    addLabelButtonTitle() {
      if (this.availableResultsUpper.length == 0)
        return "No model-calculation results available to show. Please add a calculation in a parent-step.";
      else
        return "Add a label to the graph to show the result of a calculation.";
    }
  },
  methods: {
    selectCard(index) {
      for (let ind = 0; ind < this.currentStepData.labels.length; ind++) {
        if (ind == index) $("#chartItemEditCollapse_" + ind).collapse("toggle");
        else $("#chartItemEditCollapse_" + ind).collapse("hide");
      }
    },
    addChartItem() {
      this.currentStepData.labels.push("Enter Label");
      this.currentStepData.datasets[0].backgroundColor.push("#00ff00");
      this.currentStepData.datasets[0].data.push(17);
      this.itemReferenceUpper.push({
        reference: this.availableResultsUpper[0],
        negation: false
      });
      this.$emit("refresh-chart-data1", this.currentStepData);
      this.$emit("refresh-reference-data1", this.itemReferenceUpper);
    },

    refreshData(dataArray) {
      const helpData = JSON.parse(JSON.stringify(dataArray));
      this.currentStepData.labels[helpData[3]] = helpData[0];
      this.currentStepData.datasets[0].backgroundColor[helpData[3]] =
        helpData[1];
      this.currentStepData.datasets[0].data[helpData[3]] = Number(helpData[2]);
      this.itemReferenceUpper[helpData[3]] = helpData[4];
      this.$emit("refresh-chart-data", this.currentStepData);
      this.$emit("refresh-reference-data", this.itemReferenceUpper);
    },

    toggleUpperRemoveChartItem(delIndex) {
      this.currentStepData.labels.splice(delIndex, 1);
      this.currentStepData.datasets[0].backgroundColor.splice(delIndex, 1);
      this.currentStepData.datasets[0].data.splice(delIndex, 1);
      this.itemReferenceUpper.splice(delIndex, 1);
      this.$emit("refresh-chart-data-after-deletion", this.currentStepData);
      this.$emit(
        "refresh-reference-data-after-deletion",
        this.itemReferenceUpper
      );
    }
  }
};
</script>