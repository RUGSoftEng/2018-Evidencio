<template>
    <div class="mt-2">
        <button type="button" class="list-group-item list-group-item-action" data-toggle="collapse" :data-target="'#editChartItem_' + indexItem"
                aria-expanded="false" :aria-controls="'editChartItem_' + indexItem" :id="'headerChartItem_' + indexItem" @click="show = !show">
            <i class="fo-icon icon-down-open" v-if="!show">&#xe802;</i>
            <i class="fo-icon icon-up-open" v-else>&#xe803;</i>
            {{ currStepData.labels[indexItem] }}
        </button>
        <div class="collapse mt-2" :id="'editChartItem_' + indexItem">
            <form onsubmit="return false">
                <div class="form-group">
                    <label :for="'chartItemTitle_' + indexItem">Label </label>
                    <input type="text" name="" :id="'chartItemTitle_' + indexItem" class="form-control" v-model="localItemLabel" placeholder="Label" @change="toggleUpdate()">
                    <!--<small :id="'chartItemTitleHelp_' + indexItem" class="form-text text-muted">Label of the variable</small>-->
                </div>
                <div class="form-group">
                    <label :for="'chartItemColor_' + indexItem">Color </label>
                    <input type="text" name="" class="form-control" :id="'chartItemColor_' + indexItem" v-model="localItemColor" @change="toggleUpdate()">
                    <!--<small :id="'chartItemColorHelp_' + indexItem" class="form-text text-muted">Color of the item</small>-->
                </div>
                <div class="form-group">
                    <label :for="'chartItemValue_' + indexItem">Value </label>
                    <input type="number" name="" class="form-control" :id="'chartItemValue_' + indexItem" v-model="localItemValue" @change="toggleUpdate()">
                    <!--<small :id="'chartItemValueHelp_' + indexItem" class="form-text text-muted">Placeholder value of the item</small>-->
                </div>
                <button type="button" class="btn btn-primary ml-2">Remove from chart</button>
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
      indexItem: {
        type: Number,
        required: true
      },
      currStepData: {}
    },
    indexItem: {
      type: Number,
      required: true
    },
    data() {
      return {
        editing: false,
        localItemLabel: this.chartItemLabel,
        localItemColor: this.chartItemColor,
        localItemValue: Number(this.chartItemValue),
        show: false
      };
    },
    methods: {
      toggleShow() {
        this.$emit("toggle1", this.indexItem);
      },
      toggleUpdate() {
        this.$emit("refresh-chart-data", [this.localItemLabel, this.localItemColor,Number(this.localItemValue), this.indexItem]);
      }
    },
    computed: {
      getImage: function() {
        if (this.editing) return "/images/check.svg";
        else return "/images/pencil.svg";
      }
    }
  };
</script>