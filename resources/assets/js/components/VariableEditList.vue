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
    updateOrder() {
      this.$emit("sort", this.localSelectedVariables);
    },
    boolFalseArray(size) {
      return Array(size).fill(false);
    },
    reload() {
      this.localSelectedVariables = JSON.parse(JSON.stringify(this.selectedVariables));
      this.showFlags = this.boolFalseArray(this.selectedVariables.length);
    },
    closeCollapsed() {
      this.showFlags = this.boolFalseArray(this.selectedVariables.length);
      for (let indexVar = this.selectedVariables.length - 1; indexVar >= 0; indexVar--) {
        $("#editVar_" + indexVar).collapse("hide");
      }
    },
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

<!--<template>
    <div id="accVariablesEdit">
        <variable-edit-item v-for="(variable, index) in selectedVariables" :key="index" :index-item="index" :variable="usedVariables[variable]"
            @toggle="selectCard($event)"></variable-edit-item>
    </div>
</template>

<script>
import VariableEditItem from "./VariableEditItem.vue";

export default {
  components: {
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
    selectCard(index) {
      for (let ind = 0; ind < this.selectedVariables.length; ind++) {
        if (ind == index) $("#varEditCollapse_" + ind).collapse("toggle");
        else $("#varEditCollapse_" + ind).collapse("hide");
      }
    }
  }
};
</script> -->