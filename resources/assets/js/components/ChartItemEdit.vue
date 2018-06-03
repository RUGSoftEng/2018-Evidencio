<template>
    <div class="card border-secondary">
        <a href="#" @click="toggleShow" class="card-header collapsed" :id="'chartItemEditCollapseHeader_' + indexItem" data-parent="#accRulesEdit"
           aria-expanded="false" :aria-controls="'chartItemEditCollapse_' + indexItem">
            <h6 class="mb-0">
                {{ currStepData.labels[indexItem] }}
            </h6>
        </a>

        <div :id="'chartItemEditCollapse_' + indexItem" class="collapse" :aria-labelledby="'#chartItemEditCollapseHeader_' + indexItem">
            <div class="card-body">
                <form onsubmit="return false">
                    <div class="form-group">
                        <label :for="'chartItemTitle_' + indexItem">Label </label>
                        <input type="text" name="" :id="'chartItemTitle_' + indexItem" class="form-control" v-model="localItemLabel" placeholder="Label" :disabled="!editing" @change="toggleUpdate()">
                        <small :id="'chartItemTitleHelp_' + indexItem" class="form-text text-muted">Label of the variable</small>
                    </div>
                    <div class="form-group">
                        <label :for="'chartItemColor_' + indexItem">Color </label>
                        <input type="text" name="" class="form-control" :id="'chartItemColor_' + indexItem" v-model="localItemColor" :disabled="!editing" @change="toggleUpdate()">
                        <small :id="'chartItemColorHelp_' + indexItem" class="form-text text-muted">Color of the item</small>
                    </div>
                    <div class="form-group">
                        <label :for="'chartItemValue_' + indexItem">Value </label>
                        <input type="number" name="" class="form-control" :id="'chartItemValue_' + indexItem" v-model="localItemValue" :disabled="!editing" @change="toggleUpdate()">
                        <small :id="'chartItemValueHelp_' + indexItem" class="form-text text-muted">Placeholder value of the item</small>
                        <input type="image" class="buttonIcon" :src="getImage" @click="editing = !editing" alt="Edit">

                    </div>
                    <button type="button" class="btn btn-primary ml-2">Remove from chart</button>
                </form>
            </div>
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

    data() {
      return {
        editing: false,
        localItemLabel: this.chartItemLabel,
        localItemColor: this.chartItemColor,
        localItemValue: Number(this.chartItemValue)
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