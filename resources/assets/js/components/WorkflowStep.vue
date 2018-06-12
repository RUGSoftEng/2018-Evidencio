<template>
    <div class="container">
        <h3>{{workflowData.title}}</h3>
        <br>
        <h5>{{step.title}}</h5>

        <input type="hidden" :name="model" :value="workflowData.evidencioModels[0]">
        <ul class="list-group">
            <li class="list-group-item" v-for="(variable, varIndex) in step.variables" :key="varIndex">
                <div v-if="variable.type=='continuous'">
                    <p>{{variable.title}}: {{variable.options.min}} - {{variable.options.max}} by {{variable.options.step}}</p>

                    <div class="slidecontainer">
                        <vue-slider :min="variable.options.min" :max="variable.options.max" :interval="variable.options.step" :value="answers[variable.id]" 
                        @input="sliderChange(variable.id, $event)"></vue-slider>
                    </div>
                </div>
                <div v-else>
                    {{variable.title}}:
                    <div v-for="(option, optIndex) in variable.options" :key="optIndex">
                        <input type="radio" button-variant="outline-primary" :id="'radio_' + varIndex + '_' + optIndex" :name="answers[option.id]"
                            v-model="answers[variable.id]" :value="option.title">
                        <label :for="'radio_' + varIndex + '_' + optIndex">{{option.title}}</label>
                        <br>
                    </div>
                </div>
            </li>
        </ul>
        <br>
        <button type="submit" class="btn btn-primary btn-sm" @click="runStep()">submit</button>



    </div>

</template>


<script>
import vueSlider from "vue-slider-component";
import { Engine } from "json-rules-engine";

export default {
  components: {
    vueSlider
  },
  props: ["workflowData"],
  mounted() {
    for (var key in this.workflowData.steps) {
      if (this.workflowData.steps.hasOwnProperty(key)) {
        if (this.workflowData.steps[key].level == this.stepLevel) {
          this.step = this.workflowData.steps[key];
          for (var varKey in this.step.variables) {
            if (this.step.variables.hasOwnProperty(varKey)) {
              let variable = this.step.variables[varKey];
              if (variable.type == "continuous") this.answers[variable.id] = variable.options.min;
            }
          }
          this.step.nextSteps.forEach(nextStep => {
            this.rules.push(JSON.parse(nextStep.pivot.condition));
          });
          this.engine = new Engine();
          this.rules.map(rule => {
            this.engine.addRule(rule);
          });
          this.stepEvidencioId = this.step.evidencioModelID[0].evidencio_model_id;
        }
      }
    }
    console.log(this.stepEvidencioId);
  },
  data() {
    return {
      model: 0,
      stepLevel: 0,
      inputResult: 0,
      step: {},
      rules: [],
      engine: null,
      stepEvidencioId: 0,
      answers: {},
      result: {}
    };
  },
  methods: {
    sliderChange(key, value) {
      Vue.set(this.answers, key, value);
    },
    // TODO: fix submitResult
    submitResult(id) {
      Event.fire("submitResult", id);
    },
    // TODO: fix next step
    nextStep() {
      var nextStepID;
      runStep();
      //// TODO: add rule engine Here
      //nextStepID=Result of rule Engine
      for (var key in this.workflowData.steps) {
        if (this.workflowData.steps[key].id == nextStepID) {
          this.step = this.workflowData.steps[key];
          this.stepEvidencioId = this.step.evidencioModelID[0].evidencio_model_id;
        }
      }
    },
    //// TODO: fix api call
    runStep() {
      var self = this;

      $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        url: "/workflow/run",
        type: "POST",
        data: {
          modelId: self.stepEvidencioId,
          values: self.answers
        },
        success: function(result) {
          self.result = result;
          if (result.hasOwnProperty("result")) {
            //normal
          } else {
            //composite
          }
          console.log(self.result);
        }
      });
    }
  }
};
</script>