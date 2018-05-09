require("./event-dispatcher.js");
Vue.component("vueMultiselect", window.VueMultiselect.default);
Vue.component("workflowInformation", require("./components/WorkflowInformation.vue"));
Vue.component("variableViewList", require("./components/VariableViewList.vue"));
Vue.component("modalStep", require("./components/ModalStep.vue"));

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
    nodeCounter: 0,

    deltaX: 150,
    deltaY: 250,
    addLevelButtons: [],
    addStepButtons: [],

    selectedStepId: 0,
    modalChanged: false,

    workflowId: null
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
      this.fitView();
    });
    // Event called when the user tries to load an Evidencio model
    Event.listen("modelLoad", modelId => {
      this.loadModelEvidencio(modelId);
    });
    // Event called when the user tries to save a workflow
    Event.listen("save", () => {
      this.saveWorkflow();
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

    // Deep-copy of the models and variables, used for MultiSelect
    possibleVariables: function () {
      if (this.modelLoaded) {
        deepCopy = JSON.parse(JSON.stringify(this.models));
        return deepCopy;
      }
      return [];
    },

    // Array containing children of currently selected step
    childrenNodes: function () {
      if (this.selectedStepId == -1) return [];
      let levelIndex = this.getStepLevel(this.selectedStepId);
      if (levelIndex == -1 || levelIndex == this.levels.length - 1) return [];
      let options = [];
      this.levels[levelIndex + 1].steps.forEach(element => {
        options.push({
          stepId: element,
          title: this.steps[element].title,
          id: this.steps[element].id,
          colour: this.steps[element].colour,
          ind: options.length
        });
      });
      return options;
    },

    // Array of model-representations for API-call
    modelChoiceRepresentation: function () {
      let representation = [];
      this.models.forEach(element => {
        representation.push({
          title: element.title,
          id: element.id
        });
      });
      return representation;
    }
  },

  methods: {
    /**
     * Load model from Evidencio API, Model is prepared for later saving.
     * @param {Number} -> modelId is the id of the Evidencio model that should be loaded.
     */
    loadModelEvidencio(modelId) {
      var self = this;
      if (!this.isModelLoaded(modelId)) {
        $.ajax({
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
          },
          url: "/designer/fetch",
          type: "POST",
          data: {
            modelId: modelId
          },
          success: function (result) {
            self.debug = result;
            self.models.push(JSON.parse(result));
            let newVars = self.models[self.models.length - 1].variables.length;
            self.numVariables += newVars;
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
          }
        });
      }
    },

    /**
     * Save Workflow in database, IDs of saved data are set after saving.
     */
    saveWorkflow() {
      var self = this;
      let url = "/designer/save";
      if (this.workflowId != null) url = url + "/" + this.workflowId;
      this.steps.map((x, index) => {
          x["level"] = this.getStepLevel(index);
        }),
        $.ajax({
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
          },
          url: url,
          type: "POST",
          data: {
            title: self.title,
            description: self.description,
            languageCode: self.languageCode,
            steps: self.steps,
            variables: self.usedVariables,
            modelIds: self.modelIds
          },
          success: function (result) {
            self.workflowId = Number(result.workflowId);
            let numberOfSteps = self.steps.length;
            for (let index = 0; index < numberOfSteps; index++) {
              self.steps[index].id = result.stepIds[index];
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
        });
    },

    /**
     * Load a Workflow from the database, as of now does nothing with the workflow
     * @param {Number} workflowId 
     */
    loadWorkflow(workflowId) {
      var self = this;
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
            result.evidencioModels.forEach(element => {
              self.loadModelEvidencio(element);
            });
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
              self.steps[index].id = element.id;
              self.steps[index].colour = element.colour;
              self.steps[index].variables = element.variables;
            });
            if (result.usedVariables.constructor !== Array)
              self.usedVariables = result.usedVariables;
            self.recountVariableUses();
            self.stepsChanged = !currentSteps;
            self.levelsChanged = !currentLevels;
          } else {
            self.workflowId = null;
            Event.fire("normalStart");
          }
        }
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
      for (let index = 0; index < this.modelIds.length; index++) {
        if (this.modelIds[index] == modelId) return true;
      }
      return false;
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
        apiCall: {
          model: null,
          variables: []
        },
        create: true,
        destroy: false
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
      this.steps[id].destroy = true;
      this.stepsChanged = !this.stepsChanged;
    },

    /**
     * Fit the viewport around the nodes shown.
     */
    fitView() {
      cy.fit();
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
     * Opens an option-modal for the node that is clicked on.
     * @param {Object} nodeRef is the reference to the node that is clicked on.
     */
    prepareModal(nodeRef) {
      this.selectedStepId = nodeRef.scratch("_nodeId");
      this.modalChanged = !this.modalChanged;
    },

    /**
     * Saves the changes made to a step (variables added, etc.)
     * @param {Object} changedStep has the new step and usedVariables (with changes made in the modal)
     */
    applyChanges(changedStep) {
      this.steps[this.selectedStepId] = changedStep.step;
      this.usedVariables = changedStep.usedVars;

      // Set new backgroundcolor
      cy.getElementById(this.steps[this.selectedStepId].nodeId).style({
        "background-color": changedStep.step.colour
      });
      this.recountVariableUses();
    },

    /**
     * Returns the level (height in graph) of a step
     * @param {integer} stepIndex of step
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
      for (let index = 0; index < this.steps.length; index++) {
        if (this.steps[index].create) {
          this.steps[index].nodeId = cy.add({
            classes: "node",
            data: {
              id: "node_" + this.nodeCounter
            },
            scratch: {
              _nodeId: index
            },
            style: {
              "background-color": this.steps[index].colour
            }
          }).id();
          this.steps[index].create = false;
          cy.getElementById(this.steps[index].nodeId).style({
            label: this.steps[index].id
          });
          this.nodeCounter++;
        }
        if (this.steps[index].destroy) {
          cy.remove(this.steps[index].nodeId);
          this.steps.splice(index, 1);
        }
      }
      this.positionAddStepButtons();
      this.positionAddLevelButtons();
      this.positionSteps();
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

    selectedVariables: function () {
      this.recountVariableUses();
    }
  }
});