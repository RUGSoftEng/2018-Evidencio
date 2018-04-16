window.cytoscape = require('cytoscape');
window.Vue = require('vue');
Vue.component('vue-multiselect', window.VueMultiselect.default)
window.cyCanvas = require('cytoscape-canvas');

// ============================================================================================= //


    /* Step-template:
    {
        id: -1,
        title: title,
        description: description,
        nodeID: -1,
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
  el: '#designerDiv',
  data: {
    modelLoaded: false,
    modelID: 0,
    model: [],
    variablesUsed: [],

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

    modalNodeID: -1,    //ID in vue steps-array
    nodeID: -1,         //ID in database
    stepType: 'input',
    selectedVariables: [],
    rules: [],
    editVariableFlags: [],
    selectedColor: '#000000'
  },

  /**
   * This function adds the first basic level and the first step.
   */
  mounted: function() {
    this.addLevel(0);
    this.addStep('Starter step', 'First step in the model shown to the patient. Change this step to fit your needs.', 0);
  },

  computed: {
    possibleVariables: function() {
      if (this.modelLoaded) {
        var deepCopy = JSON.parse(JSON.stringify(this.model.variables))
        deepCopy.map((x, index)=>x['ind'] = index);
        return deepCopy;
    } else
        return [];
    },

    childrenNodes: function() {
      if (this.modalNodeID == -1)
        return [];
      else {
        let levelIndex = this.getStepLevel(this.modalNodeID);
        if (levelIndex == -1 || levelIndex == this.levels.length-1)
          return [];
        let options = []
        this.levels[levelIndex+1].steps.forEach(element => {
          options.push({
            stepID: element,
            title: this.steps[element].title,
            id: this.steps[element].id,
            color: this.steps[element].color
          });
        });
        return options;
      }
    }

  },

  methods: {
    
    /**
     * Load model from Evidencio API, model is identified using variable modelID
     */
    loadModelEvidencio() {
      var self = this;
      $.ajax({
        url: '/designer/fetch',
        data: {
          modelID: self.modelID
        },
        success: function(result) {
          self.model = JSON.parse(result);
          self.modelLoaded = true;
          self.variablesUsed = Array.apply(null, Array(self.model.variables.length)).map(Number.prototype.valueOf,0);
          self.editVariableFlags = new Array(self.model.variables.length).fill(false);
        }
      });
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
        nodeID: -1,
        color: '#0099ff',
        type: 'input',
        variables: [],
        rules: [],
        widgetType: 'pieChart',
        create: true,
        destroy: false
      });
      this.stepsChanged = !this.stepsChanged;
      this.levels[level].steps.push(this.steps.length-1);
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
        const element = this.addLevelButtons[index].nodeID;
        cy.getElementById(element).position({
          x: (this.maxStepsPerLevel/2 + 1) * this.deltaX,
          y: (index+0.5) * this.deltaY
        })
      }
    },

    /**
     * Positions the AddStepButtons.
     */
    positionAddStepButtons() {
      if (this.levels.length > 0) {
        for (let index = 0; index < this.addStepButtons.length; index++) {
          const element = this.addStepButtons[index].nodeID;
          cy.getElementById(element).position({
            x: (this.levels[index+1].steps.length/2 + (this.levels[index+1].steps.length>0?0.5:0)) * this.deltaX,
            y: (index+1) * this.deltaY
          })
        }
      }
    },

    /**
     * Positions the Steps.
     */
    positionSteps() {
      for (let indexLevel = 0; indexLevel < this.levels.length; indexLevel++) {
        const elementLevel = this.levels[indexLevel].steps;
        let left = (-(elementLevel.length-1) * this.deltaX)/2;
        for (let indexStep = 0; indexStep < elementLevel.length; indexStep++) {
          const elementStep = this.steps[elementLevel[indexStep]].nodeID;
          cy.getElementById(elementStep).position({
            x: left + indexStep * this.deltaX,
            y: indexLevel * this.deltaY
          })
          
        }
      }

    },

    /**
     * Returns the index of the AddLevelButton-node referred to by id.
     * @param {string} [id] of the node for which the index has to be found 
     */
    getAddLevelButtonIndex(id) {
      for (let index = 0; index < this.addLevelButtons.length; index++) {
        const element = this.addLevelButtons[index].nodeID;
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
        const element = this.addStepButtons[index].nodeID;
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
      this.modalNodeID = nodeRef.scratch('_nodeID');
      let step = this.steps[this.modalNodeID];
      this.nodeID = step.id;
      this.stepType = step.type;
      this.selectedVariables = JSON.parse(JSON.stringify(step.variables));
      this.rules = JSON.parse(JSON.stringify(step.rules));
      this.selectedColor = step.color;
    },

    /**
     * Returns the text shown when more than the limit of options are selected.
     * @param {integer} [count] is the number of not-shown options.
     */
    multiselectVariablesText(count) {
      return ' and ' + count + ' other variable(s)';
    },

    /**
     * Saves the changes made to a step (variables added, etc.)
     */
    saveChanges() {
      // Set new backgroundcolor
      cy.getElementById(this.steps[this.modalNodeID].nodeID).style({
        'background-color': this.selectedColor
      });
      this.steps[this.modalNodeID].color = this.selectedColor; 
      // Reset flags
      for (let index = 0; index < this.editVariableFlags.length; index++) 
        this.editVariableFlags[index] = false;
      // Set (new) step-type
      this.steps[this.modalNodeID].type = this.stepType;
      // Set (new) variables
      this.steps[this.modalNodeID].variables = JSON.parse(JSON.stringify(this.selectedVariables));
      // Recount variable uses
      if (this.modelLoaded) {
        this.variablesUsed = Array.apply(null, Array(this.model.variables.length)).map(Number.prototype.valueOf,0);
        for (let indexStep = 0; indexStep < this.steps.length; indexStep++) {
          const elementStep = this.steps[indexStep];
          if (elementStep.type == 'input') {
            for (let indexVariable = 0; indexVariable < elementStep.variables.length; indexVariable++) {
              const element = elementStep.variables[indexVariable].ind;
              this.variablesUsed[element] += 1;             
            }
          }
        }
      }
    },

    /**
     * Returns a check-image if the image is set to be edited, pencil-image if not.
     * @param {integer} [index] of the variable
     */
    getImage(indicator) {
      if (indicator)
        return '/images/check.svg';
      else
        return '/images/pencil.svg';
    },

    /**
     * Allow for titel/description/etc. of variable to be changed. Mainly used to make it less likely to happen accidentally.
     * @param {index} index 
     */
    editVariable(index) {
      Vue.set(this.editVariableFlags, index, !this.editVariableFlags[index]);
    },

    addRule() {
      this.rules.push({
        name: 'Go to target',
        rule: [],
        target: -1,
        editing: true
      });
    },

    removeRule(ruleIndex) {
      this.rules.splice(ruleIndex, 1);
    },

    editRule(index) {
      this.rules[index].editing = !this.rules[index].editing;
    },

    getStepLevel(stepIndex) {
      for (let levelIndex = 0; levelIndex < this.levels.length; levelIndex++) {
        const level = this.levels[levelIndex].steps;
        for (let index = 0; index < level.length; index++) {
          if (stepIndex == level[index])
            return levelIndex;          
        }
      }
      return -1;
    },
    
    customLabel ({ desc }) {
      return `${desc}`
    },

    changeStepType(stepIndex, type) {
      this.steps[stepIndex].type = type;
    }
    
  },

  watch: {
    /**
     * stepsChanged is used to indicate if a step has been set to be created or removed, this function does the actual work.
     */
    stepsChanged: function() {
      for (let index = 0; index < this.steps.length; index++) {
        if (this.steps[index].create) {
          this.steps[index].nodeID = cy.add({
            classes: 'node',
            data: {
              id: 'node_' + this.nodeCounter
            },
            scratch: {
              '_nodeID': index
            },
            style: {
              'background-color': this.steps[index].color
            }
          }).id();
          this.steps[index].create = false;
          cy.getElementById(this.steps[index].nodeID).style({
            'label': this.steps[index].id
          })
          this.nodeCounter++;
        }
        if (this.steps[index].destroy) {
          cy.remove(this.steps[index].nodeID);
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
            nodeID: cy.add({
              classes: 'buttonAddStep'
            }).id()
          });
        }
        this.addLevelButtons.push({
          nodeID: cy.add({
            classes: 'buttonAddLevel'
          }).id()
        });
      }
      while (this.levels.length < this.addLevelButtons.length) {
        cy.remove(this.addLevelButtons.pop().nodeID);
        cy.remove(this.addStepButtons.pop().nodeID);
      }
      this.positionAddLevelButtons();
      this.positionAddStepButtons();
      this.positionSteps();
    }

  }
    
});

