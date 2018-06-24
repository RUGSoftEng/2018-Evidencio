<template>
    <form onsubmit="return false">
        <div class="form-group">
            <label for="title" class="mb-0">Title</label>
            <input type="text" name="title" id="title' + indexItem" v-model="localTitle" @input="change" placeholder="Title" :disabled="!editing"
                :class="{'form-control': editing, 'form-control-plaintext': !editing }">
            <label for="description" class="mb-0 mt-2">Description</label>
            <textarea id="description" cols="30" v-model="localDescription" @input="change" placeholder="Description" :disabled="!editing"
                :class="{'form-control': editing, 'form-control-plaintext': !editing }" :rows="numberOfRows"></textarea>
            <input type="image" class="buttonIcon float-right" :src="getImage" @click="editing = !editing" alt="Edit">
        </div>
    </form>
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
      required: false,
      default: () => {
        return "";
      }
    },
    description: {
      type: String,
      required: false,
      default: () => {
        return "";
      }
    },
    numberOfRows: {
      type: Number,
      required: false,
      default: () => {
        return 3;
      }
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
textarea {
  resize: none;
}

label {
  font-weight: bold;
}
</style>