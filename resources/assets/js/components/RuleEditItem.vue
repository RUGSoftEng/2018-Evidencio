<template>
    <div>
        <button type="button" class="list-group-item list-group-item-action" :class="{warning: warning}" data-toggle="collapse" :data-target="'#editRule_' + index"
            aria-expanded="false" :aria-controls="'editRule_' + index" :id="'headerRule_' + index" @click="show = !show">
            <i class="fo-icon icon-down-open arrow" v-if="!show">&#xe802;</i>
            <i class="fo-icon icon-up-open arrow" v-else>&#xe803;</i>
            {{ rule.title }}
            <i class="fo-icon icon-trash float-right" @click="removeRule">&#xf1f8;</i>
        </button>
        <div class="form-group collapse" :id="'editRule_' + index">
            <label for="title" class="mb-0">Title</label>
            <input type="text" name="title" class="form-control" v-model="rule.title" placeholder="Title">
            <label for="condition" class="mb-0">Condition</label>
            <div class="card border-secondary">
                <div class="card-body">
                    <rule-logic name="condition" :logic="rule.condition" :current-label="rule.condition.label" :reachable-results="reachableResults"></rule-logic>
                </div>
            </div>
            <label class="mb-0" for="target">Target</label>
            <multiselect name="target" v-model="rule.target" label="title" track-by="ind" :options="children" :option-height="44" :show-labels="false"
                preselect-first :allow-empty="false" @input="$emit('children-changed')">
                <template slot="singleLabel" slot-scope="props">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <svg class="option__image" viewBox="0 0 44 44" width="44" height="44">
                                    <rect x="2" y="2" width="40" height="40" rx="4" ry="4" :style="'fill:'+props.option.colour+';stroke-width:1;stroke:rgb(0,0,0)'"
                                    />
                                </svg>
                            </div>
                            <div class="col option__desc">
                                <span class="option__title">{{ props.option.title }}</span>
                                <span>{{ props.option.id }}</span>
                            </div>
                        </div>
                    </div>
                </template>
                <template slot="option" slot-scope="props">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <svg class="option__image" viewBox="0 0 44 44" width="44" height="44">
                                    <rect x="2" y="2" width="40" height="40" rx="4" ry="4" :style="'fill:'+props.option.colour+';stroke-width:1;stroke:rgb(0,0,0)'"
                                    />
                                </svg>
                            </div>
                            <div class="col option__desc">
                                <span class="option__title">{{ props.option.title }}</span>
                                <span>{{ props.option.id }}</span>
                            </div>
                        </div>
                    </div>
                </template>
            </multiselect>
        </div>
    </div>
</template>

<script>
import RuleLogic from "./RuleLogic.vue";

export default {
  components: {
    RuleLogic
  },
  props: {
    rule: {
      type: Object,
      required: true
    },
    index: {
      type: Number,
      required: true
    },
    children: {
      type: Array,
      required: true
    },
    reachableResults: {
      type: Array,
      required: true
    },
    warning: {
      type: Boolean,
      required: true
    }
  },
  methods: {
    removeRule() {
      this.$emit("remove", this.index);
    }
  },
  data() {
    return {
      show: false
    };
  }
};
</script>

<style scoped>
.icon-trash {
  font-size: 140%;
}
.arrow {
  font-size: 120%;
}
.border-secondary {
  border-color: #ced4da !important;
}
.warning {
  border: solid 2px yellow;
}
</style>
