<template>
    <div>
        <div class="form-group" v-if="type == 'none'">
            <label for="selectType">Choose a type</label>
            <select name="selectType" id="selectType" class="form-control" v-model="newType">
                <option v-if="logic.label=='rule'" value="ALWAYS">No condition</option>
                <option value="LOGIC">Comparison</option>
                <option value="AND">Logical AND</option>
                <option value="OR">Logical OR</option>
            </select>
            <button class="btn btn-primary form-control mt-2" @click="setType">Select type</button>
        </div>
        <div v-else-if="type == 'always'">
          <h6 class="no-condition">No condition</h6>

        </div>
        <div class="form-row" v-else-if="type == 'logic'">
            <div class="col">
                <select name="resultName" class="form-control" v-model="logic.fact" title="Model result" required>
                    <option :value="result" v-for="(result, index) in reachableResults" :key="index">{{ result }}</option>
                </select>
            </div>
            <div class="col">
                <select name="operator" class="form-control" v-model="logic.operator" title="Operator" required>
                    <option :value="op.name" v-for="(op, index) in operators" :key="index">{{ op.label }}</option>
                </select>
            </div>
            <div class="col">
                <input type="number" class="form-control" v-model="logic.value" title="Value to compare with">
            </div>
        </div>
        <div class="card" :class="{'border-primary': hover, 'border-light': !hover}" v-else>
            <div class="card-header" @mouseover="hover = true" @mouseout="hover = false">
                <i class="fo-icon icon-up-open-1">&#xe809;</i>
            </div>
            <div class="list-group list-group-flush">
                <template v-for="(expression, index) in logic[type]">
                    <div v-if="index > 0" class="list-group-item logic-operator" :id="'inBetween_' + index-1" :label="currentLabel + index + '_0'">
                        <strong class="text-uppercase">{{ type }}</strong>
                    </div>
                    <div class="list-group-item" :label="currentLabel + index + '_1'">
                        <rule-logic :logic="expression" :current-label="newLabel" :reachable-results="reachableResults"></rule-logic>
                    </div>
                </template>
            </div>
            <div class="card-footer" @mouseover="hover = true" @mouseout="hover = false">
                <i class="fo-icon icon-down-open-1">&#xe808;</i>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "rule-logic",
  props: {
    logic: {
      type: Object,
      required: true
    },
    currentLabel: {
      type: String,
      required: false,
      default: ""
    },
    reachableResults: {
      type: Array,
      required: true
    }
  },
  computed: {
    type() {
      if (this.logic.label == "rule_ALWAYS" || this.logic.hasOwnProperty("always")) return "always";
      if (this.logic.hasOwnProperty("all")) return "all";
      if (this.logic.hasOwnProperty("any")) return "any";
      if (this.logic.hasOwnProperty("fact")) return "logic";
      return "none";
    }
  },
  methods: {
    setType() {
      switch (this.newType) {
        case "ALWAYS":
          Vue.set(this.logic, "always", {});
          Vue.set(this.logic, "any", [
            {
              fact: "trueValue",
              operator: "equal",
              value: true
            }
          ]);
          break;
        case "LOGIC":
          if (this.logic.label == "rule") {
            Vue.set(this.logic, "any", [
              {
                fact: this.getFirstResult(),
                operator: this.operators[0].name,
                value: 0
              }
            ]);
          } else {
            Vue.set(this.logic, "fact", this.getFirstResult());
            Vue.set(this.logic, "operator", this.operators[0].name);
            Vue.set(this.logic, "value", 0);
          }
          break;
        case "AND":
          Vue.set(this.logic, "all", [
            {
              label: "sub"
            },
            {
              label: "sub"
            }
          ]);
          break;
        case "OR":
          Vue.set(this.logic, "any", [
            {
              label: "sub"
            },
            {
              label: "sub"
            }
          ]);
          break;
      }
      this.refreshNewLabel();
      Vue.set(this.logic, "label", this.newLabel);
    },
    getFirstResult() {
      if (this.reachableResults.length == 0) return "";
      else return this.reachableResults[0];
    },
    refreshNewLabel() {
      this.newLabel = this.currentLabel + "_" + this.type.toUpperCase();
    }
  },
  mounted() {
    this.refreshNewLabel();
    if (this.logic.label == "rule") this.newType = "ALWAYS";
    else this.newType = "LOGIC";
  },
  data() {
    return {
      hover: false,
      newType: "",
      newLabel: "",
      operators: [
        {
          label: "<",
          name: "lessThan"
        },
        {
          label: "≤",
          name: "lessThanInclusive"
        },
        {
          label: "=",
          name: "equal"
        },
        {
          label: "≠",
          name: "notEqual"
        },
        {
          label: "≥",
          name: "greaterThanInclusive"
        },
        {
          label: ">",
          name: "greaterThan"
        }
      ]
    };
  }
};
</script>

<style scoped>
.fo-icon {
  display: inline-block;
  width: 100%;
  font-weight: 100%;
}
.logic-operator {
  text-align: center;
}
</style>