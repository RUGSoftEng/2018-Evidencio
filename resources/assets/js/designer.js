Vue.component("detailsEditable", require("./components/DetailsEditable.vue"));
Vue.component("variableViewList", require("./components/VariableViewList.vue"));
Vue.component("modalStep", require("./components/ModalStep.vue"));
Vue.component("modalConfirm", require("./components/ModalConfirm.vue"));
Vue.component("vueLoading", require("vue-loading-overlay"));
import 'vue-loading-overlay/dist/vue-loading.min.css';
import Multiselect from "vue-multiselect";
Vue.component("multiselect", Multiselect);
import Notifications from 'vue-notification';
Vue.use(Notifications);

// ============================================================================================= //

/* Step-template:
    {-l
        id: -1,
        title: title,
        description: description,
        nodeId: -1,
        colour: '#0099ff',
        type: 'input' or 'result',
        create: true,
        destroy: false,
        [optional: 
          variables: [],
          rules: []
        ]
    }
    */

/* Level-template:
      {
        steps: []
      }
    */
window.vObj = new Vue({
  el: "#designerDiv",
  data: {
    modelLoaded: false,
    models: [],
    modelIds: [],
    numVariables: 0,
    usedVariables: {},
    timesUsedVariables: {},

    title: "Default title",
    description: "Default description",
    languageCode: "EN",
    steps: [],
    levels: [],
    maxStepsPerLevel: 0,
    stepsChanged: false,
    levelsChanged: false,
    connectionsChanged: false,
    nodeCounter: 0,
    edgeCounter: 0,

    deltaX: 150,
    deltaY: 250,
    addLevelButtons: [],
    addStepButtons: [],

    selectedStepId: 0,
    modalChanged: false,
    confirmDialog: {
      title: "",
      message: "",
      type: "",
      data: 0,
      approvalFunction: () => {}
    },

    workflowId: null,
    isLoading: true,

    debug: {}
  },

  created() {
    // Event called when the Cytoscape graph is ready for interaction.
    Event.listen("graphReady", () => {
      this.workflowId = this.urlParam("workflow");
      if (this.workflowId === null) {
        Event.fire("normalStart");
      } else this.loadWorkflow(this.workflowId);
    });
    // Event called when a normal (empty) start should occur.
    Event.listen("normalStart", () => {
      this.addLevel(0);
      this.addStep(
        "Starter step",
        "First step in the model shown to the patient. Change this step to fit your needs.",
        0
      );
      this.panView();
      this.isLoading = false;
    });
    // Event called when the user tries to load an Evidencio model
    Event.listen("modelLoad", modelId => {
      this.loadModelEvidencio(modelId);
    });
    // Event called when all the evidencio models for a workflow are (hopefully) loaded
    Event.listen("loadWorkflowAllModelsLoaded", () => {
      this.steps.forEach(localStep => {
        localStep.rules.forEach(rule => {
          rule.target.stepId = this.getStepIdFromDatabaseId(rule.target.id);
          rule.action = "create";
          if (rule.target.stepId == -1) {
            rule.action = "destroy";
          }
        });
        localStep.apiCalls.forEach(apiCall => {
          apiCall.title = this.models[this.getLocalIdFromModelId(apiCall.evidencioModelId)].title;
          apiCall.variables.forEach(variable => {
            let localVariable = this.getVariableKeyFromDatabaseId(variable.fieldId);
            variable.localVariable = localVariable;
            variable.evidencioTitle = this.usedVariables[localVariable].title;
          });
        });
      });
      this.connectionsChanged = !this.connectionsChanged;
      this.isLoading = false;
    })
    // Event called when the user tries to remove a step
    Event.listen("confirmDialog", confirmInfo => {
      this.prepareConfirmDialog(confirmInfo);
    });
  },

  computed: {
    // Array of all variables, pass by reference rather than by value.
    allVariables: function () {
      if (this.modelLoaded) {
        var allvars = [];
        this.models.forEach(element => {
          allvars = allvars.concat(element.variables);
        });
        return allvars;
      } else return [];
    },

    // Array containing children of currently selected step
    childrenSteps: function () {
      if (this.selectedStepId == -1) return [];
      let levelIndex = this.getStepLevel(this.selectedStepId);
      if (levelIndex == -1 || levelIndex == this.levels.length - 1) return [];
      return this.levels[levelIndex + 1].steps;
    },

    ancestors: function () {
      return this.getAncestorStepList(this.selectedStepId);
    },

    variablesUpToStep: function () {
      let vars = [];
      this.ancestors.forEach(stepId => {
        vars = vars.concat(this.steps[stepId].variables);
      });
      return vars;
    },

    resultsUpToStep: function () {
      let results = [];
      this.ancestors.forEach(stepId => {
        this.steps[stepId].apiCalls.map(apiCall => {
          apiCall.results.map(result => {
            results.push(result.name);
          });
        });
      });
      return results;
    }
  },

  methods: {
    /**
     * Load model from Evidencio API, Model is prepared for later saving.
     * @param {Number} modelId is the id of the Evidencio model that should be loaded.
     * @return {Promise} Returns the promise object. 
     */
    loadModelEvidencio(modelId) {
      var self = this;
      if (!this.isModelLoaded(modelId)) {
        return $.ajax({
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
          },
          url: "/designer/fetch",
          type: "POST",
          timeout: 5000,
          data: {
            modelId: modelId
          },
          success: function (result, textStatus) {
            self.debug = result;
            self.models.push(result);
            let newVars = self.models[self.models.length - 1].variables.length;
            self.numVariables += newVars;
            self.models[self.models.length - 1]["resultVars"] = [];
            self.models[self.models.length - 1].variables.map(x => {
              x["databaseId"] = -1;
              if (x["type"] == "categorical") {
                x.options.map(y => {
                  y["databaseId"] = -1;
                  y["friendlyTitle"] = y.title;
                });
              }
            });
            self.modelLoaded = true;
            self.modelIds.push(modelId);
            self.recountVariableUses();
            self.runDummyModelEvidencio(self.models.length - 1);
          },
          error: function (xhr, textStatus, errorThrown) {
            self.$notify({
              title: "Failed to grab Evidencio model",
              text: "There seems to be an issue with connection to Evidencio (evidencio.com). Please try again later.",
              type: "error"
            });
          }
        });
      }
    },

    /**
     * Performs a dummy api call of an ALREADY LOADED Evidencio model
     * @param {Number} localModelId : index of model in this.models array
     */
    runDummyModelEvidencio(localModelId) {
      var self = this;
      let values = {};
      this.models[localModelId].variables.forEach(variable => {
        if (variable.type == "continuous")
          values[variable.id.toString()] = variable.options.min;
        else
          values[variable.id.toString()] = variable.options[0].title;
      });

      //The code below is a start to working with sequential models, but I ignore them for now
      let steps = [];
      if (this.models[localModelId].hasOwnProperty("steps")) {
        return;
        // this.models[localModelId].steps.forEach(step => {
        //   steps.push({
        //     id: step.id
        //   })
        // });
      }
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        url: "/designer/runmodel",
        type: "POST",
        timeout: 5000,
        data: {
          modelId: self.models[localModelId].id,
          values: values
        },
        success: function (result) {
          if (result.hasOwnProperty("result")) {
            self.models[localModelId].resultVars.push("result_" + self.models[localModelId].id.toString() + "_0");
          } else {
            for (let index = 0; index < result.resultSet.length; index++) {
              self.models[localModelId].resultVars.push("result_" + self.models[localModelId].id.toString() + "_" + index);
            }
          }
        },
        error: function (xhr, textStatus, errorThrown) {
          self.$notify({
            title: "Failed to run Evidencio model",
            text: "There seems to be an issue with connection to Evidencio (evidencio.com). Please try again later.",
            type: "error"
          });
        }
      });
    },

    /**
     * Save Workflow in database, IDs of saved data are set after saving.
     */
    saveWorkflow() {
      var self = this;
      let url = "/designer/save";
      if (this.workflowId != null) url = url + "/" + this.workflowId;
      let localSteps = JSON.parse(JSON.stringify(this.steps));
      localSteps.map(step => {
        step.rules.forEach((rule, index) => {
          let newRule = {};
          newRule.jsonRule = {
            conditions: rule.condition,
            event: {
              type: "goToNextStep",
              params: {
                stepId: rule.target.stepId
              }
            }
          }
          newRule.title = rule.title;
          newRule.description = rule.description;
          step.rules[index] = newRule;
        });
      });
      localSteps.map((x, index) => {
        x["level"] = this.getStepLevel(index);
      });
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        url: url,
        type: "POST",
        timeout: 5000,
        data: {
          title: self.title,
          description: self.description,
          languageCode: self.languageCode,
          steps: localSteps,
          variables: self.usedVariables,
          modelIds: self.modelIds
        },
        success: function (result) {
          if (result.hasOwnProperty("workflowId")) {
            self.$notify({
              title: "Save successful",
              text: "Your workflow has been successfully saved.",
              type: "success"
            });
            self.workflowId = Number(result.workflowId);
            var pathArray = location.href.split("/");
            window.history.pushState(window.history.state, "", pathArray[0] + "//" + pathArray[2] + "/designer?workflow=" + self.workflowId);
            let numberOfSteps = self.steps.length;
            for (let stepIndex = 0; stepIndex < numberOfSteps; stepIndex++) {
              self.steps[stepIndex].id = result.stepIds[stepIndex];
              cy.getElementById(self.steps[stepIndex].nodeId).style({
                label: self.steps[stepIndex].id
              });
              for (let apiIndex = 0; apiIndex < result.resultIds[stepIndex].length; apiIndex++) {
                let apiCall = result.resultIds[stepIndex][apiIndex];
                for (let resultIndex = 0; resultIndex < apiCall.length; resultIndex++) {
                  self.steps[stepIndex].apiCalls[apiIndex].results[resultIndex].databaseId = apiCall[resultIndex];
                }
              }
            }
            let varIds = result.variableIds;
            for (var key in varIds) {
              if (varIds.hasOwnProperty(key)) {
                self.usedVariables[key].databaseId = Number(varIds[key]);
              }
            }
            let optIds = result.optionIds;
            for (var key in optIds) {
              if (optIds.hasOwnProperty(key)) {
                optIds[key].forEach((element, index) => {
                  self.usedVariables[key].options[index].databaseId = Number(element);
                });
              }
            }
          }
        },
        error: function (xhr, textStatus, errorThrown) {
          self.$notify({
            title: "Saving failed",
            text: "Your workflow failed to save. Please try again later.",
            type: "error"
          });
          console.log(errorThrown);
        }
      });
    },

    /**
     * Load a Workflow from the database, as of now does nothing with the workflow
     * @param {Number} workflowId 
     */
    loadWorkflow(workflowId) {
      var self = this;
      this.isLoading = true;
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        url: "/designer/load/" + workflowId,
        type: "POST",
        data: {},
        success: function (result) {
          console.log("Workflow loaded: " + result.success);
          if (result.success) {
            let currentSteps = self.stepsChanged;
            let currentLevels = self.levelsChanged;
            self.title = result.title;
            self.description = result.description;
            self.languageCode = result.languageCode;
            result.steps.forEach((element, index) => {
              while (self.levels.length <= element.level)
                self.addLevel(self.levels.length);
              console.log("Index: " + index + ", #steps: " + self.steps.length);
              self.addStep(element.title, element.description, element.level);
              let localStep = self.steps[index];
              localStep.id = element.id;
              localStep.type = element.type;
              localStep.colour = element.colour;
              localStep.rules = [];
              if (localStep.type == "input") {
                localStep.variables = element.variables;
                localStep.varCounter = element.variables.length;
                element.rules.map(rule => {
                  localStep.rules.push(self.prepareSingleRule(rule));
                });
                localStep.apiCalls = element.apiCalls;
              } else {
                localStep.chartTypeNumber = Number(element.chartTypeNumber);
                localStep.chartItemReference = element.chartItemReference;
                localStep.chartRenderingData = element.chartRenderingData;
              }
            });
            if (result.usedVariables.constructor !== Array)
              self.usedVariables = result.usedVariables;
            self.recountVariableUses();
            self.stepsChanged = !currentSteps;
            self.levelsChanged = !currentLevels;
            self.LoadWorkflowLoadModels(result.evidencioModels);
            self.panView();
          } else {
            self.workflowId = null;
            Event.fire("normalStart");
          }
        },
        error: function (xhr, textStatus, errorThrown) {
          self.$notify({
            title: "Loading failed",
            text: "Your workflow failed to load. Please try again later.",
            type: "error"
          });
          self.isLoading = false;
          console.log(errorThrown);
        }
      });
    },

    /**
     * Prepares a rule loaded from the database for use in the designer
     * @param {Object} databaseRule 
     */
    prepareSingleRule(databaseRule) {
      return {
        title: databaseRule.title,
        description: databaseRule.description,
        target: databaseRule.target,
        condition: JSON.parse(databaseRule.jsonRule).conditions
      };
    },

    /**
     * Can be used to load an array of models to the workflow, will fire an event as soon as all the models are loaded.
     * @param {Array} models to load to the workflow
     */
    LoadWorkflowLoadModels(models) {
      $.when.apply($, models.map(element => {
        return this.loadModelEvidencio(element);
      })).then(function (x) {
        Event.fire("loadWorkflowAllModelsLoaded");
      }, function (e) {
        self.$notify({
          title: "Loading workflow failed",
          text: "Some requested information from Evidencio failed to arrive, loading failed.",
          type: "error"
        });
        console.log("At least one of the requests failed.");
        console.log(e);
      });
    },

    /**
     * Get the value of a URL parameter.
     * @param {String} name of the url parameter to get the value from
     * @return {string} The value that was found, null if none.
     */
    urlParam(name) {
      var results = new RegExp("[?&]" + name + "=([^]*)").exec(window.location.href);
      if (results == null) {
        return null;
      } else {
        return results[1] || 0;
      }
    },

    /**
     * Checks if a model is already loaded, to ensure models aren't loaded twice.
     * @param {Number} modelID of the model to be checked.
     * @return {Boolean} true if found, false if not
     */
    isModelLoaded(modelId) {
      return this.getLocalIdFromModelId(modelId) != -1;
    },

    /**
     * Returns the local index of an Evidencio model, -1 if it has not been loaded.
     * @param {Number} modelId 
     */
    getLocalIdFromModelId(modelId) {
      for (let index = 0; index < this.modelIds.length; index++) {
        if (this.modelIds[index] == modelId) return index;
      }
      return -1;
    },

    checkPossibleVariableMappingFailures() {
      let showNotification = false;
      this.steps.forEach((step, index) => {
        if (step.apiCalls.length > 0) {
          let reachableVars = this.getVariablesUpToStep(index).concat(step.variables);;
          if (reachableVars.length > 0) {
            let ifNotFound = reachableVars[0];
            step.apiCalls.forEach(apiCall => {
              apiCall.variables.forEach(variable => {
                if (this.getReachableIndex(variable.localVariable, reachableVars) == -1) {
                  variable.localVariable = ifNotFound;
                  showNotification = true;
                }
              });
            })
          } else {
            step.apiCalls = [];
          }
        }
      });
      if (showNotification)
        this.$notify({
          title: "Variable removed",
          text: "You have removed one or more variables that were used in a model-calculation, it is now replaced with another.",
          type: "warn"
        });
    },

    getVariablesUpToStep(localStepId) {
      let variables = [];
      this.getAncestorStepList(localStepId).forEach(stepId => {
        variables = variables.concat(this.steps[stepId].variables);
      });
      return variables;
    },

    /**
     * Finds the index in the reachables based on the local variable name
     * @param {String} varName
     */
    getReachableIndex(varName, reachableVariables) {
      for (let index = reachableVariables.length - 1; index >= 0; index--) {
        if (reachableVariables[index] == varName) return index;
      }
      return -1;
    },

    /**
     * Adds level to workflow. Levels contain one or more steps. The first level can contain at most one step.
     * @param {Number} index of position level should be added
     */
    addLevel(index) {
      this.levels.splice(index, 0, {
        steps: []
      });
      this.levelsChanged = !this.levelsChanged;
    },

    /**
     * Adds a level normally if no rules are broken due to the addition, otherwise asks for confirmation.
     * @param {Number} levelIndex is the location at which to add the level
     */
    addLevelConditional(levelIndex) {
      if (this.levelHasRule(levelIndex - 1)) {
        this.prepareConfirmDialog({
          title: "Add a level",
          message: "Adding a level at this height will remove some exising rules. Are you sure you wish to continue?\r\n Only the direct children of steps can be targets for a rule.",
          type: "addLevelRuleDeletion",
          data: levelIndex
        });
        this.callConfirmDialog();
      } else {
        this.addLevel(levelIndex);
      }
    },

    /**
     * Returns true if any of the steps in a level has a rule, false otherwise.
     * @param {Number} levelIndex of level to be checked
     */
    levelHasRule(levelIndex) {
      for (let indexStep = 0; indexStep < this.levels[levelIndex].steps.length; indexStep++) {
        if (this.steps[this.levels[levelIndex].steps[indexStep]].rules.length > 0)
          return true;
      }
      return false;
    },

    /**
     * Add step to workflow
     * @param {String} title of step
     * @param {String} description of step
     * @param {Number} level at which to add the step (it should exist!)
     */
    addStep(title, description, level) {
      this.steps.push({
        id: -1,
        title: title,
        description: description,
        nodeId: -1,
        colour: "#0099ff",
        type: "input",
        variables: [],
        varCounter: 0,
        rules: [],
        apiCalls: [],
        action: "create",
        chartTypeNumber: 0,
        chartRenderingData: {
          labels: [],
          datasets: [{
            backgroundColor: [],
            data: []
          }]
        },
        /*{
                 labels: ['January', 'February'],
                 datasets: [{
                   // label: "Edit Label",
                   backgroundColor: ['#0000ff', '#ff0000'],
                   data: [40, 20]
                 }]
               },*/
        chartItemReference: []
        /*[{
                    reference: "first",
                    negation: false
                  },
                  {
                    reference: "second",
                    negation: false
                  }
                ]*/
      });
      this.stepsChanged = !this.stepsChanged;
      this.levels[level].steps.push(this.steps.length - 1);
      if (this.levels[level].steps.length > this.maxStepsPerLevel)
        this.maxStepsPerLevel = this.levels[level].steps.length;
    },

    /**
     * Removes step (and node) given by step-id id.
     * @param {Number} id of step that should be removed. IMPORTANT: this should be the step-id, not the node-id
     */
    removeStep(id) {
      this.steps[id].action = "destroy";
      this.stepsChanged = !this.stepsChanged;
    },

    /**
     * Fit the viewport around the nodes shown.
     */
    fitView() {
      cy.fit();
    },

    /**
     * Pans the view to (approximately) the location of the nodes.
     */
    panView() {
      cy.pan({
        x: cy.width() / 2,
        y: cy.height() / 4
      });
    },

    /**
     * Positions the AddLevelButtons.
     */
    positionAddLevelButtons() {
      for (let index = 0; index < this.addLevelButtons.length; index++) {
        const element = this.addLevelButtons[index].nodeId;
        cy.getElementById(element).position({
          x: (this.maxStepsPerLevel / 2 + 1) * this.deltaX,
          y: (index + 0.5) * this.deltaY
        });
      }
    },

    /**
     * Positions the AddStepButtons.
     */
    positionAddStepButtons() {
      if (this.levels.length > 0) {
        for (let index = 0; index < this.addStepButtons.length; index++) {
          const element = this.addStepButtons[index].nodeId;
          cy.getElementById(element).position({
            x: (this.levels[index + 1].steps.length / 2 + (this.levels[index + 1].steps.length > 0 ? 0.5 : 0)) * this.deltaX,
            y: (index + 1) * this.deltaY
          });
        }
      }
    },

    /**
     * Positions the Steps.
     */
    positionSteps() {
      for (let indexLevel = 0; indexLevel < this.levels.length; indexLevel++) {
        const elementLevel = this.levels[indexLevel].steps;
        let left = -(elementLevel.length - 1) * this.deltaX / 2;
        for (let indexStep = 0; indexStep < elementLevel.length; indexStep++) {
          const elementStep = this.steps[elementLevel[indexStep]].nodeId;
          cy.getElementById(elementStep).position({
            x: left + indexStep * this.deltaX,
            y: indexLevel * this.deltaY
          });
        }
      }
    },

    /**
     * Returns the index of the AddLevelButton-node referred to by id.
     * @param {String} id of the node for which the index has to be found
     * @return {Number} index in button in array
     */
    getAddLevelButtonIndex(id) {
      for (let index = 0; index < this.addLevelButtons.length; index++) {
        const element = this.addLevelButtons[index].nodeId;
        if (element == id) {
          return index;
        }
      }
      return -1;
    },

    /**
     * Returns the index of the AddStepButton-node based on its id.
     * @param {String} id of the AddStepButton-node that the index is wanted of.
     * @return {Number} index of button in array
     */
    getAddStepButtonIndex(id) {
      for (let index = 0; index < this.addStepButtons.length; index++) {
        const element = this.addStepButtons[index].nodeId;
        if (element == id) {
          return index;
        }
      }
      return -1;
    },

    /**
     * Prepares the confirmation dialog for use.
     * @param {Object} confirmInfo contains the title, message, data and type of the dialog
     */
    prepareConfirmDialog(confirmInfo) {
      this.confirmDialog.title = confirmInfo.title;
      this.confirmDialog.message = confirmInfo.message;
      this.confirmDialog.data = confirmInfo.data;
      switch (confirmInfo.type) {
        case "removeStep":
          this.confirmDialog.approvalFunction = () => {
            this.removeStep(this.confirmDialog.data);
          }
          break;
        case "addLevelRuleDeletion":
          this.confirmDialog.approvalFunction = () => {
            let stepIds = this.levels[this.confirmDialog.data - 1].steps;
            for (let indexStep = 0; indexStep < stepIds.length; indexStep++) {
              this.steps[stepIds[indexStep]].rules.map(rule => {
                rule.action = "destroy";
              });
            }
            this.connectionsChanged = !this.connectionsChanged;
            this.addLevel(this.confirmDialog.data);
          }
          break;
      }
    },

    /**
     * Calls the confirmation dialog.
     */
    callConfirmDialog() {
      $("#confirmModal").modal();
    },

    /**
     * Opens an option-modal for the node that is clicked on.
     * @param {Object} nodeRef is the reference to the node that is clicked on.
     */
    prepareModal(nodeId) {
      this.selectedStepId = this.getStepIdFromNode(nodeId);
      this.modalChanged = !this.modalChanged;
    },

    /**
     * Saves the changes made to a step (variables added, etc.)
     * @param {Object} changedStep has the new step and usedVariables (with changes made in the modal)
     */
    applyChanges(changedStep) {
      changedStep.step.rules.map(rule => {
        if (rule.action == "none") rule.action = "change";
      });
      this.steps[this.selectedStepId] = changedStep.step;
      this.usedVariables = changedStep.usedVars;
      // Set new backgroundcolor
      cy.getElementById(this.steps[this.selectedStepId].nodeId).style({
        "background-color": changedStep.step.colour
      });
      this.recountVariableUses();
      this.checkPossibleVariableMappingFailures();
      this.connectionsChanged = !this.connectionsChanged;
    },

    /**
     * Returns the stepId based on the nodeId
     * @param {String} nodeId Node-Id of the node
     */
    getStepIdFromNode(nodeId) {
      for (let index = 0; index < this.steps.length; index++) {
        if (this.steps[index].nodeId == nodeId) return index;
      }
      return -1;
    },

    /**
     * Returns the stepId based on the databaseId
     * @param {Number} databaseId 
     */
    getStepIdFromDatabaseId(databaseId) {
      for (let index = 0; index < this.steps.length; index++) {
        if (this.steps[index].id == databaseId) return index;
      }
      return -1;
    },

    /**
     * Returns the key of a variable from the usedVariable object by its databaseId
     * @param {Number} databaseId 
     */
    getVariableKeyFromDatabaseId(databaseId) {
      for (var key in this.usedVariables) {
        if (this.usedVariables.hasOwnProperty(key)) {
          if (this.usedVariables[key].databaseId == databaseId) return key;
        }
      }
      return null;
    },

    /**
     * Returns the level (height in graph) of a step
     * @param {Number} stepIndex of step
     * @return {Number} the level at which a step is.
     */
    getStepLevel(stepIndex) {
      for (let levelIndex = 0; levelIndex < this.levels.length; levelIndex++) {
        const level = this.levels[levelIndex].steps;
        for (let index = 0; index < level.length; index++) {
          if (stepIndex == level[index]) return levelIndex;
        }
      }
      return -1;
    },

    /**
     * Removes the step from the level, if it exists.
     * @param {Number} stepIndex of step
     */
    removeStepFromLevel(stepIndex) {
      for (let levelIndex = 0; levelIndex < this.levels.length; levelIndex++) {
        const level = this.levels[levelIndex].steps;
        for (let index = level.length - 1; index >= 0; index--) {
          if (stepIndex == level[index]) {
            this.levels[levelIndex].steps.splice(index, 1);
          }
        }
        for (let index = level.length; index >= 0; index--) {
          if (level[index] > stepIndex) this.levels[levelIndex].steps[index]--;
        }
      }
      this.calculateMaxStepsPerLevel();
    },

    /**
     * Removes rules based on the target step
     * @param {Number} stepId of the target step of an edge/rule
     */
    removeRulesByTarget(stepId) {
      let levelIndex = this.getStepLevel(stepId) - 1;
      if (levelIndex >= 0) {
        this.levels[levelIndex].steps.forEach(stepIndex => {
          let currentRules = this.steps[levelIndex].rules;
          for (let ruleIndex = currentRules.length - 1; ruleIndex >= 0; ruleIndex--) {
            if (currentRules[ruleIndex].target.stepId == stepId)
              currentRules.splice(ruleIndex, 1);
          }
        });
      }
    },

    /**
     * Finds the ancestors of a step based on the rules (excludes itself)
     * @param {Number} stepId of the child to find ancestors of
     */
    getAncestorStepList(stepId) {
      let list = [];
      let previousLevel = this.getStepLevel(stepId) - 1;
      if (previousLevel < 0)
        return list;
      this.levels[previousLevel].steps.forEach(stId => {
        this.steps[stId].rules.forEach(rule => {
          if (rule.target.stepId == stepId)
            list = list.concat(this.getAncestorStepListHelper(stId));
        });
      });
      return this.arrayUnique(list);
    },

    getAncestorStepListHelper(stepId) {
      let list = [stepId];
      let previousLevel = this.getStepLevel(stepId) - 1;
      if (previousLevel < 0)
        return list;
      this.levels[previousLevel].steps.forEach(stId => {
        this.steps[stId].rules.forEach(rule => {
          if (rule.target.stepId == stepId)
            list = list.concat(this.getAncestorStepListHelper(stId));
        });
      });
      return list;
    },

    /**
     * Removes duplicate items from an array.
     * @param {Array} array 
     */
    arrayUnique(array) {
      var a = array.concat();
      for (var i = 0; i < a.length; ++i) {
        for (var j = i + 1; j < a.length; ++j) {
          if (a[i] === a[j])
            a.splice(j--, 1);
        }
      }
      return a;
    },

    /**
     * Calculates the maximum number of steps per level.
     */
    calculateMaxStepsPerLevel() {
      this.maxStepsPerLevel = 0;
      this.levels.forEach(element => {
        if (element.steps.length > this.maxStepsPerLevel)
          this.maxStepsPerLevel = element.steps.length;
      });
    },

    /**
     * Recounts the number of times a variable is used, to be used whenever this changes.
     */
    recountVariableUses() {
      this.timesUsedVariables = {};
      this.models.forEach(element => {
        element.variables.forEach(variable => {
          this.timesUsedVariables[variable.id.toString()] = 0;
        });
      });

      for (let indexStep = 0; indexStep < this.steps.length; indexStep++) {
        const elementStep = this.steps[indexStep];
        if (elementStep.type == "input") {
          for (let indexVariable = 0; indexVariable < elementStep.variables.length; indexVariable++) {
            const element = elementStep.variables[indexVariable];
            this.timesUsedVariables[this.usedVariables[element].id.toString()] += 1;
          }
        }
      }
    },

    /**
     * Changes the details of the currently loaded workflow.
     * @param {Object} newDetails contains an object with the new details (fiels title, description)
     */
    changeWorkflowDetails(newDetails) {
      this.title = newDetails.title;
      this.description = newDetails.description;
    }
  },

  watch: {
    /**
     * stepsChanged is used to indicate if a step has been set to be created or removed, this function does the actual work.
     */
    stepsChanged: function () {
      for (let index = this.steps.length - 1; index >= 0; index--) {
        let currentStep = this.steps[index];
        switch (currentStep.action) {
          case "create":
            currentStep.nodeId = cy.add({
              classes: "node",
              data: {
                id: "node_" + this.nodeCounter
              },
              style: {
                "background-color": currentStep.colour
              }
            }).id();
            currentStep.create = false;
            cy.getElementById(currentStep.nodeId).style({
              label: currentStep.id
            });
            this.nodeCounter++;
            currentStep.action = "none";
            break;
          case "destroy":
            cy.remove(cy.getElementById(currentStep.nodeId));
            this.removeRulesByTarget(index);
            this.removeStepFromLevel(index);
            this.steps.splice(index, 1);
            currentStep.action = "none";
            break;
        }
      }
      this.positionSteps();
      this.positionAddStepButtons();
      this.positionAddLevelButtons();
    },

    /**
     * levelsChanged is used to indicate if a level has been added or removed, this function does the actual work
     */
    levelsChanged: function () {
      while (this.levels.length > this.addLevelButtons.length) {
        if (this.addLevelButtons.length > 0) {
          this.addStepButtons.push({
            nodeId: cy.add({
              classes: "buttonAddStep"
            }).id()
          });
        }
        this.addLevelButtons.push({
          nodeId: cy.add({
            classes: "buttonAddLevel"
          }).id()
        });
      }
      while (this.levels.length < this.addLevelButtons.length) {
        cy.remove(this.addLevelButtons.pop().nodeId);
        cy.remove(this.addStepButtons.pop().nodeId);
      }
      this.positionAddLevelButtons();
      this.positionAddStepButtons();
      this.positionSteps();
    },

    /**
     * Adds/removes/changes rules in required
     */
    connectionsChanged: function () {
      this.steps.forEach((step, index) => {
        for (let index = step.rules.length - 1; index >= 0; index--) {
          let currentRule = step.rules[index];
          switch (currentRule.action) {
            case "create":
              let source = step.nodeId;
              let target = this.steps[currentRule.target.stepId].nodeId;
              currentRule.edgeId = cy.add({
                classes: "edge",
                data: {
                  id: "edge_" + this.edgeCounter,
                  source: source,
                  target: target
                }
              }).id();
              this.edgeCounter++;
              currentRule.action = "none";
              break;
            case "change":
              let newTarget = this.steps[currentRule.target.stepId].nodeId;
              cy.getElementById(currentRule.edgeId).move({
                target: newTarget
              });
              currentRule.action = "none";
              break;
            case "destroy":
              cy.remove(cy.getElementById(currentRule.edgeId));
              step.rules.splice(index, 1);
              break;
          }
        }
      });
    }
  }
});