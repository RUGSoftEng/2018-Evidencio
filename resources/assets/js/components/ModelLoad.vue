<template>

  <div>
    <input type="text" id="inputModelID" name="inputModelSearch" v-model="modelSearch" >
    <!-- Trigger the modal with a button -->
  <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#myModal" @click="loadModelEvidencio">Search Evidencio Model</button>

  <!-- Modal -->
  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Search for: {{modelSearch}}</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <ul class="list-group">
            <li class="list-group-item" v-for="(search, index) in searchs" :key="index" v-if="search.title" v-text="search.title" @click="modelLoad(search.id)" data-dismiss="modal"></li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
  </div>

</template>

<script>
export default {
  data() {
    return {
      modelSearch:"",
      modelID: 0,
      searchs: {
        type: Object,
        default: () => {}
      }
    };
  },
  methods: {
    loadModelEvidencio() {
      var self = this;

        $.ajax({
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
          },
          url: "/designer/search",
          type: "POST",
          data: {
            modelSearch:this.modelSearch,
          },
          success: function (result) {
            self.debug = result;
            self.searchs=JSON.parse(result);
          }
        });

    },


    modelLoad(id){
      this.modelID=id;
      Event.fire("modelLoad", this.modelID);
    },
  }
};
</script>

<style lang="scss" scoped>
#inputModelID {
  width: 50px;
}
</style>
