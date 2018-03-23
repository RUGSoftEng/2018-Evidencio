

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
    // Buttons
    {
      data: { id: 'addStep' },
      position: { x: 100, y: 50 }   
    },
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
      selector: '#addStep',
      style: {
        'shape': 'roundrectangle',
        'width': '20',
        'height': '20',
        'label': '',
        'background-color': '#46c637',
        'border-width': 1,
        'border-color': '#1f6b17'
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
var delta = 50, steps = 1, maxOptions = 1;
var step = [{
      options: [cy.getElementById('Start')]
    }];


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
    let w = ((maxOptions-1)/2)*delta;
    ctx.fillRect(-w-100, i*delta-(delta/2), 2*(w+100), delta);
  }
  //ctx.restore();
});


// Functions
window.addNode = function(height, id) {
  return cy.add({
    group: "nodes", data: { id: id }, position: { x: cy.width()/2, y: height }
  })
}

window.addStep = function(height) {
  if (height <= steps) {
    steps += 1;
    step.splice(height, 0, { options: [] });
    alert(step.length);
  }
}

window.addOption = function(height) {
  if (height < steps) {
    let node = addNode(height * delta, height+'_'+options.length);
    step[height].options.push([node]);
  }
}

window.graphFit = function() {
  nodes = step[0].options;
  for (var i = 1; i < steps; i++){
    Array.prototype.push.apply(nodes,step[i].options);
  }
  cy.fit(nodes, 200);
  cy.center(nodes);
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

// Adding steps
cy.on('tap', 'node', function(evt){
  let node = evt.target;
  alert(node.id());
  if (node.id() == "addStep") {
    addStep(0);
  }
});



