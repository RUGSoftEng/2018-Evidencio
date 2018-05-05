<template>
    <div id="variablesDivCard" class="card full-height">
        <div class="card-header">
            Variables <model-load></model-load>
        </div>

        <div class="card-body scrollbarAtProject full-height">
            <div id="accVariablesView">
                <div class="card" v-if="allVariables.length == 0">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0">
                            No variables added yet...
                        </h5>
                    </div>
                </div>
                <variable-view-item v-for="(variable, index) in allVariables" :key="index" :index-item="index" 
                :variable="variable" :times-used="allVariablesUsed[variable.id.toString()]" @toggle="selectCard($event)"></variable-view-item>
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
      for (let ind = 0; ind < this.allVariablesUsed.length; ind++) {
        if (ind == index) $("#varViewCollapse_" + ind).collapse("toggle");
        else $("#varViewCollapse_" + ind).collapse("hide");
      }
    }
  }
};
</script>

<style lang="scss" scoped>
#variablesDivCard {
  height: 100%;
}
</style>