<template>
    <!-- Modal -->
    <div class="modal fade" id="modalStep" tabindex="-1" role="dialog" aria-labelledby="modalStepOptions" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelTitleId">Step Options</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- TOP -->
                        <div class="row">
                            <div class="col-md-4">
                                <label for="colorPick">Pick a color:</label>
                                <button id="colorPick" type="button" class="btn btn-colorpick dropdown-toggle outline" data-toggle="dropdown" :style="{'background-color': localStep.colour}">{{ localStep.id }}</button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <div id="colorPalette"></div>
                                    </li>
                                </ul>
                                <div class="form-group">
                                    <label for="stepType">Select step-type:</label>
                                    <select class="custom-select" name="stepType" id="stepType" :disabled="stepId==0" v-model="localStep.type">
                                        <option value="input">Input</option>
                                        <option value="result">Result</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8 mb-2">
                                <details-editable :title="localStep.title" :description="localStep.description" @change="changeStepDetails" number-of-rows="2"></details-editable>
                            </div>
                        </div>

                        <!-- Middle -->
                        <div class="row">
                            <div class="col">

                                <div class="card" v-if="localStep.type == 'input'">
                                    <div class="card-header">
                                        <nav>
                                            <div class="nav nav-tabs card-header-tabs nav-scroll" id="nav-tab-modal" role="tablist">
                                                <a class="nav-item nav-link active" id="nav-variables-tab" data-toggle="tab" href="#nav-variables" role="tab" aria-controls="nav-variables"
                                                    aria-selected="true">Variables</a>
                                                <a class="nav-item nav-link" id="nav-api-tab" data-toggle="tab" href="#nav-api" role="tab" aria-controls="nav-api" aria-selected="false">Model calculation</a>
                                                <a class="nav-item nav-link" id="nav-logic-tab" data-toggle="tab" href="#nav-logic" role="tab" aria-controls="nav-logic"
                                                    aria-selected="false">Logic</a>
                                            </div>
                                        </nav>
                                    </div>
                                    <div class="card-body" id="modalCard">
                                        <div class="tab-content" id="nav-tabContent-modal">

                                            <div class="tab-pane fade show active" id="nav-variables" role="tabpanel" aria-labelledby="nav-variables-tab">
                                                <multiselect v-model="multiSelectedVariables" :options="models" :multiple="true" group-values="variables" group-label="title"
                                                    :group-select="true" :close-on-select="false" :clear-on-select="false" label="title"
                                                    track-by="id" :limit=3 :limit-text="multiselectVariablesText" :preserve-search="true"
                                                    placeholder="Choose variables" @remove="multiRemoveVariables" @select="multiSelectVariables">
                                                    <template slot="tag" slot-scope="props">
                                                        <span class="badge badge-info badge-larger">
                                                            <span class="badge-maxwidth">{{ props.option.title }}</span>&nbsp;
                                                            <span class="custom__remove" @click="props.remove(props.option)">❌</span>
                                                        </span>
                                                    </template>
                                                </multiselect>
                                                <label for="variableEditList" class="variable-label mb-2">Selected variables</label>
                                                <variable-edit-list :selected-variables="localStep.variables" :used-variables="localUsedVariables" @sort="updateOrder($event)"></variable-edit-list>
                                            </div>

                                            <div class="tab-pane fade" id="nav-api" role="tabpanel" aria-labelledby="nav-api-tab">
                                                <div class="container-fluid">
                                                    <div v-if="variablesUpToStep.length != 0">
                                                        <label for="apiCallModelSelect">Select model for calculation:</label>
                                                        <multiselect id="apiCallModelSelect" :multiple="true" v-model="multiSelectedModels" deselect-label="Remove model calculation"
                                                            track-by="id" label="title" placeholder="Select a model" :options="modelChoiceRepresentation"
                                                            :searchable="false" :allow-empty="true" open-direction="bottom" :close-on-select="false"
                                                            @select="modelSelectAPI" @remove="modelRemoveApi">
                                                            <template slot="tag" slot-scope="props">
                                                                <span class="badge badge-info badge-larger">
                                                                    <span class="badge-maxwidth">{{ props.option.title }}</span>&nbsp;
                                                                    <span class="custom__remove" @click="props.remove(props.option)">❌</span>
                                                                </span>
                                                            </template>
                                                        </multiselect>
                                                    </div>
                                                    <div v-else>
                                                        <h6>A model calculation cannot be done without variables. Either add
                                                            fields to the current step or link it to a precious step to use
                                                            the fields of that step.</h6>
                                                    </div>
                                                    <variable-mapping-api-list :api-calls="localStep.apiCalls" :used-variables="localUsedVariables" :reachable-variables="variablesUpToStep"
                                                        @remove="localStep.apiCalls = []"></variable-mapping-api-list>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="nav-logic" role="tabpanel" aria-labelledby="nav-logic-tab">
                                                <rule-edit-list :rules="localStep.rules" :children="childrenStepsExtended" :reachable-results="resultsUpToStep"></rule-edit-list>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="outputOptionsMenu" class="card" v-else>
                                    <div id="outputCategories" class="row vdivide">
                                        <div id="outputTypeLeft" class="col-sm-6">
                                            <div id="chartLayoutDesigner">
                                                <div class="dropdown">
                                                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        Pick a chart type
                                                    </a>

                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <a class="dropdown-item" v-on:click="changeChartType(0)">Bar Chart</a>
                                                        <a class="dropdown-item" v-on:click="changeChartType(1)">Pie Chart</a>
                                                        <a class="dropdown-item" v-on:click="changeChartType(2)">Polar Area Chart</a>
                                                        <a class="dropdown-item" v-on:click="changeChartType(3)">Doughnut chart</a>
                                                    </div>
                                                </div>
                                                <chart-items-list :current-step-data="localStep.chartRenderingData"
                                                                  :item-reference-upper="localStep.chartItemReference"
                                                                  :available-results-upper="resultsUpToStep"
                                                                  @refresh-chart-data="updateChartData($event)"
                                                                  @refresh-chart-data1="updateChartData($event)"
                                                                  @refresh-chart-data-after-deletion="updateChartData($event)"
                                                                  @refresh-reference-data="updateReferenceData($event)"
                                                                  @refresh-reference-data1="updateReferenceData($event)"
                                                                  @refresh-reference-data-after-deletion="updateReferenceData($event)"></chart-items-list>
                                            </div>
                                        </div>
                                        <div id="outputTypeRight" class="col-sm-6">
                                            <chart-preview :chart-type="localStep.chartTypeNumber" :chart-data-upper="localStep.chartRenderingData" :changed="chartChanged"></chart-preview>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer spaced">
                    <div>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" data-toggle="modal" data-target="#confirmModal" :disabled="this.stepId==0"
                            @click="remove">Remove</button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="apply">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import VariableEditList from "./VariableEditList.vue";
