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

                            <div class="col-md-8" v-if="localStep.type == 'input'">
                                <vue-multiselect v-model="multiSelectedVariables" :options="possibleVariables" :multiple="true" group-values="variables"
                                    group-label="title" :group-select="true" :close-on-select="false" :clear-on-select="false"
                                    label="title" track-by="id" :limit=3 :limit-text="multiselectVariablesText" :preserve-search="true"
                                    placeholder="Choose variables" @remove="multiRemoveVariables" @select="multiSelectVariables">
                                    <template slot="tag" slot-scope="props">
                                        <span class="badge badge-info badge-larger">
                                            <span class="badge-maxwidth">{{ props.option.title }}</span>&nbsp;
                                            <span class="custom__remove" @click="props.remove(props.option)">❌</span>
                                        </span>
                                    </template>
                                </vue-multiselect>
                            </div>

                            <div class="col-md-8" v-if="localStep.type == 'result'">

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
                                                <a class="nav-item nav-link" id="nav-logic-tab" data-toggle="tab" href="#nav-logic" role="tab" aria-controls="nav-logic"
                                                    aria-selected="false">Logic</a>
                                                <a class="nav-item nav-link" id="nav-api-tab" data-toggle="tab" href="#nav-api" role="tab" aria-controls="nav-api" aria-selected="false">Model calculation</a>
                                            </div>
                                        </nav>
                                    </div>
                                    <div class="card-body" id="modalCard">
                                        <div class="tab-content" id="nav-tabContent-modal">

                                            <div class="tab-pane fade show active" id="nav-variables" role="tabpanel" aria-labelledby="nav-variables-tab">
                                                <variable-edit-list :selected-variables="localStep.variables" :used-variables="localUsedVariables"></variable-edit-list>
                                            </div>

                                            <div class="tab-pane fade" id="nav-logic" role="tabpanel" aria-labelledby="nav-logic-tab">
                                                <rule-edit-list :rules="localStep.rules" :children="childNodes"></rule-edit-list>
                                                <!--<div class="container-fluid">
                                                    <button type="button" class="btn btn-primary" @click="addRule()">Add rule</button>
                                                    <table class="table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Condition</th>
                                                                <th>Target</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(rule, index) in modalRules">
                                                                <td data-label="Name">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="Name" :id="'ruleName_' + index" :aria-describedby="'helpId_' + index" placeholder="Rule name"
                                                                            v-model="rule.name">
                                                                        <small :id="'helpId_' + index" class="form-text text-muted">Name of the rule</small>
                                                                    </div>
                                                                </td>
                                                                <td data-label="Condition">Wayne</td>
                                                                <td data-label="Target">Batman</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>-->
                                            </div>

                                            <div class="tab-pane fade" id="nav-api" role="tabpanel" aria-labelledby="nav-api-tab">
                                                <!--<div class="container-fluid">
                                                    <label for="apiCallModelSelect">Select model for calculation:</label>
                                                    <vue-multiselect id="apiCallModelSelect" v-model="modalApiCall.model" deselect-label="Cannot be done without a model" track-by="id"
                                                        label="title" placeholder="Select one" :options="modelChoiceRepresentation"
                                                        :searchable="true" :allow-empty="false" open-direction="bottom" @select="apiCallModelChangeAction">
                                                    </vue-multiselect>
                                                    <small class="form-text text-muted">Model used for calculation</small>
                                                    <h6>Set variables used in calculation:</h6>
                                                    <div class="form-group" v-for="apiVariable in modalApiCall.variables">
                                                        <label :for="'var_' + apiVariable.originalID">@{{ apiVariable.originalTitle }}</label>
                                                        <select class="custom-select" :name="apiVariable.title" :id="'var_' + apiVariable.originalID" v-model="apiVariable.targetID">
                                                            <option v-for="usedVariable in modalUsedVariables" :key="usedVariable.id">@{{ usedVariable.title }}</option>
                                                        </select>
                                                    </div>
                                                </div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="outputOptionsMenu" class="card" v-if="localStep.type == 'result'">
                                    <div id="outputCategories" class="row vdivide">
                                        <div id="outputTypeLeft" class="col-sm-6">

                                            <div id="outputCategoriesAccordion">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <a class="card-link" data-toggle="collapse" href="#collapseOne">
                                                            Pie Chart
                                                        </a>
                                                    </div>
                                                    <div id="collapseOne" class="collapse show" data-parent="#outputCategoriesAccordion">
                                                        <div class="card-body">
                                                            Lorem ipsum..
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card">
                                                    <div class="card-header">
                                                        <a class="collapsed card-link" data-toggle="collapse" href="#collapseTwo">
                                                            Bar Plot
                                                        </a>
                                                    </div>
                                                    <div id="collapseTwo" class="collapse" data-parent="#outputCategoriesAccordion">
                                                        <div class="card-body">
                                                            Lorem ipsum dolor sit amet, adhuc temporibus concludaturque nec et, cu nostrud euismod dissentias mel. Te nec vidisse persius
                                                            referrentur. Ad ius semper iuvaret, albucius placerat mea ad.
                                                            Agam appetere quo te, ad nusquam suavitate reformidans pri. Pri
                                                            viderer nominavi an, eu solet labores deserunt vim, te diceret
                                                            adipiscing liberavisse qui. Eos in viris tacimates periculis,
                                                            in pri consequat theophrastus, amet accusamus duo in. Aperiri
                                                            verterem per et, augue congue cu vis. Ne inani erroribus cum.
                                                            Essent tritani insolens eu pri. Ei dolore mucius detraxit sea,
                                                            vide liber ne est. Cu tation aliquip quaestio cum, per ad aeterno
                                                            patrioque intellegam. Te sit minimum albucius. Ad scripta consulatu
                                                            vim, cu case laudem partem vix. Ei eos consul inimicus, ius id
                                                            blandit deseruisse. Est purto idque ea, per cu eripuit saperet
                                                            consetetur. Id vim error nihil noster, in illud oblique sententiae
                                                            nec. Eu velit laudem nec, at tacimates imperdiet nec. Ei prima
                                                            aperiri legendos duo, ut rebum ullamcorper deterruisset his.
                                                            Vel eu feugiat salutatus, at ipsum aeterno reprehendunt sit.
                                                            Te dicam suscipit percipitur vel, in quo nulla graecis necessitatibus,
                                                            alia tollit placerat ut mel. Nominavi invidunt ut vel, copiosae
                                                            scribentur his cu. At eos vero noster. Ius vitae everti an, pro
                                                            eu dicunt convenire splendide. Vim natum illum signiferumque
                                                            et, numquam petentium per id. No duo adolescens vituperatoribus,
                                                            luptatum reprehendunt te quo. Erat impedit quo ut, sed dicant
                                                            omnesque an. Mel inani vitae omnesque ex, expetendis delicatissimi
                                                            conclusionemque in vel.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card">
                                                    <div class="card-header">
                                                        <a class="collapsed card-link" data-toggle="collapse" href="#collapseThree">
                                                            Doughnut chart
                                                        </a>
                                                    </div>
                                                    <div id="collapseThree" class="collapse" data-parent="#outputCategoriesAccordion">
                                                        <div class="card-body">
                                                            Lorem ipsum..
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header">
                                                        <a class="collapsed card-link" data-toggle="collapse" href="#collapseFour">
                                                            Polar area Chart
                                                        </a>
                                                    </div>
                                                    <div id="collapseFour" class="collapse" data-parent="#outputCategoriesAccordion">
                                                        <div class="card-body">
                                                            Lorem ipsum..
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header">
                                                        <a class="collapsed card-link" data-toggle="collapse" href="#collapseFive">
                                                            Smiley Faces
                                                        </a>
                                                    </div>
                                                    <div id="collapseFive" class="collapse" data-parent="#outputCategoriesAccordion">
                                                        <div class="card-body">
                                                            Lorem ipsum..
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div id="outputTypeRight" class="col-sm-6">
                                            TODO: Preview..
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" @click="apply">Apply</button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import VariableEditList from "./VariableEditList.vue";
import RuleEditList from "./RuleEditList.vue";

