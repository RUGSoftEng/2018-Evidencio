<template>
    <div>
        <button type="button" class="list-group-item list-group-item-action" aria-expanded="false" :aria-controls="'editVar_' + index"
            :id="'headerVar_' + index" @click="toggleShow">
            <i class="fo-icon icon-down-open" v-if="!show">&#xe802;</i>
            <i class="fo-icon icon-up-open" v-else>&#xe803;</i>
            {{ variable.title }}
            <i class="fo-icon icon-menu handle float-right">&#xf0c9;</i>
        </button>
        <div class="form-group collapse" :id="'editVar_' + index">
            <label for="title" class="mb-0">Title</label>
            <input type="text" name="title" class="form-control" v-model="variable.title" placeholder="Title">
            <label for="description" class="mb-0 mt-2">Description</label>
            <textarea id="description" cols="30" class="form-control" v-model="variable.description" placeholder="Description" rows="2"></textarea>
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
    show: {
      type: Boolean,
      required: true
    },
    index: {
      type: Number,
      required: true
    }
  },
  methods: {
    toggleShow() {
      this.$emit("toggle", this.index);
    }
  }
};
</script>

<style>
.icon-menu {
  font-size: 120%;
}
</style>


<!--<template>
    <div class="card">
        <div @click="toggleShow" class="card-header collapsed" :id="'varEditCollapseHeader_' + indexItem" data-parent="#accVariablesEdit"
            aria-expanded="false" :aria-controls="'varEditCollapse_' + indexItem">
            <h6 class="mb-0">
                {{ variable.title }}
            </h6>
        </div>

        <div :id="'varEditCollapse_' + indexItem" class="collapse" :aria-labelledby="'#varEditCollapseHeader_' + indexItem">
            <div class="card-body">
                <form onsubmit="return false">
                    <div class="form-group">
                        <label :for="'titleVar_' + indexItem">Title: </label>
                        <input type="text" name="" :id="'titleVar_' + indexItem" :disabled="!editing" :class="{'form-control': editing, 'form-control-plaintext': !editing}"
                            v-model="variable.title" placeholder="Title">
                        <small :id="'titleVarHelp_' + indexItem" class="form-text text-muted">Title of the variable</small>
                    </div>
                    <div class="form-group">
                        <label :for="'descriptionVar_' + indexItem">Description: </label>
                        <textarea :disabled="!editing" :class="{'form-control': editing, 'form-control-plaintext': !editing}" :id="'descriptionVar_' + indexItem"
                            cols="30" rows="3" v-model="variable.description"></textarea>
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
</script>-->