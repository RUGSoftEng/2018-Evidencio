<template>
    <div class="list-group">
        <variable-mapping-api v-for="(apiCall, index) in apiCalls" :key="index" :index="index" :model="apiCall" :used-variables="usedVariables"
            :reachable-variables="reachableVariables"></variable-mapping-api>
    </div>
</template>

<script>
import VariableMappingApi from "./VariableMappingApi.vue";

export default {
  components: {
    VariableMappingApi
  },
  props: {
    apiCalls: {
      type: Array,
      required: true
    },
    usedVariables: {
      type: Object,
      required: true
    },
    reachableVariables: {
      type: Array,
      required: true
    }
  },
  watch: {
    // Change API mapping if a field is removed/added (only used in case of removal, actually)
    reachableVariables: function() {
      if (this.reachableVariables.length == 0) {
        this.$emit("remove");
      } else {
        let ifNotFound = this.reachableVariables[0];
        this.apiCalls.forEach(apiCall => {
          apiCall.variables.forEach(variable => {
            if (this.getReachableIndex(variable.localVariable) == -1) {
              variable.localVariable = ifNotFound;
              this.$notify({
                title: "Variable removed",
                text:
                  "You have removed one or more variables that were used in the model-calculation, it is now replaced with another.",
                type: "warn"
              });
            }
          });
        });
      }
    }
  },
  methods: {
    /**
     * Finds the index in the reachables based on the local variable name
     * @param {String} varName
     */
    getReachableIndex(varName) {
      for (let index = this.reachableVariables.length - 1; index >= 0; index--) {
        if (this.reachableVariables[index] == varName) return index;
      }
      return -1;
    }
  }
};
</script>
