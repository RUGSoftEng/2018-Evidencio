

/**
 * workflow.js
 * Basic functionalities for the design of workflows
 */

//Include Cytoscape, Vue
var cytoscape = require('cytoscape');
var popper = require('cytoscape-popper');
cytoscape.use( popper );
var cyCanvas = require('cytoscape-canvas');
cyCanvas(cytoscape);


//Init Cytoscape
const cy = cytoscape({
  container: document.getElementById('cy'),
  elements: [
    // Nodes
    {
      data: { id: 'Start' },
      position: { x: 0, y: 0 }
    }
  ],
  style: [
    {
      selector: 'node',
      style: {
        'background-color': '#666',
        'label': 'data(id)'
       }
    },
    {
      selector: 'edge',
      style: {
        'width': 3,
        'line-color': '#ccc',
        'target-arrow-color': '#ccc',
        'target-arrow-shape': 'triangle'
      }
    },
    {
      selector: '.buttonAddStep',
      style: {
        'shape': 'roundrectangle',
        'width': '25',
        'height': '25',
        'label': '',
        'background-color': '#46c637',
        'border-width': 1,
        'border-color': '#1f6b17'
      }
    },
    {
      selector: '.buttonAddOption',
      style: {
        'shape': 'roundrectangle',
        'width': '20',
        'height': '20',
        'label': '',
        'background-color': '#00a5ff',
        'border-width': 1,
        'border-color': '#0037ff'
      }
    }
  ],
  autoungrabify: true,
  layout: {
    name: 'preset',
    fit: true,
    directed: true
  }
});

// Data
var delta = 50, steps = 2, maxOptions = 1, numNodes = 0;
var step = [{
      options: [{
        nodeRef: 'Start'
      }]
    },
    {
      options: []
    }];
var buttonAddStep = [];
var buttonAddOption = [];
updateButtonAddOption();
updateButtonAddStep();


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
  for (var i = 0; i < steps; i++){
    if (i%2 == 0)
      ctx.fillStyle = "#e3e7ed";
    else
      ctx.fillStyle = "#c6cad1";
    let w = (maxOptions/2)*delta;
    ctx.fillRect(-w-100, i*delta-(delta/2), 2*w+225, delta);
  }
  ctx.restore();
});


// Functions
/**
 * Adds node to graph at a certain height, needs to be changed.
 * @param {*} height 
 * @param {*} id 
 */
window.addNode = function(height) {
  numNodes++;
  return cy.add({
    group: "nodes", 
    data: { id: 'node_' + (numNodes-1) }, 
    position: { 
      x: cy.width()/2, 
      y: height 
    }
  })
}

/**
 * Adds step to graph at given height, needs to be changed
 * @param {*} height 
 */
window.addStep = function(height) {
  if (height <= steps) {
    steps += 1;
    step.splice(height+1, 0, { options: [] });
    updateButtonAddOption();
    updateButtonAddStep();
    updateNodes();
  }
}

/**
 * Adds option to graph at given height, needs to be changed
 * @param {} height 
 */
window.addOption = function(height) {
  if (height < steps) {
    let node = addNode(height * delta);
    step[height].options.push({
      nodeRef: node.id()
    });
    maxOptions = Math.max(maxOptions, step[height].options.length);
    updateButtonAddOption();
    updateButtonAddStep();
    updateNodes();
  }
}

/**
 * Fits and centers the viewport on the graph
 */
window.graphFit = function() {
  nodes = [step[0].options[0].nodeRef];
  for (var i = 1; i < steps; i++){
    step[i].options.forEach(element => {
      step.push(cy.getElementById(element.nodeRef));
    });
  }
  cy.fit(nodes);
  //cy.center(nodes);
}

/**
 * Returns id of AddStep button that has the same reference as the argument, if it exists. Otherwise returns -1;
 * @param {*} reference 
 */
function isButtonAddStep(reference) {
  let id = buttonAddStep.length-1;
  while (id >= 0 && buttonAddStep[id].buttonRef != reference.id())
    id--;
  return id;
}

/**
 * Returns id of AddOption button that has the same reference as the argument, if it exists. Otherwise returns -1;
 * @param {*} reference 
 */
function isButtonAddOption(reference) {
  let id = buttonAddOption.length-1;
  while (id >= 0 && buttonAddOption[id].buttonRef != reference.id())
    id--;
  return id;
}

/**
 * Used for adding/removing/moving addStep-buttons
 */
function updateButtonAddStep() {
  // Removing
  while (buttonAddStep.length > steps) {
    const element = buttonAddStep.pop();
    cy.remove(cy.getElementById(element.buttonRef));
  }
  // Adding
  while (buttonAddStep.length < steps) {
    let newButtonData = {
      data: { id: 'buttonAddStep_' + buttonAddStep.length },
      classes: 'buttonAddStep'  
    }
    let newButton = cy.add(newButtonData);
    buttonAddStep.push({
      buttonRef: newButton.id()
    });
  }
  // Moving to correct positions
  for (let index = 0; index < buttonAddStep.length; index++) {
    const element = buttonAddStep[index];
    cy.getElementById(element.buttonRef).position({
      x: (maxOptions/2)*delta + 100,
      y: index*delta + 25
    });
  }
}

/**
 * Used for adding/removing/moving addOption-buttons
 */
function updateButtonAddOption() {
  // Removing
  while (buttonAddOption.length >= steps) {
    const element = buttonAddOption.pop();
    cy.remove(cy.getElementById(element.buttonRef));
  }
  // Adding
  while (buttonAddOption.length < steps-1) {
    let newButtonData = {
      data: { id: 'buttonAddOption_' + buttonAddOption.length },
      classes: 'buttonAddOption'
    }
    let newButton = cy.add(newButtonData);
    buttonAddOption.push({
      buttonRef: newButton.id()
    });
  }
  // Moving to correct positions
  for (let index = 0; index < buttonAddOption.length; index++) {
    const element = buttonAddOption[index];
    cy.getElementById(element.buttonRef).position({
      x: (step[index+1].options.length/2 + (step[index+1].options.length>0?0.5:0)) * delta,
      y: (index+1) * delta
    })
  }
}

function updateNodes() {
  for (let indexStep = 0; indexStep < steps; indexStep++) {
    const elementStep = step[indexStep];
    let numNodes = elementStep.options.length;
    let left = (-(numNodes-1) * delta)/2;
    for (let indexOption = 0; indexOption < numNodes; indexOption++) {
      const elementOption = elementStep.options[indexOption];
      cy.getElementById(elementOption.nodeRef).position({
        x: left + indexOption * delta,
        y: indexStep * delta
      });
    }
  }
}

/**
 * Buttons
 */
// Resets graph to fit to the screen, popper used to make the buttons stay in place.
var popperFit = cy.popper({
  content: () => {
    let div = document.createElement('div');
    div.innerHTML = '<button onclick="graphFit()">[ ]</button>';
    document.body.appendChild(div);
    return div;
  },
  renderedPosition: () => ({ x: 50, y: 50 }),
  popper: {}
});

// Buttons made using nodes
cy.on('tap', 'node', function(evt){
  let node = evt.target;
  let height = isButtonAddStep(node);
  if (height >= 0) {
    addStep(height);
    return;
  }
  height = isButtonAddOption(node);
  if (height >= 0) {
    addOption(height+1);
    return;
  }
});



