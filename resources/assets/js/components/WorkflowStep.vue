<template >
<div class="container">
  <h3>{{workflowData.title}}</h3>
  <br>
    <h5>{{step.title}}</h5>

      <input type="hidden" :name="model" :value="workflowData.evidencioModels[0]">
      <ul class="list-group">
        <li class="list-group-item" v-for="variable in step.variables">
          <div v-if="variable.type=='continuous'">
            <p>{{variable.title}}: {{variable.options.min}} - {{variable.options.max}} by {{variable.options.step}}</p>

            <div class="slidecontainer">
              <input type="range" :min="variable.options.min" :max="variable.options.max" v-model="answers[variable.id]" class="slider" >
            </div>
              <input type="number" :step='variable.options.step' v-model="answers[variable.id]">

          </div>
          <div v-if="variable.type=='categorical'">
            {{variable.title}}:
            <div v-for="option in variable.options">
              <input type="radio" button-variant="outline-primary" :name="answers[option.id]" v-model="answers[variable.id]" :value="option.title" >
              {{option.title}}
              <br>
            </div>
          </div>
        </li>
      </ul>
      <br>
      <button type="submit" class="btn btn-primary btn-sm" @click="runStep()">submit</button>
      {{this.result}}


</div>

</template>


<script>
export default {
    props: ['workflowData'],
    mounted(){
      for(var key in this.workflowData.steps){
        if(this.workflowData.steps[key].level==this.stepLevel){
          this.step=this.workflowData.steps[key];
          this.stepEvidencioId=this.step.evidencioModelID[0].evidencio_model_id;
        }
      }
    console.log(this.stepEvidencioId);

    },
    data() {

      return {
        model:0,
        stepLevel:0,
        inputResult:0,
        step:{},
        stepEvidencioId:0,
        answers:{},
        result:0
      };
    },
    methods:{
      // TODO: fix submitResult
      submitResult(id){
        Event.fire("submitResult", id);
      },
      // TODO: fix next step
      nextStep(){
        var nextStepID;
        let engine = new Engine();
        //save rules in rule engine
        for(var key in this.step.nextSteps){
          let condition=this.step.nextSteps[key].pivot.condition;
          let rule = new Rule(condition);
          engine.addRule(rule);
        }
        var key = "result" + "_" + this.stepEvidencioId + "_" + 0;
        let resultFact= this.result;
        let facts;
        facts[key]=this.result;
        //run rule engine and get next step ID
        engine.run(facts).then(events => {
          events.map(event => {
            if (event.type == "goToNextStep") {
              nextStepID = event.params.stepId;
            }
          });
        });
        //// TODO: add rule engine Here
        //nextStepID=Result of rule Engine
        //get new step
        for(var key in this.workflowData.steps){
          if(this.workflowData.steps[key].id==nextStepID){
            this.step=this.workflowData.steps[key];
            this.stepEvidencioId=this.step.evidencioModelID[0].evidencio_model_id;
          }
        }

      },
      runStep() {
        var self = this;

        $.ajax({
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
          },
          url: "/workflow/run",
          type: "POST",
          data: {
            modelId:this.stepEvidencioId,
            values:this.answers
          },
          success: function(result) {
            self.debug = result;
            self.result= result.result;
          }
        });
      },
    }
}
</script>
