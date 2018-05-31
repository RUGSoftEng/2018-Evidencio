<template>
    <div>
        <div class="row">
            <button type="button" class="btn btn-primary ml-2" @click="addChartItem">Add item</button>
        </div>
        <div class="row" id="chartItemAdder">
            <div class="col">
                <chart-item-edit v-for="(item, index) in chartItems" :key="index" :index-item="index" :chartItem="item" @toggle1="selectCard($event)">
                </chart-item-edit>
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
    chartItems: {
      type: Array
    }
  },
  methods: {
    selectCard(index) {
      for (let ind = 0; ind < this.chartItems.length; ind++) {
        if (ind == index) $("#chartItemEditCollapse_" + ind).collapse("toggle");
        else $("#chartItemEditCollapse_" + ind).collapse("hide");
      }
    },
    addChartItem() {
      this.chartItems.push({
        label: "Enter Label",
        color: "#ff0000",
        value: 10
      });
    },
    data() {
      return {
        localChartItems: []
      };
    }
  }
};
</script>