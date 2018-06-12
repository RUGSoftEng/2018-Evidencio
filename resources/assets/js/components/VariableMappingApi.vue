<template>
    <div class="mt-2">
        <button type="button" class="list-group-item list-group-item-action" data-toggle="collapse" :data-target="'#editApi_' + index"
            aria-expanded="false" :aria-controls="'editApi_' + index" :id="'headerApi_' + index" @click="show = !show" :class="{warning: warningExists}">
            <i class="fo-icon icon-down-open arrow" v-if="!show">&#xe802;</i>
            <i class="fo-icon icon-up-open arrow" v-else>&#xe803;</i>
            {{ model.title }}
            <span class="badge badge-secondary float-right">Id: {{ model.evidencioModelId }}</span>
        </button>
        <div class="alert alert-warning" role="alert" v-if="warningExists">
            The fieldmapping is done based on the expected use of fields. For the indicated field(s) the mapping could not be done automatically,
            please do this mapping manually.
        </div>
        <div class="collapse mt-2" :id="'editApi_' + index">
            <form>
                <div class="form-row">
                    <div class="form-group col-md-6" v-for="(variableMap, indexMap) in model.variables" :key="indexMap">
                        <label :for="'select_' + indexMap">{{ variableMap.evidencioTitle }}</label>
                        <multiselect :class="{warning: warnings[indexMap]}" :options="reachableVariables" :allow-empty="false" deselect-label="Cannot be empty"
                            v-model="variableMap.localVariable">
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
                <div class="row">
                    <div class="card-body">
                        <h5 class="card-title">Result variables</h5>
                        <h5>
                            <span class="badge badge-secondary mx-1" v-for="(result, index) in model.results" :key="index">{{ result.name }}</span>
                        </h5>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>


<script>
export default {
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
    },
    index: {
      type: Number,
      required: true
    }
  },
  computed: {
    // Preselect all fields, set a warning boolean to true if a field cannot be found
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
  methods: {
    /**
     * Tries to find a field in the reachables that has the given evidencioVariableId
     * @param {Number} evidencioVariableId
     */
    findReachableVariable(evidencioVariableId) {
      for (let index = this.reachableVariables.length - 1; index >= 0; index--) {
        if (this.usedVariables[this.reachableVariables[index]].id == evidencioVariableId)
          return this.reachableVariables[index];
      }
      return "";
    },

    /**
     * Performs the OR operation on the given array of booleans
     * @param {Array}
     */
    arrayOr(array) {
      for (let index = array.length - 1; index >= 0; index--) {
        if (array[index]) return true;
      }
      return false;
    }
  },
  data() {
    return {
      show: false
    };
  }
};
</script>

<style scoped>
.warning {
  border: solid 2px yellow;
}
.arrow {
  font-size: 120%;
}
</style>