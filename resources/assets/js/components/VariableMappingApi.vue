<template>
<div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ model.title }}</h5>
            <form>
                <div class="form-row" v-for="row in numberOfRows" :key="row">
                    <div class="form-group col-md-6">
                        <label :for="'select_' + (row*2)">{{ model.variables[row*2].evidencioTitle }}</label>
                        <select :id="'select_' + (row*2)" v-model="model.variables[row*2].localVariable" class="form-control">
                            <option v-for="(variable, index) in reachableVariables" :key="index" :value="variable">{{ usedVariables[variable].title }}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <div v-if="(row*2+1) < model.variables.length">
                            <label :for="'select_' + (row*2+1)">{{ model.variables[row*2+1].evidencioTitle }}</label>
                            <select :id="'select_' + (row*2+1)" v-model="model.variables[row*2+1].localVariable" class="form-control">
                                <option v-for="(variable, index) in reachableVariables" :key="index" :value="variable">{{ usedVariables[variable].title }}</option>
                            </select>
                        </div>
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
    }
  },
  computed: {
    numberOfRows: function() {
      let n = Math.ceil(this.model.variables.length / 2);
      return Array.apply(null, {
        length: n
      }).map(Number.call, Number);
    }
    // ,
    // choosableVariables: function() {
   //   let vars = [];
    //   let numberOfVariables = this.reachableVariables.length;
    //   for (let index = 0; index < numberOfVariables; index++)
    //     vars.push(this.usedVariables[this.reachableVariables[index]]);
    //   return vars;
    // }
  },
  data() {
    return {};
   }
};
</script>
