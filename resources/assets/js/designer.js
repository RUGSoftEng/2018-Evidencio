window.cytoscape = require('cytoscape');
window.Vue = require('vue');
Vue.component('vue-multiselect', window.VueMultiselect.default)
window.cyCanvas = require('cytoscape-canvas');

// ============================================================================================= //


    /* Step-template:
    {
      title: '',
      description: '',
      nodeID: {id},
      variables: [],
      ...
      create: true,
      destroy: false
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

    modalNodeID: -1,
    selectedVariables: []

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
        this.model.variables.map((x, index)=>x['ind'] = index);
        return this.model.variables;
    } else
        return [];
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
        variables: [],
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
      this.selectedVariables = this.steps[this.modalNodeID].variables;
    },

    /**
     * Returns the text shown when more than the limit of options are selected.
     * @param {integer} [count] is the number of not-shown options.
     */
    multiselectVariablesText(count) {
      return ' and ' + count + ' other variable(s)';
    },

    saveChanges() {
      this.steps[this.modalNodeID].variables = this.selectedVariables;
      this.variablesUsed = Array.apply(null, Array(this.model.variables.length)).map(Number.prototype.valueOf,0);
      for (let indexStep = 0; indexStep < this.steps.length; indexStep++) {
        const elementStep = this.steps[indexStep];
        for (let indexVariable = 0; indexVariable < elementStep.variables.length; indexVariable++) {
          const element = elementStep.variables[indexVariable].ind;
          this.variablesUsed[element] += 1;             
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
          this.steps[index].nodeID = cy.add({
            classes: 'node',
            data: {
              id: 'node_' + this.nodeCounter
            },
            scratch: {
              '_nodeID': index
            }
          }).id();
          this.steps[index].create = false;
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
        'border-color': ' #005c99',
        'border-width': '4px'
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


window.onload = function() {
  vObj.fitView();
}