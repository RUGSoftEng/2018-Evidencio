<template>
    <div class="list-group" id="variableEditList">
        <draggable v-model="localSelectedVariables" :options="{handle: '.handle'}" @start="drag=true" @end="drag=false" @choose="closeCollapsed"
            @update="updateOrder">
            <variable-edit-item v-for="(variableName, index) in localSelectedVariables" :key="index" :index="index" :show="showFlags[index]"
                :variable="usedVariables[variableName]" @toggle="toggleShow($event)"></variable-edit-item>
        </draggable>
    </div>
</template>

<script>
import Draggable from "vuedraggable";
import VariableEditItem from "./VariableEditItem.vue";

export default {
  components: {
    Draggable,
    VariableEditItem
  },

  props: {
    selectedVariables: {
      type: Array,
      required: true
    },
    usedVariables: {
      type: Object,
      required: true
    }
  },

  methods: {
    /**
     * Emit an event to update the order of the fields/variables
     */
    updateOrder() {
      this.$emit("sort", this.localSelectedVariables);
    },

    /**
     * Return an array of booleans with a given size.
     * @param {Number} size
     */
    boolFalseArray(size) {
      return Array(size).fill(false);
    },

    /**
     * Reload the data
     */
    reload() {
      this.localSelectedVariables = JSON.parse(JSON.stringify(this.selectedVariables));
      this.showFlags = this.boolFalseArray(this.selectedVariables.length);
    },

    /**
     * Close all collapsable divs
     */
    closeCollapsed() {
      this.showFlags = this.boolFalseArray(this.selectedVariables.length);
      for (let indexVar = this.selectedVariables.length - 1; indexVar >= 0; indexVar--) {
        $("#editVar_" + indexVar).collapse("hide");
      }
    },

    /**
     * Show/hide the collapse with given index
     * @param {Number} index
     */
    toggleShow(index) {
      if (!$("#editVar_" + index).hasClass("collapsing")) {
        this.$set(this.showFlags, index, !this.showFlags[index]);
        $("#editVar_" + index).collapse("toggle");
      }
    }
  },

  mounted() {
    this.reload();
  },

  watch: {
    selectedVariables: function() {
      this.reload();
    }
  },

  data() {
    return {
      localSelectedVariables: [],
      showFlags: []
    };
  }
};
</script>
