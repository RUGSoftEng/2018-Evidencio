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
                    <br>
                    <div class="slidecontainer">
                        <vue-slider :min="variable.options.min" :max="variable.options.max" :interval="variable.options.step" v-model="inputs[variable.databaseId]"
                        @input="sliderChange(variable.databaseId, $event)"></vue-slider>
                    </div>
                </div>
                <div v-else>
                    {{variable.title}}:
                    <div v-for="(option, optIndex) in variable.options" :key="optIndex">
                        <input type="radio" button-variant="outline-primary" :id="'radio_' + varIndex + '_' + optIndex" :name="variable.title"
                            v-model="inputs[variable.databaseId]" :value="option.title">
                        <label :for="'radio_' + varIndex + '_' + optIndex">{{option.title}}</label>
                        <br>
                    </div>
                </div>
            </li>
        </ul>
        <br>
        <div v-if="resultStep==0">
          <button type="submit" class="btn btn-primary btn-sm" @click="runStep()">submit</button>
        </div>

        <div v-if="resultStep==1">
          <form method="GET" action="/graph">
            <input type="hidden" name="db_id" v-model="this.step.id">
            <div v-for="(modelResult, key) in modelResults" :key="key">
        
                <input type="hidden" :name="key" v-model="modelResults[key]">

            </div>
            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
          </form>
        </div>
        <br>
        <br>
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
          this.stepEvidencioId = this.step.evidencioModelIds[0];
          if (this.step.nextSteps.length == 0) {
            this.resultStep = 1;
          }
          for (var varKey in this.step.variables) {
            if (this.step.variables.hasOwnProperty(varKey)) {
              let variable = this.step.variables[varKey];
              if (variable.type == "continuous") this.inputs[variable.databaseId] = variable.options.min;
            }
          }
          this.step.apiMapping.forEach((apiCall, index) => {
            this.mapping[index] = {};
            apiCall.forEach(api => {
              this.mapping[index][api.pivot.evidencio_field_id] = api.id;
            });
          });

          this.step.nextSteps.forEach(nextStep => {
            this.rules.push(JSON.parse(nextStep.pivot.condition));
          });
          this.engine = new Engine();
          this.rules.map(rule => {
            this.engine.addRule(rule);
          });
        }
      }
    }
  },
  data() {
    return {
      model: 0,
      stepLevel: 0,
      inputResult: 0,
      step: {},
      mapping: [],
      inputs: {},
      rules: [],
      facts: {
        trueValue: true
      },
      answers: {},
      modelResults: {},
      engine: null,
      stepEvidencioId: 0,
      result: {},
      nextID: 0,
      resultStep: 0
    };
  },
  watch: {
    inputs() {
      this.calculateAnswers();
    }
  },
  methods: {
    sliderChange(key, value) {
      Vue.set(this.inputs, key, value);
      this.calculateAnswers();
    },
    calculateAnswers() {
      let ret = [];
      this.mapping.forEach((apiCall, index) => {
        ret[index] = {};
        for (var key in apiCall) {
          if (apiCall.hasOwnProperty(key)) {
            ret[index][key] = this.inputs[apiCall[key]];
          }
        }
      });
      this.answers = ret;
    },

    nextStep() {
      var nextStepID;
      runStep();
      nextStepID = this.nextID;
      nextStep(nextStepID);
    },
    nextStep(nextStepID) {
      for (var key in this.workflowData.steps) {
        if (this.workflowData.steps.hasOwnProperty(key)) {
          if (this.workflowData.steps[key].id == nextStepID) {
            this.step = this.workflowData.steps[key];
            if (this.step.nextSteps.length == 0) {
              this.resultStep = 1;
            }
            for (var varKey in this.step.variables) {
              if (this.step.variables.hasOwnProperty(varKey)) {
                let variable = this.step.variables[varKey];
                if (variable.type == "continuous") this.inputs[variable.databaseId] = variable.options.min;
              }
            }
            this.step.apiMapping.forEach((apiCall, index) => {
              this.mapping[index] = {};
              apiCall.forEach(api => {
                this.mapping[index][api.pivot.evidencio_field_id] = api.id;
              });
            });
            this.step.nextSteps.forEach(nextStep => {
              this.rules.push(JSON.parse(nextStep.pivot.condition));
            });

            this.engine = new Engine();
            this.rules.map(rule => {
              this.engine.addRule(rule);
            });
            this.stepEvidencioId = this.step.evidencioModelIds[0];
          }
        }
      }
    },

    runStep() {
      var self = this;
      let calculations = [];
      let models = this.step.evidencioModelIds;
      $.when
        .apply(
          $,
          models.map((modelId, index) => {
            return $.ajax({
              headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
              },
              url: "/workflow/run",
              type: "POST",
              data: {
                modelId: modelId,
                values: self.answers[index]
              },
              success: function(result) {
                self.result = result;
                if (result.hasOwnProperty("result")) {
                  self.facts["result_" + modelId + "_0"] = result.result;
                  self.modelResults["result_" + modelId + "_0"] = result.result;
                } else {
                  result.resultSet.forEach((res, ind) => {
                    self.facts["result_" + modelId + "_" + ind] = res.result;
                    self.modelResults["result_" + modelId + "_" + ind] = res.result;
                  });
                }
              }
            });
          })
        )
        .then(function(x) {
          self.engine.run(self.facts).then(events => {
            events.map(event => {
              if (event.type == "goToNextStep") {
                console.log(event.params.stepId);
                self.nextID = event.params.stepId;
              }
            });
          });
        })
        .then(function(x) {
          var nextStepID = self.nextID;
          self.nextStep(nextStepID);
        });
    }
  }
};
</script>
