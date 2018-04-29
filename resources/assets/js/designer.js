window.cytoscape = require("cytoscape");
window.Vue = require("vue");
require("./event-dispatcher.js");
Vue.component("vueMultiselect", window.VueMultiselect.default);
Vue.component("variableViewList", require("./components/VariableViewList.vue"));
Vue.component("modalStep", require("./components/ModalStep.vue"));
window.cyCanvas = require("cytoscape-canvas");

// ============================================================================================= //

/* Step-template:
    {-l
        id: -1,
        title: title,
        description: description,
        nodeId: -1,
        color: '#0099ff',
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
vObj = new Vue({
  el: "#designerDiv",
  data: {
    modelLoaded: false,
    models: [],
    modelIds: [],
    numVariables: 0,
    usedVariables: {},
    timesUsedVariables: [],

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
    modalChanged: false
  },

  created() {
    Event.listen("modelLoad", modelId => {
      this.loadModelEvidencio(modelId);
    });
    this.addLevel(0);
    this.addStep(
      "Starter step",
      "First step in the model shown to the patient. Change this step to fit your needs.",
      0
    );
  },

  /**
   * This function adds the first basic level and the first step.
   */
  mounted: function() {
    // this.addLevel(0);
    // this.addStep(
    //   "Starter step",
    //   "First step in the model shown to the patient. Change this step to fit your needs.",
    //   0
    // );
  },

  computed: {
    allVariables: function() {
      if (this.modelLoaded) {
        var allvars = [];
        this.models.forEach(element => {
          allvars = allvars.concat(element.variables);
        });
        return allvars;
      } else return [];
    },

    possibleVariables: function() {
      if (this.modelLoaded) {
        deepCopy = JSON.parse(JSON.stringify(this.models));
        let counter = 0;
        deepCopy.forEach(element => {
          element.variables.map((x, index) => (x["ind"] = counter + index));
          counter += element.variables.length;
        });
        return deepCopy;
      }
      return [];
    },

    childrenNodes: function() {
      if (this.selectedStepId == -1) return [];
      let levelIndex = this.getStepLevel(this.selectedStepId);
      if (levelIndex == -1 || levelIndex == this.levels.length - 1) return [];
      let options = [];
      this.levels[levelIndex + 1].steps.forEach(element => {
        options.push({
          stepId: element,
          title: this.steps[element].title,
          id: this.steps[element].id,
          color: this.steps[element].color,
          ind: options.length
        });
      });
      return options;
    },

    modelChoiceRepresentation: function() {
      let representation = [];
      this.models.forEach(element => {
        representation.push({
          title: element.title,
          id: element.id
        });
      });
      return representation;
    },

    editedVariables: function() {
      let editedVars = [];
      for (var key in this.usedVariables) {
        if (this.usedVariables.hasOwnProperty(key)) {
          editedVars.push(this.usedVariables[key]);
        }
      }
      return editedVars;
    }
  },

  methods: {
    /**
     * Load model from Evidencio API, model is identified using variable modelID
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
          success: function(result) {
            self.models.push(JSON.parse(result));
            let newVars = self.models[self.models.length - 1].variables.length;
            self.numVariables += newVars;
            self.modelLoaded = true;
            self.timesUsedVariables = self.timesUsedVariables.concat(
              Array.apply(null, Array(newVars)).map(Number.prototype.valueOf, 0)
            );
            self.modelIds.push(modelId);
          }
        });
      }
    },

    /**
     * Checks if a model is already loaded, to ensure models aren't loaded twice.
     * @param {integer} [modelID] of the model to be checked.
     */
    isModelLoaded(modelId) {
      for (let index = 0; index < this.modelIds.length; index++) {
        if (this.modelIds[index] == modelId) return true;
      }
      return false;
    },
    /**
     * Adds level to workflow. Levels contain one or more steps. The first level can contain at most one step.
     * @param {integer} [index] of position level should be added
     */
    addLevel(index) {
      this.levels.splice(index, 0, {
        steps: []
      });
      this.levelsChanged = !this.levelsChanged;
    },

    /**
     * Add step to workflow
     * @param {string} [title] of step
     * @param {string} [description] of step
     */
    addStep(title, description, level) {
      this.steps.push({
        id: -1,
        title: title,
        description: description,
        nodeId: -1,
        color: "#0099ff",
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
     * @param {integer} [id] of step that should be removed. IMPORTANT: this should be the step-id, not the node-id
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
            x:
              (this.levels[index + 1].steps.length / 2 +
                (this.levels[index + 1].steps.length > 0 ? 0.5 : 0)) *
              this.deltaX,
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
     * @param {string} [id] of the node for which the index has to be found
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
     * @param {string} [id] of the AddStepButton-node that the index is wanted of.
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
     * @param {object} [nodeRef] is the reference to the node that is clicked on.
     */
    prepareModal(nodeRef) {
      this.selectedStepId = nodeRef.scratch("_nodeId");
      this.modalChanged = !this.modalChanged;
      // let step = this.steps[this.modalnodeId];
      // this.modalDatabaseStepID = step.id;
      // this.modalStepType = step.type;
      // this.modalSelectedColor = step.color;
      // this.modalSelectedVariables = step.variables.slice();
      // this.modalVarCounter = step.varCounter;
      // this.modalUsedVariables = JSON.parse(JSON.stringify(this.usedVariables));
      // this.modalRules = JSON.parse(JSON.stringify(step.rules));
      // this.modalEditRuleFlags = new Array(this.modalRules.length).fill(false);
      // this.modalApiCall = JSON.parse(JSON.stringify(step.apiCall));
      // this.setSelectedVariables();
    },

    /**
     * Saves the changes made to a step (variables added, etc.)
     */
    applyChanges(changedStep) {
      this.steps[this.selectedStepId] = changedStep.step;
      this.usedVariables = changedStep.usedVars;

      // Set new backgroundcolor
      cy.getElementById(this.steps[this.selectedStepId].nodeId).style({
        "background-color": changedStep.step.color
      });
      // step.color = this.modalSelectedColor;
      // // Set (new) step-type
      // step.type = this.modalStepType;
      // // Set (new) variables
      // step.variables = this.modalSelectedVariables.slice();
      // step.varCounter = this.modalVarCounter;
      // this.usedVariables = JSON.parse(JSON.stringify(this.modalUsedVariables));
      // // Set (new) rules
      // step.rules = JSON.parse(JSON.stringify(this.modalRules));
      // // Set (new) API-call
      // step.apiCall = JSON.parse(JSON.stringify(this.modalApiCall));
      // // Recount variable uses
      // this.modalSelectedVariables = [];
      this.recountVariableUses();
    },

    /**
     * Returns the level (height in graph) of a step
     * @param {integer} [stepIndex] of step
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
      this.timesUsedVariables = Array.apply(null, Array(this.numVariables)).map(
        Number.prototype.valueOf,
        0
      );
      for (let indexStep = 0; indexStep < this.steps.length; indexStep++) {
        const elementStep = this.steps[indexStep];
        if (elementStep.type == "input") {
          for (
            let indexVariable = 0;
            indexVariable < elementStep.variables.length;
            indexVariable++
          ) {
            const element = elementStep.variables[indexVariable];
            this.timesUsedVariables[this.usedVariables[element].ind] += 1;
          }
        }
      }
    }
  },

  watch: {
    /**
     * stepsChanged is used to indicate if a step has been set to be created or removed, this function does the actual work.
     */
    stepsChanged: function() {
      for (let index = 0; index < this.steps.length; index++) {
        if (this.steps[index].create) {
          this.steps[index].nodeId = cy
            .add({
              classes: "node",
              data: {
                id: "node_" + this.nodeCounter
              },
              scratch: {
                _nodeId: index
              },
              style: {
                "background-color": this.steps[index].color
              }
            })
            .id();
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
    levelsChanged: function() {
      while (this.levels.length > this.addLevelButtons.length) {
        if (this.addLevelButtons.length > 0) {
          this.addStepButtons.push({
            nodeId: cy
              .add({
                classes: "buttonAddStep"
              })
              .id()
          });
        }
        this.addLevelButtons.push({
          nodeId: cy
            .add({
              classes: "buttonAddLevel"
            })
            .id()
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

    selectedVariables: function() {
      this.recountVariableUses();
    }
  }
});

// ============================================================================================= //

var cy = cytoscape({
  container: $("#graph"),
  style: [
    // the stylesheet for the graph
    {
      selector: ".node",
      style: {
        label: "data(id)",
        shape: "roundrectangle",
        width: "100px",
        height: "100px",
        "background-color": "#0099ff",
        "border-color": " #000000",
        "border-width": "4px",
        "text-halign": "center",
        "text-valign": "center",
        color: "#ffffff",
        "font-size": "24px",
        "text-outline-color": "#000000",
        "text-outline-width": "1px"
      }
    },

    {
      selector: ".edge",
      style: {
        width: 3,
        "line-color": "#ccc",
        "target-arrow-color": "#ccc",
        "target-arrow-shape": "triangle"
      }
    },

    {
      selector: ".buttonAddLevel",
      style: {
        label: "",
        width: "75px",
        height: "75px",
        "background-color": "#46c637",
        "border-color": "#1f6b17",
        "border-width": "4px",
        "background-image": "/images/plus.svg",
        "background-width": "50%",
        "background-height": "50%"
      }
    },

    {
      selector: ".buttonAddStep",
      style: {
        label: "",
        width: "75px",
        height: "75px",
        "background-color": "#00a5ff",
        "border-color": "#0037ff",
        "border-width": "4px",
        "background-image": "/images/plus.svg",
        "background-width": "50%",
        "background-height": "50%"
      }
    }
  ],

  autoungrabify: true,
  autounselectify: true,

  layout: {
    name: "preset"
  }
});

cy.on("tap", "node", function(evt) {
  let ref = evt.target;
  if (ref.hasClass("buttonAddLevel")) {
    let nID = vObj.getAddLevelButtonIndex(ref.id());
    if (nID != -1) vObj.addLevel(nID + 1);
  } else if (ref.hasClass("buttonAddStep")) {
    let nID = vObj.getAddStepButtonIndex(ref.id());
    if (nID != -1)
      vObj.addStep("Default title", "Default description", nID + 1);
  } else if (ref.hasClass("node")) {
    vObj.prepareModal(ref);
    $("#modalStep").modal();
  }
});

// ============================================================================================= //

//Canvas of background
const bottomLayer = cy.cyCanvas({
  zIndex: -1
});
const canvas = bottomLayer.getCanvas();
const ctx = canvas.getContext("2d");
cy.on("render cyCanvas.resize", function(evt) {
  bottomLayer.resetTransform(ctx);
  bottomLayer.clear(ctx);
  bottomLayer.setTransform(ctx);
  ctx.save();
  for (var i = 0; i < vObj.levels.length; i++) {
    if (i % 2 == 0) ctx.fillStyle = "#e3e7ed";
    else ctx.fillStyle = "#c6cad1";
    let w = vObj.maxStepsPerLevel / 2 * vObj.deltaX;
    ctx.fillRect(
      -w - 500,
      i * vObj.deltaY - vObj.deltaY / 2,
      2 * w + 1000,
      vObj.deltaY
    );
  }
  ctx.restore();
});

// ============================================================================================= //

window.onload = function() {
  vObj.fitView();
};
