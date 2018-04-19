<template>
    <div id="accVariablesEdit">
        <variable-edit-item v-for="(variable, index) in selectedVariables" :key="index" :index-item="index" 
        :variable="usedVariables[variable]" @toggle="selectCard($event)"></variable-edit-item>
    </div>
</template>

<script>
import VariableEditItem from "./VariableEditItem.vue";

export default {
  components: {
    VariableEditItem
  },

  props: {
    selectedVariables: {
      type: Array,
      required: true
    },
    usedVariables: {
      type: Object,
      required: true
    }
  },

  methods: {
    selectCard(index) {
      for (let ind = 0; ind < this.selectedVariables.length; ind++) {
        if (ind == index) $("#varEditCollapse_" + ind).collapse("toggle");
        else $("#varEditCollapse_" + ind).collapse("hide");
      }
    }
  }
};
</script>