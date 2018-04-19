<template>
    <div class="card">
        <a href="#" @click="toggleShow" class="card-header collapsed" :id="'varEditCollapseHeader_' + indexItem" 
            data-parent="#accVariablesEdit" aria-expanded="false" :aria-controls="'varEditCollapse_' + indexItem">
            <h6 class="mb-0">
                {{ variable.title }}
            </h6>
        </a>

        <div :id="'varEditCollapse_' + indexItem" class="collapse" :aria-labelledby="'#varEditCollapseHeader_' + indexItem">
            <div class="card-body">
                <form onsubmit="return false">
                    <div class="form-group">
                        <label :for="'titleVar_' + indexItem">Title: </label>
                        <input type="text" name="" :id="'titleVar_' + indexItem" class="form-control" v-model="variable.title" placeholder="Title"
                            :disabled="!editing">
                        <small :id="'titleVarHelp_' + indexItem" class="form-text text-muted">Title of the variable</small>
                    </div>
                    <div class="form-group">
                        <label :for="'descriptionVar_' + indexItem">Description: </label>
                        <textarea class="form-control" :id="'descriptionVar_' + indexItem" cols="30" rows="3" v-model="variable.description" :disabled="!editing"></textarea>
                        <small :id="'descriptionVarHelp_' + indexItem" class="form-text text-muted">Description of the variable</small>
                        <input type="image" class="buttonIcon" :src="getImage" @click="editing = !editing" alt="Edit">
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  props: {
    variable: {
      type: Object,
      required: true
    },
    indexItem: {
      type: Number,
      required: true
    }
  },

  data() {
    return {
      editing: false
    };
  },

  methods: {
    toggleShow() {
      this.$emit("toggle", this.indexItem);
    }
  },

  computed: {
    getImage: function() {
      if (this.editing) return "/images/check.svg";
      else return "/images/pencil.svg";
    }
  }
};
</script>