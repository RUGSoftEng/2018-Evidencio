<template>
    <div>
        <button type="button" class="list-group-item list-group-item-action" aria-expanded="false" :aria-controls="'editVar_' + index"
            :id="'headerVar_' + index" @click="toggleShow">
            <i class="fas fa-angle-down icon" v-if="!show"></i>
            <i class="fas fa-angle-up icon" v-else></i>
            {{ variable.title }}
            <i class="fas fa-bars handle icon float-right"></i>
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
.icon {
  font-size: 140%;
}
</style>

