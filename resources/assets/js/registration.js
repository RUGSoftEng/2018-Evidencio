var appRegistration;

Vue.component("documentInput", require("./components/DocumentInput.vue"));

window.appRegistration = new Vue({
  el: '#files',
  data: {
    maxFileNum: 5,
    fileList: [
      { id: 0 }
    ],
    next_id: 3
  },
  methods: {
    addButton: function(event) {
      this.fileList.push({id: this.next_id});
      this.next_id++;

      if(this.fileList.length >= this.maxFileNum)
      {
        $(".add-document").hide();
      }
    }
  }
});
