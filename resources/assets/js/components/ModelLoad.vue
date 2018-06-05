<template>

    <div>

        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#myModal" @click="loadModelEvidencio">Search Evidencio Model</button>

        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                      <input type="text" name="inputModelSearch" v-model="modelSearch" class="form-control" style="width:100%; font-size:x-large; height:50px;" placeholder="Search for Model..." v-on:keyup.enter="loadModelEvidencio"></input>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="list-group" id="list-tab" role="tablist">
                            <a class="list-group-item list-group-item-action"  v-for="(search, index) in searchs" :key="index" v-if="search.title" v-text="search.title" @click="modelLoad(search.id)"
                                data-dismiss="modal"></a>
                        </div>
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
      modelSearch: "",
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
          modelSearch: this.modelSearch
        },
        success: function(result) {
          self.debug = result;
          self.searchs = JSON.parse(result);
        }
      });
    },

    modelLoad(id) {
      this.modelID = id;
      Event.fire("modelLoad", this.modelID);
    }
  }
};
</script>

<style lang="scss" scoped>
#inputModelID {
  width: 100px;
}
</style>
