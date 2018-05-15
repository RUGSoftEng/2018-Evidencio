<template v-if='modelLoaded'>
    <div class="card">
        <div @click="toggleShow" class="card-header collapsed d-flex justify-content-between" :id="'varViewCollapseHeader_' + indexItem" 
            aria-expanded="true" :aria-controls="'varViewCollapse_' + variable.ind" data-parent="#accVariablesView">
            <h6 class="mb-0">
                {{ variable.title }}
                <span class="badge badge-pill" :class="{'badge-danger': timesUsed==0, 'badge-success': timesUsed>0}">{{ timesUsed }}</span>

            </h6>
        </div>

        <div :id="'varViewCollapse_' + indexItem" class="collapse" :aria-labelledby="'#varViewCollapseHeader_' + indexItem">
            <p>{{ variable.title }}</p>
            <p>{{ variable.description }}</p>
            <variable-view-categorical v-if="variable.type == 'categorical'" :options="variable.options"></variable-view-categorical>
            <variable-view-continuous v-if="variable.type == 'continuous'" :options="variable.options"></variable-view-continuous>
        </div>
    </div>
</template>

<script>
import VariableViewCategorical from "./VariableViewCategorical.vue";
import VariableViewContinuous from "./VariableViewContinuous.vue";

export default {
  components: {
    VariableViewCategorical,
    VariableViewContinuous
  },

  props: {
    variable: {
      type: Object,
      required: true
    },
    timesUsed: {
      type: Number,
      required: true
    },
    indexItem: {
      type: Number,
      required: true
    }
  },

  methods: {
    toggleShow() {
      this.$emit("toggle", this.indexItem);
    }
  }
};
</script>