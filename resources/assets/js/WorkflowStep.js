//const nouisliderComponent = require('vue-nouislider-component');

Vue.component("WorkflowStep", require("./components/WorkflowStep.vue"));


new Vue({
  el: '#workflow',
  data: {
    stepIndex: 0,
  },
  method: {
    submitResult: function (answers) {
      this.$http.post('/graph', JSON.stringify(answers));
    }
  }

})