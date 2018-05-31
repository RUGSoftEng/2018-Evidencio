<template>
    <div>
        <button type="button" class="list-group-item list-group-item-action" data-toggle="collapse" :data-target="'#editRule_' + index"
            aria-expanded="false" :aria-controls="'editRule_' + index" :id="'headerRule_' + index" @click="show = !show">
            <i class="fo-icon icon-down-open" v-if="!show">&#xe802;</i>
            <i class="fo-icon icon-up-open" v-else>&#xe803;</i>
            {{ rule.title }}
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
                preselect-first :allow-empty="false">
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
    }
  },
  data() {
    return {
      show: false
    };
  }
};
</script>


<!--<template>
    <div class="card border-secondary">
        <a href="#" @click="toggleShow" class="card-header collapsed" :id="'ruleEditCollapseHeader_' + indexItem" data-parent="#accRulesEdit"
            aria-expanded="false" :aria-controls="'ruleEditCollapse_' + indexItem">
            <h6 class="mb-0">
                {{ rule.title }}
            </h6>
        </a>

        <div :id="'ruleEditCollapse_' + indexItem" class="collapse" :aria-labelledby="'#ruleEditCollapseHeader_' + indexItem">
            <div class="card-body">
                <form onsubmit="return false">
                    <div class="form-group">
                        <label :for="'titleRule_' + indexItem">Title: </label>
                        <input type="text" name="" :id="'titleRule_' + indexItem" class="form-control" v-model="rule.title" placeholder="Title" :disabled="!editing">
                        <small :id="'titleRuleHelp_' + indexItem" class="form-text text-muted">Title of the variable</small>
                    </div>
                    <div class="form-group">
                        <label :for="'conditionRule_' + indexItem">Condition: </label>
                        <textarea class="form-control" :id="'conditionRule_' + indexItem" cols="30" rows="3" v-model="rule.condition" :disabled="!editing">true</textarea>
                        <small :id="'descriptionVarHelp_' + indexItem" class="form-text text-muted">Condition of the rule</small>
                        <input type="image" class="buttonIcon" :src="getImage" @click="editing = !editing" alt="Edit">
                    </div>
                    <div class="form-group">
                        <label class="typo__label">Custom option template</label>
                        
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";

export default {
  components: {
    Multiselect
  },
  props: {
    rule: {
      type: Object,
      required: true
    },
    indexItem: {
      type: Number,
      required: true
    },
    options: {
      type: Array,
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