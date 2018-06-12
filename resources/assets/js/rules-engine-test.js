import {
  Engine
} from 'json-rules-engine';

var currentStepId = 1;

var rules = [{
  conditions: {
    all: [{
      fact: "result_576_0",
      operator: "greaterThanInclusive",
      value: 0.6
    }]
  },
  event: {
    type: "goToNextStep",
    params: {
      stepId: 2
    }
  }
}, {
  conditions: {
    all: [{
      fact: "result_576_0",
      operator: "lessThan",
      value: 0.6
    }]
  },
  event: {
    type: "goToNextStep",
    params: {
      stepId: 3
    }
  }
}];

var engine = new Engine();
rules.map(rule => {
  engine.addRule(rule);
});

let facts = {
  result_576_0: 0.5
}

window.setTimeout(() => {
  engine.run(facts).then(events => {
    events.map(event => {
      if (event.type == "goToNextStep") {
        currentStepId = event.params.stepId;
        setHTML();
      }
    });
  });
}, 2000);

function setHTML() {
  document.getElementById("result").innerHTML = "CurrentStep: " + currentStepId;
}
setHTML();