import RuleEditList from "./RuleEditList.vue";
import ChartPreview from "./ChartDisplay.vue";
import DetailsEditable from "./DetailsEditable.vue";
import VariableMappingApiList from "./VariableMappingApiList.vue";
import ChartItemsList from "./ChartItemsList";

export default {
  components: {
    VariableEditList,
    RuleEditList,
    ChartPreview,
    DetailsEditable,
    VariableMappingApiList,
    ChartItemsList
  },
  props: {
    stepId: {
      type: Number,
      required: true
    },
    models: {
      type: Array,
      required: true
    },
    steps: {
      type: Array,
      required: true
    },
    usedVariables: {
      type: Object,
      required: true
    },
    ancestorVariables: {
      type: Array,
      required: true
    },
    ancestorResults: {
      type: Array,
      required: true
    },
    childrenSteps: {
      type: Array,
      required: true
    },
    changed: {
      type: Boolean,
      required: true
    }
  },

  computed: {
    // Array containing all variables assigned up to and including the current step
    variablesUpToStep: function() {
      let vars = this.ancestorVariables;
      vars = vars.concat(this.localStep.variables);
      return vars;
    },
    // Array containing all results calculated up to and including the current step
    resultsUpToStep: function() {
      let results = this.ancestorResults;
      if (this.localStep.hasOwnProperty("apiCalls")) {
        this.localStep.apiCalls.forEach(apiCall => {
          apiCall.results.map(result => {
            results.push(result.name);
          });
        });
      }
      return results;
    },
    // Array of model-representations for API-call
    modelChoiceRepresentation: function() {
      let representation = [];
      this.models.forEach((model, index) => {
        representation.push({
          localId: index,
          title: model.title,
          id: model.id
        });
      });
      return representation;
    },
    // Array containing all children of the current step
    childrenStepsExtended: function() {
      let children = [];
      this.childrenSteps.forEach((childId, index) => {
        let step = this.steps[childId];
        children.push({
          stepId: childId,
          colour: step.colour,
          id: step.id,
          ind: index,
          title: step.title
        });
      });
      return children;
    }
  },

  mounted: function() {
    let self = this;
    $("#colorPalette")
      .colorPalette()
      .on("selectColor", function(evt) {
        self.localStep.colour = evt.color;
      });
  },

  watch: {
    changed: function() {
      this.reload();
    }
  },

  methods: {
    /**
     * Called whenever the modal is opened again.
     */
    reload() {
      this.localStep = JSON.parse(JSON.stringify(this.steps[this.stepId]));
      this.localUsedVariables = JSON.parse(JSON.stringify(this.usedVariables));
      this.setSelectedVariables();
      this.setSelectedModels();
      this.updateRuleTargetDetails();
      this.chartChanged = !this.chartChanged;
    },

    /**
     * Apply the changes made to the step (send an Event that does it)
     */
    apply() {
      this.$emit("change", {
        step: this.localStep,
        usedVars: this.localUsedVariables
      });
    },

    /**
     * Start the process of removing a step
     */
    remove() {
      Event.fire("confirmDialog", {
        title: "Removal of Step",
        message: "Are you sure you want to remove this step?",
        type: "removeStep",
        data: this.stepId
      });
    },

    /**
     * Update the order of the fields/variables
     * @param {Array} newOrderVariables has the new order of the variables
     */
    updateOrder(newOrderVariables) {
      this.selectedVariables = newOrderVariables;
      this.localStep.variables = newOrderVariables;
    },

    /**
     * Add a model to the API field mapping list
     * @param {Object} model to be added
     */
    modelSelectAPI(model) {
      this.localStep.apiCalls.push({
        evidencioModelId: model.id,
        title: model.title,
        results: this.models[model.localId].resultVars.map(result => {
          return {
            name: result,
            databaseId: -1
          };
        }),
        variables: this.models[model.localId].variables.map(variable => {
          return {
            evidencioVariableId: variable.id,
            evidencioTitle: variable.title,
            localVariable: ""
          };
        })
      });
    },

    /**
     * Remove a model from the API field mapping list
     * @param {Object} model to be removed
     */
    modelRemoveApi(model) {
      for (let index = this.localStep.apiCalls.length - 1; index >= 0; index--) {
        if (this.localStep.apiCalls[index].evidencioModelId == model.id) {
          this.localStep.apiCalls.splice(index, 1);
          return;
        }
      }
    },

    /**
     * Set the selected models for the API field mapping, to be called on reload()
     */
    setSelectedModels() {
      this.multiSelectedModels = [];
      this.multiSelectedModels = this.localStep.apiCalls.map(apiCall => {
        return {
          localId: this.findModel(apiCall.evidencioModelId),
          title: apiCall.title,
          id: apiCall.evidencioModelId
        };
      });
    },

    /**
     * Find a model locally based on the Evidencio Model Id
     * @param {Number} evidencioModelId
     */
    findModel(evidencioModelId) {
      for (let index = 0; index < this.models.length; index++) {
        if (this.models[index].id == evidencioModelId) return index;
      }
      return -1;
    },

    /**
     * Adds the selected variables to the selectedVariable part of the multiselect.
     * Due to the work-around to remove groups, this is required. It is not nice/pretty/fast, but it works.
     */
    setSelectedVariables() {
      this.multiSelectedVariables = [];
      for (let index = 0; index < this.localStep.variables.length; index++) {
        let origID = this.localUsedVariables[this.localStep.variables[index]].id;
        findVariable: for (let indexOfMod = 0; indexOfMod < this.models.length; indexOfMod++) {
          const element = this.models[indexOfMod];
          for (let indexInMod = 0; indexInMod < element.variables.length; indexInMod++) {
            if (element.variables[indexInMod].id == origID) {
              this.multiSelectedVariables.push(element.variables[indexInMod]);
              break findVariable;
            }
          }
        }
      }
    },

    /**
     * Everytime the modal is opened, the details for the rule-targets should be updated.
     */
    updateRuleTargetDetails() {
      this.localStep.rules.forEach(rule => {
        let next = rule.target,
          nextStep = this.steps[next.stepId];
        (next.id = nextStep.id), (next.title = nextStep.title), (next.colour = nextStep.colour);
      });
    },

    /**
     * Returns the text shown when more than the limit of options are selected.
     * @param {integer} [count] is the number of not-shown options.
     */
    multiselectVariablesText(count) {
      return " and " + count + " other variable(s)";
    },

    /**
     * Removes the variables from the step.
     * @param {array||object} [removedVariables] are the variables to be removed
     */
    multiRemoveVariables(removedVariables) {
      if (removedVariables.constructor == Array) {
        removedVariables.forEach(element => {
          this.multiRemoveSingleVariable(element);
        });
      } else {
        this.multiRemoveSingleVariable(removedVariables);
      }
    },

    /**
     * Helper function for modalRemoveVariables(removedVariables), removes a single variable
     * @param {Object} [removedVariable] the variable-object to be removed
     */
    multiRemoveSingleVariable(removedVariable) {
      for (let index = 0; index < this.localStep.variables.length; index++) {
        if (this.localUsedVariables[this.localStep.variables[index]].id == removedVariable.id) {
          delete this.localUsedVariables[this.localStep.variables[index]];
          this.localStep.variables.splice(index, 1);
          return;
        }
      }
    },

    /**
     * Selects the variables from the step.
     * @param {array||object} [selectedVariables] are the variables to be selected
     */
    multiSelectVariables(selectedVariables) {
      if (selectedVariables.constructor == Array) {
        selectedVariables.forEach(element => {
          this.multiSelectSingleVariable(element);
        });
      } else {
        this.multiSelectSingleVariable(selectedVariables);
      }
    },

    /**
     * Helper function for modalSelectVariables(selectedVariables), selects a single variable
     * @param {object} [selectedVariable] the variable-object to be selected
     */
    multiSelectSingleVariable(selectedVariable) {
      let varName = "var" + this.stepId + "_" + this.localStep.varCounter++;
      this.localStep.variables.push(varName);
      this.localUsedVariables[varName] = JSON.parse(JSON.stringify(selectedVariable));
    },

    /**
     * Changes the details of the step
     * @param {object} [newDetails] Object containin the keys 'title' and 'description'
     */
    changeStepDetails(newDetails) {
      this.localStep.title = newDetails.title;
      this.localStep.description = newDetails.description;
    },

    /**
     * Changes the type of the chart used inside a step
     * @param {Number} type Number representing the chart type.
     * 0 -> Bar, 1 -> Pie, 2 -> PolarArea, 3 -> Doughnut.
     */
    changeChartType(type) {
      this.localStep.chartTypeNumber = type;
    },

    /**
     * Adds the object containing at least the label and the color
     * corresponding to a graph field.
     * @param {String} label
     * @param {String} color
     */
    addNewField(label, color) {
      let object = {
        label,
        color
      };
      this.localStep.chartData.push(object);
    },

    updateChartData(chartData) {
      Vue.set(this.localStep, "chartRenderingData", JSON.parse(JSON.stringify(chartData)));
      this.chartChanged = !this.chartChanged;
    },

    updateReferenceData(refData) {
      Vue.set(this.localStep, "chartItemReference", JSON.parse(JSON.stringify(refData)));
    }
  },

  data() {
    return {
      localStep: {},
      localUsedVariables: {},
      multiSelectedVariables: [],
      chartChanged: false
    };
  }
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style lang="css" scoped>
.variable-label {
  font-weight: bold;
}

.spaced {
  justify-content: space-between;
}
</style>