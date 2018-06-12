const
  nouisliderComponent = require('vue-nouislider-component');

Vue.component('acme-component', {
  components: {
    nouisliderComponent
  }
});
Vue.component("WorkflowStep", require("./components/WorkflowStep.vue"));


import {
  Engine
} from 'json-rules-engine';


new Vue ({
  el:'#workflow',
  data:{
    stepIndex:0,
  },
  method:{
    submitResult: function(answers) {
        this.$http.post('/graph', JSON.stringify(answers));
    }
  }

})