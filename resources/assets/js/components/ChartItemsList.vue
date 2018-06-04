<template>
    <div v-if="currentStepData !== undefined">
        <div class="row">
            <button type="button" class="btn btn-primary ml-2" @click="addChartItem">Add item</button>
        </div>
        <div class="row list-group" id="chartItemAdder">
            <div class="col">
                <chart-item-edit v-for="(item, index) in currentStepData.labels" :key="index" :index-item="index"
                                 :chart-item-label="item" :chart-item-color="currentStepData.datasets[0].backgroundColor[index]"
                                 v-bind:chart-item-value="currentStepData.datasets[0].data[index]" @toggle1="selectCard($event)"
                                 :curr-step-data="currentStepData" @refresh-chart-data-lower="refreshData($event)"
                                 @remove-chart-item="toggleUpperRemoveChartItem($event)"
                                 :chart-item-reference="itemReferenceUpper[index]"
                                 :available-results="availableResultsUpper"></chart-item-edit>
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
        currentStepData: {},
        itemReferenceUpper: {
          type: Array
        },
        availableResultsUpper: {
          type: Array
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
          this.itemReferenceUpper.push('');
          this.$emit("refresh-chart-data1", this.currentStepData);
          this.$emit("refresh-reference-data1", this.itemReferenceUpper);
        },

        refreshData(data) {
          this.currentStepData.labels[data[3]] = data[0];
          this.currentStepData.datasets[0].backgroundColor[data[3]] = data[1];
          this.currentStepData.datasets[0].data[data[3]] = Number(data[2]);
          this.itemReferenceUpper[data[3]] = data[4];
          this.$emit("refresh-chart-data", this.currentStepData);
          this.$emit("refresh-reference-data", this.itemReferenceUpper);

        },
        toggleUpperRemoveChartItem(delIndex) {
          let helpData = JSON.parse(JSON.stringify(this.currentStepData));
          helpData.labels.splice(delIndex, 1);
          helpData.datasets[0].backgroundColor.splice(delIndex, 1);
          helpData.datasets[0].data.splice(delIndex, 1);
          this.itemReferenceUpper.splice(delIndex, 1);
          this.$emit("refresh-chart-data-after-deletion", helpData);
          this.$emit("refresh-reference-data-after-deletion", this.itemReferenceUpper);
        }
      },
      data() {
        return {
          localChartItems: []
        }
      }
    };
</script>