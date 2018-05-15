<template>
    <div class="card height-100">
        <div class="card-header">
            Variables
            <model-load></model-load>
        </div>

        <div class="card-body height-100 sizing">
            <div class="scrollbar">
                <div id="accVariablesView">
                    <div class="card" v-if="allVariables.length == 0">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                No variables added yet...
                            </h5>
                        </div>
                    </div>
                    <variable-view-item v-for="(variable, index) in allVariables" :key="index" :index-item="index" :variable="variable" :times-used="allVariablesUsed[variable.id.toString()]"
                        @toggle="selectCard($event)"></variable-view-item>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import ModelLoad from "./ModelLoad.vue";
import VariableViewItem from "./VariableViewItem.vue";

export default {
  components: {
    ModelLoad,
    VariableViewItem
  },
  props: {
    allVariables: {
      type: Array,
      required: true
    },
    allVariablesUsed: {
      type: Object,
      required: true
    }
  },

  methods: {
    selectCard(index) {
      let numberOfUsedVariables = Object.keys(this.allVariablesUsed).length;
      for (let ind = 0; ind < numberOfUsedVariables; ind++) {
        if (ind == index) $("#varViewCollapse_" + ind).collapse("toggle");
        else $("#varViewCollapse_" + ind).collapse("hide");
      }
    }
  }
};
</script>

<style scoped>
.sizing {
    position: relative;
    min-height: 300px;
}

.scrollbar {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    overflow-y: auto;
    margin: 1.25rem;
}
</style>