export default {
  components: {
    VariableEditList,
    RuleEditList
  },
  props: {
    stepId: {
      type: Number,
      required: true
    },
    step: {
      type: Object,
      default: () => {}
    },
    usedVariables: {
      type: Object,
      required: true
    },
    possibleVariables: {
      type: Array,
      required: true
    },
    childNodes: {
      type: Array,
      required: true
    },
    changed: {
      type: Boolean,
      required: true
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
      this.localStep = JSON.parse(JSON.stringify(this.step));
      this.localUsedVariables = JSON.parse(JSON.stringify(this.usedVariables));
      this.setSelectedVariables();
    },

    apply() {
      this.$emit("change", {
        step: this.localStep,
        usedVars: this.localUsedVariables
      });
    },

    /**
     * Adds the selected variables to the selectedVariable part of the multiselect.
     * Due to the work-around to remove groups, this is required. It is not nice/pretty/fast, but it works.
     */
    setSelectedVariables() {
      this.multiSelectedVariables = [];
      for (let index = 0; index < this.localStep.variables.length; index++) {
        let origID = this.localUsedVariables[this.localStep.variables[index]].id;
        findVariable: for (let indexOfMod = 0; indexOfMod < this.possibleVariables.length; indexOfMod++) {
          const element = this.possibleVariables[indexOfMod];
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
     * Returns the text shown when more than the limit of options are selected.
     * @param {integer} [count] is the number of not-shown options.
     */
    multiselectVariablesText(count) {
      return " and " + count + " other variable(s)";
    },

    /**
     * Removes the variables from the step.
     * @param {array||object} [removedVariables] are the variables to be removed (can be either an array of objects or a single object)
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
     * @param {object} [removedVariable] the variable-object to be removed
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
     * @param {array||object} [selectedVariables] are the variables to be selected (can be either an array of objects or a single object)
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
      /*  
      nodeID: -1, //ID in vue steps-array
      DatabaseStepId: -1, //ID in database
      modalStepType: "input",
      modalSelectedColor: "#000000",
      modalMultiselectSelectedVariables: [],
      modalSelectedVariables: [],
      modalVarCounter: -1,
      modalUsedVariables: {},
      modalRules: [],
      modalApiCall: {
        model: null,
        variables: []
      }*/
    };
  }
};
</script>