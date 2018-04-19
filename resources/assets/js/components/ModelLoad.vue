<template>
  <div>
    <input type="number" id="inputModelID" name="inputModelID" v-model="modelID" @keyup.enter="modelLoad">
    <button type="button" class="btn btn-primary" @click="modelLoad">Load Model</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      modelID: 0
    };
  },
  methods: {
    modelLoad() {
      Event.fire("modelLoad", this.modelID);
    }
  }
};
</script>

<style lang="scss" scoped>
#inputModelID {
  width: 50px;
}
</style>