// ============================================================================================= //

var cy = cytoscape({
  container: $('#graph'),
  style: [ // the stylesheet for the graph
    {
      selector: '.node',
      style: {
        'label': 'data(id)',
        'shape': 'roundrectangle',
        'width': '100px',
        'height': '100px',
        'background-color': '#0099ff',
        'border-color': ' #000000',
        'border-width': '4px',
        'text-halign': 'center',
        'text-valign': 'center',
        'color': '#ffffff',
        'font-size': '24px',
        'text-outline-color': '#000000',
        'text-outline-width': '1px'
      }
    },

    {
      selector: '.edge',
      style: {
        'width': 3,
        'line-color': '#ccc',
        'target-arrow-color': '#ccc',
        'target-arrow-shape': 'triangle'
      }
    },

    {
      selector: '.buttonAddLevel',
      style: {
        'label': '',
        'width': '75px',
        'height': '75px',
        'background-color': '#46c637',
        'border-color': '#1f6b17',
        'border-width': '4px',
        'background-image': '/images/plus.svg',
        'background-width': '50%',
        'background-height': '50%'
      }
    },

    {
      selector: '.buttonAddStep',
      style: {
        'label': '',
        'width': '75px',
        'height': '75px',
        'background-color': '#00a5ff',
        'border-color': '#0037ff',
        'border-width': '4px',
        'background-image': '/images/plus.svg',
        'background-width': '50%',
        'background-height': '50%'
      }
    }

  ],

  autoungrabify: true,
  autounselectify: true,

  layout: {
    name: 'preset'
  }
})

