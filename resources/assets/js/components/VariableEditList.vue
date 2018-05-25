<template>
    <div class="list-group">
        <draggable v-model="localSelectedVariables" :options="{handle: '.handle'}" @start="drag=true" @end="drag=false" @update="updateOrder">
            <variable-edit-item v-for="(variableName, index) in localSelectedVariables" :key="index" :index="index" :variable="usedVariables[variableName]"></variable-edit-item>
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
    }
  },

  mounted() {
    this.localSelectedVariables = JSON.parse(JSON.stringify(this.selectedVariables));
  },

  watch: {
    selectedVariables: function() {
      this.localSelectedVariables = JSON.parse(JSON.stringify(this.selectedVariables));
    }
  },

  data() {
    return {
      localSelectedVariables: []
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