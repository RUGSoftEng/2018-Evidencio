<template>
    <div class="card height-100">
        <div class="card-header d-flex align-items-center">
            Workflow
            <button type="button" class="btn btn-primary ml-2" @click="saveWorkflow">Save Workflow</button>
        </div>

        <div class="card-body scrollbarAtProject full-height">
            <form onsubmit="return false">
                <div class="form-group">
                    <label for="title">Title: </label>
                    <input type="text" name="" id="title' + indexItem" v-model="localTitle" @input="change" placeholder="Title" :disabled="!editing"
                        :class="{'form-control': editing, 'form-control-plaintext': !editing }">
                </div>
                <div class="form-group">
                    <label for="description">Description: </label>
                    <textarea id="description" cols="30" rows="3" v-model="localDescription" @input="change" placeholder="Description" :disabled="!editing"
                        :class="{'form-control': editing, 'form-control-plaintext': !editing }"></textarea>
                    <input type="image" class="buttonIcon right" :src="getImage" @click="editing = !editing" alt="Edit">
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
  data() {
    return {
      editing: false,
      localTitle: "",
      localDescription: ""
    };
  },

  props: {
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      required: true
    }
  },

  methods: {
    change() {
      this.$emit("change", {
        title: this.localTitle,
        description: this.localDescription
      });
    },
    refresh() {
      this.localTitle = this.title;
      this.localDescription = this.description;
    },
    saveWorkflow() {
      Event.fire("save");
    }
  },

  computed: {
    getImage: function() {
      if (this.editing) return "/images/check.svg";
      else return "/images/pencil.svg";
    }
  },

  mounted: function() {
    this.refresh();
  },

  watch: {
    title: function() {
      this.refresh();
    },
    localTitle: function() {
      this.refresh();
    }
  }
};
</script>

<style lang="scss" scoped>
.right {
  float: right;
}

textarea {
  resize: none;
}

.form-control-plaintext[disabled] {
  background-color: white;
}
</style>