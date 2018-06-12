<template>
    <div class="mt-2">
        <button type="button" class="list-group-item list-group-item-action" data-toggle="collapse" :data-target="'#editChartItem_' + indexItem"
            aria-expanded="false" :aria-controls="'editChartItem_' + indexItem" :id="'headerChartItem_' + indexItem" @click="show = !show">
            <i class="fo-icon icon-down-open" v-if="!show">&#xe802;</i>
            <i class="fo-icon icon-up-open" v-else>&#xe803;</i>
            {{ chartItemLabel }}
        </button>
        <div class="collapse mt-2" :id="'editChartItem_' + indexItem">
            <form onsubmit="return false">
                <div class="form-group">
                    <label :for="'chartItemTitle_' + indexItem">Label</label>
                    <input type="text" name="" :id="'chartItemTitle_' + indexItem" class="form-control" v-model="localItemLabel" placeholder="Label"
                        @change="toggleUpdate()">
                </div>
                <div class="form-group">
                    <label :for="'chartItemColor_' + indexItem">Color</label>
                    <input type="color" name="" class="form-control" :id="'chartItemColor_' + indexItem" v-model="localItemColor" @change="toggleUpdate()" style="padding: 2px 4px;">
                </div>
                <div class="form-group">
                    <label :for="'chartItemValue_' + indexItem">Value</label>
                    <input type="number" name="" class="form-control" :id="'chartItemValue_' + indexItem" v-model="localItemValue" @change="toggleUpdate()">
                </div>
                <div class="form-group">
                    <label :for="'chartItemReference_' + indexItem">Variable</label>
                    <select class="form-control" :id="'chartItemReference_' + indexItem" v-model="localReference.reference" @change="toggleUpdate()">
                        <option v-for="(result, index) in availableResults" :key="index" :value="result">{{ result }}</option>
                    </select>
                    <div class="form-check" title="Negated result means '100-result', mainly useful for percentage-results.">
                        <input class="form-check-input" type="checkbox" :id="'negation_' + indexItem" v-model="localReference.negation">
                        <label class="form-check-label" :for="'negation_' + indexItem">
                            Negate the result
                        </label>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary ml-2" style="float: right; margin-bottom: 20px;" @click="toggleRemoval()">Remove</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
  props: {
    chartItemLabel: {
      type: String,
      required: true
    },
    chartItemColor: {
      type: String,
      required: true
    },
    chartItemValue: {
      type: Number,
      required: true
    },
    chartItemReference: {
      type: Object,
      required: true
    },
    indexItem: {
      type: Number,
      required: true
    },
    availableResults: {
      type: Array
    }
  },
  mounted() {
    this.reload();
  },
  data() {
    return {
      localItemLabel: " ",
      localItemColor: " ",
      localItemValue: 0,
      localReference: " ",
      show: false
    };
  },
  watch: {
    chartItemLabel() {
      this.reload();
    },
    chartItemColor() {
      this.reload();
    },
    chartItemValue() {
      this.reload();
    },
    chartItemReference() {
      this.reload();
    }
  },
  methods: {
    reload() {
      this.localItemLabel = this.chartItemLabel;
      this.localItemColor = this.chartItemColor;
      this.localItemValue = this.chartItemValue;
      this.localReference = this.chartItemReference;
    },
    toggleShow() {
      this.$emit("toggle1", this.indexItem);
    },

    toggleUpdate() {
      this.$emit("refresh-chart-data-lower", [
        this.localItemLabel,
        this.localItemColor,
        this.localItemValue,
        this.indexItem,
        this.localReference
      ]);
    },

    toggleRemoval() {
      this.$emit("remove-chart-item", this.indexItem);
    }
  }
};
</script>