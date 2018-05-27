<template>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">{{ model.title }}</h5>
            <div class="alert alert-warning" role="alert" v-if="warningExists">
                The fieldmapping is done based on the expected use of fields. For the indicated field(s) the mapping could not be done automatically,
                please do this mapping manually.
            </div>
            <form>
                <div class="form-row">
                    <div class="form-group col-md-6" v-for="(variableMap, indexMap) in model.variables" :key="indexMap">
                        <label :for="'select_' + indexMap">{{ variableMap.evidencioTitle }}</label>
                        <multiselect :class="{warning: warnings[indexMap]}" :options="reachableVariables" :allow-empty="false"
                            deselect-label="Cannot be empty" v-model="variableMap.localVariable">
                            <template slot="singleLabel" slot-scope="props">
                                <span class="option__desc">
                                    <span class="option__title">{{ usedVariables[props.option].title }}</span>
                                </span>
                            </template>
                            <template slot="option" slot-scope="props">
                                <div class="option__desc">
                                    <span class="option__title">{{ usedVariables[props.option].title }}</span>
                                </div>
                             </template>
                        </multiselect>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <h6 class="card-title">Result variables</h6>
            <span class="badge badge-secondary mx-1" v-for="(result, index) in model.results" :key="index">{{ result.name }}</span>
        </div>
    </div>
</template>


<script>
import Multiselect from "vue-multiselect";

export default {
  components: {
    Multiselect
  },
  props: {
    model: {
      type: Object,
      required: true
    },
    reachableVariables: {
      type: Array,
      required: true
    },
    usedVariables: {
      type: Object,
      required: true
    }
  },
  computed: {
    warnings: function() {
      let ret = Array(this.model.variables.length).fill(false);
      if (this.model.variables[0].localVariable == "") {
        let ifNotFound = this.reachableVariables[0];
        this.model.variables.forEach((variable, index) => {
          let foundVariable;
          if ((foundVariable = this.findReachableVariable(variable.evidencioVariableId))) {
            variable.localVariable = foundVariable;
          } else {
            ret[index] = true;
            variable.localVariable = ifNotFound;
          }
        });
      }
      return ret;
    },
    warningExists: function() {
      return this.arrayOr(this.warnings);
    }
  },
  watch: {
    reachableVariables: function() {
      let ifNotFound = this.reachableVariables[0];
      this.model.variables.forEach(variable => {
        if (this.getReachableIndex(variable.localVariable) == -1) variable.localVariable = ifNotFound;
      });
    }
  },
  methods: {
    findReachableVariable(evidencioVariableId) {
      for (let index = this.reachableVariables.length - 1; index >= 0; index--) {
        if (this.usedVariables[this.reachableVariables[index]].id == evidencioVariableId)
          return this.reachableVariables[index];
      }
      return "";
    },
    arrayOr(array) {
      for (let index = array.length - 1; index >= 0; index--) {
        if (array[index]) return true;
      }
      return false;
    },
    getReachableIndex(varName) {
      for (let index = this.reachableVariables.length - 1; index >= 0; index--) {
        if (this.reachableVariables[index] == varName) return index;
      }
      return -1;
    }
  }
};
</script>

<style scoped>
.warning {
  border: solid 2px yellow;
}
</style>