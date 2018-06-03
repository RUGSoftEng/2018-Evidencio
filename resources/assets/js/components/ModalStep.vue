<!--
 - Color changing
 - Gray functions
    -->

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
                                                <label for="accVariablesEdit" class="variable-label mb-2">Selected variables</label>
                                                <variable-edit-list :selected-variables="localStep.variables" :used-variables="localUsedVariables" @sort="updateOrder($event)"></variable-edit-list>
                                            </div>

                                            <div class="tab-pane fade" id="nav-api" role="tabpanel" aria-labelledby="nav-api-tab">
                                                <div class="container-fluid" v-if="variablesUpToStep.length != 0">
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
                                                    <variable-mapping-api v-for="(apiCall, index) in localStep.apiCalls" :key="index" :model="apiCall" :used-variables="localUsedVariables"
                                                        :reachable-variables="variablesUpToStep"> </variable-mapping-api>
                                                </div>
                                                <div class="container-fluid" v-else>
                                                    <h6>A model calculation cannot be done without variables. Either add fields to the current step or link it to a precious step to use the fields of that step.</h6>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="nav-logic" role="tabpanel" aria-labelledby="nav-logic-tab">
                                                <rule-edit-list :rules="localStep.rules" :children="childrenStepsExtended"></rule-edit-list>
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
                                                <chart-items-list :current-step-data="this.localStep.chartRenderingData"></chart-items-list>
                                            </div>
                                        </div>
                                        <div id="outputTypeRight" class="col-sm-6">
                                            <chart-preview :chart-type="this.localStep.chartTypeNumber" :chart-data="this.localStep.chartRenderingData" :current-step="this.localStep"></chart-preview>
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
import Multiselect from "vue-multiselect";
import VariableEditList from "./VariableEditList.vue";
import RuleEditList from "./RuleEditList.vue";
import ChartPreview from "./ChartDisplay.vue";
import DetailsEditable from "./DetailsEditable.vue";
import VariableMappingApi from "./VariableMappingApi.vue";
import ChartItemsList from "./ChartItemsList";

export default {
  components: {
    Multiselect,
    VariableEditList,
    RuleEditList,
    ChartPreview,
    DetailsEditable,
    VariableMappingApi,
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
    reload() {
      this.localStep = JSON.parse(JSON.stringify(this.steps[this.stepId]));
      this.localUsedVariables = JSON.parse(JSON.stringify(this.usedVariables));
      this.setSelectedVariables();
      this.setSelectedModels();
      this.updateRuleTargetDetails();
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

    remove() {
      Event.fire("confirmDialog", {
        title: "Removal of Step",
        message: "Are you sure you want to remove this step?",
        type: "removeStep",
        data: this.stepId
      });
    },

    updateOrder(newOrderVariables) {
      this.selectedVariables = newOrderVariables;
      this.localStep.variables = newOrderVariables;
    },

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

    modelRemoveApi(model) {
      for (let index = this.localStep.apiCalls.length - 1; index >= 0; index--) {
        if (this.localStep.apiCalls[index].evidencioModelId == model.id) {
          this.localStep.apiCalls.splice(index, 1);
          return;
        }
      }
    },

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
     * Everytime the modal is opened, the details for the rule-targets shou;d be updated.
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
    }

    // /**
    //  * Adds a rule to the list of rules
    //  */
    // addRule() {
    //   this.modalRules.push({
    //     name: "Go to target",
    //     rule: [],
    //     target: -1
    //   });
    //   this.modalEditRuleFlags.push(false);
    // },

    // /**
    //  * Removes the rule with the given index from the list
    //  * @param {integer} [ruleIndex] of rule to be removed
    //  */
    // removeRule(ruleIndex) {
    //   this.modalRules.splice(ruleIndex, 1);
    //   this.modalEditRuleFlags.splice(ruleIndex, 1);
    // },

    // /**
    //  * Allows for a rule to be edited.
    //  * @param {integer} [index] of the rule to be edited
    //  */
    // editRule(index) {
    //   Vue.set(this.modalEditRuleFlags, index, !this.modalEditRuleFlags[index]);
    // },

    // /**
    //  * Returns the index in the models-array based on the Evidencio model ID, -1 if it does not exist.
    //  * @param {integer} [modelID] is the Evidencio model ID.
    //  */
    // getModelIndex(modelID) {
    //   for (let index = 0; index < this.models.length; index++) {
    //     if (this.models[index].id == modelID) return index;
    //   }
    //   return -1;
    // },

    // /**
    //  * Sets the variables-array in the apiCall-object to the variables of the newly selected model
    //  * @param {object} [selectedModel] is the newly selected model
    //  */
    // apiCallModelChangeAction(selectedModel) {
    //   let modID = this.getModelIndex(selectedModel.id);
    //   if (modID == -1) {
    //     this.modalApiCall.variables = [];
    //     return;
    //   }
    //   let modVars = [];
    //   this.models[modID].variables.forEach(element => {
    //     modVars.push({
    //       originalTitle: element.title,
    //       originalID: element.id,
    //       targetID: null
    //     });
    //   });
    //   this.modalApiCall.variables = modVars;
    // }
  },

  data() {
    return {
      localStep: {},
      localUsedVariables: {},
      multiSelectedVariables: []
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