cy.on('tap', 'node', function(evt) {
  let ref = evt.target;
  if (ref.hasClass('buttonAddLevel')) {
    let nID = vObj.getAddLevelButtonIndex(ref.id());
    if (nID != -1)
      vObj.addLevel(nID+1);
  } else if (ref.hasClass('buttonAddStep')) {
    let nID = vObj.getAddStepButtonIndex(ref.id());
    if (nID != -1)
      vObj.addStep('Default title', 'Default description', nID + 1);
  } else if (ref.hasClass('node')) {
    vObj.prepareModal(ref);
    $('#modalStep').modal();
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
  for (var i = 0; i < vObj.levels.length; i++){
    if (i%2 == 0)
      ctx.fillStyle = "#e3e7ed";
    else
      ctx.fillStyle = "#c6cad1";
    let w = (vObj.maxStepsPerLevel/2)*vObj.deltaX;
    ctx.fillRect(-w-500, i*vObj.deltaY-(vObj.deltaY/2), 2*w+1000, vObj.deltaY);
  }
  ctx.restore();
});

// ============================================================================================= //

window.onload = function() {
  vObj.fitView();
  //yaSimpleScrollbar.attach(document.getElementById('modalCard'));
}

// ============================================================================================= //

$('#colorPalette').colorPalette().on('selectColor', function(evt) {
  vObj.selectedColor = evt.color;
});