<template>
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
                        <textarea class="form-control" :id="'conditionRule_' + indexItem" cols="30" rows="3" v-model="rule.condition" :disabled="!editing"></textarea>
                        <small :id="'descriptionVarHelp_' + indexItem" class="form-text text-muted">Condition of the rule</small>
                        <input type="image" class="buttonIcon" :src="getImage" @click="editing = !editing" alt="Edit">
                    </div>
                    <div class="form-group">
                        <label class="typo__label">Custom option template</label>
                        <vue-multiselect v-model="rule.target" label="title" track-by="ind" :options="options" :option-height="44" :show-labels="false"
                            preselect-first :allow-empty="false">
                            <template slot="singleLabel" slot-scope="props">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col">
                                            <svg class="option__image" viewBox="0 0 44 44" width="44" height="44">
                                                <rect x="2" y="2" width="40" height="40" rx="4" ry="4" :style="'fill:'+props.option.colour+';stroke-width:1;stroke:rgb(0,0,0)'" />
                                                <!--<text x="15" y="20" style="font-weight: bold; font-size: 24px; stroke: #000000; fill: #ffffff;">
                                                    {{ props.option.id }}
                                                </text>-->
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
                                                <rect x="2" y="2" width="40" height="40" rx="4" ry="4" :style="'fill:'+props.option.colour+';stroke-width:1;stroke:rgb(0,0,0)'" />
                                                <!--<text x="15" y="20" style="font-weight: bold; font-size: 24px; stroke: #000000; fill: #ffffff;">
                                                    {{ props.option.id }}
                                                </text>-->
                                            </svg>
                                        </div>
                                        <div class="col option__desc">
                                            <span class="option__title">{{ props.option.title }}</span>
                                            <span>{{ props.option.id }}</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </vue-multiselect>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
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
</script>