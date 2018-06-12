<template>
    <div>
        <button type="button" class="btn btn-primary ml-2" @click="addRule" :disabled="isLeaf || !isAvailable" :title="buttonTitle">Add rule</button>
        <label for="ruleEditList" class="rule-label mb-2">Created Rules</label>
        <div class="list-group" id="ruleEditList">
            <rule-edit-item v-for="(rule, index) in existingRules" :key="index" :index="index" :rule="rule" :reachable-results="reachableResults"
                :children="childrenAvailable" :warning="warnings[index]" @remove="removeRule($event)" @children-changed="setChildrenAvailable"></rule-edit-item>
        </div>
    </div>
</template>

<script>
import RuleEditItem from "./RuleEditItem.vue";

export default {
  components: {
    RuleEditItem
  },
  props: {
    rules: {
      type: Array,
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
    childrenChanged: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    isLeaf: function() {
      return this.children.length == 0;
    },
    buttonTitle: function() {
      if (this.isLeaf) return "You cannot add a rule to a step without steps on a next level";
      if (!this.isAvailable) return "All steps on the next level are already connected";
      return "Add a rule to connect this step to the next";
    },
    // Non-removed rules
    existingRules: function() {
      return this.rules.filter(rule => {
        return rule.action != "destroy";
      });
    },
    warnings: function() {
      return new Array(this.rules.length).fill(false);
    }
  },
  mounted() {
    this.setChildrenAvailable();
  },
  watch: {
    childrenChanged() {
      this.setChildrenAvailable();
    },
    /**
     * Executed when the reachableResults change:
     *  - All removed -> rules removed that use results
     *  - Some removed -> results used in rules replaced with first result
     */
    reachableResults: function() {
      let showNotification = false;
      if (this.rules.length > 0) {
        if (this.reachableResults.length == 0) {
          for (let index = this.rules.length - 1; index >= 0; index--) {
            if (this.checkRuleUsingResult(this.rules[index].condition)) {
              this.$emit("remove");
              this.$notify({
                title: "Model-calculations removed",
                text:
                  "You have removed (access to the results of) all model-calculations, leading to the removal of all rules that use model-calculations.",
                type: "warn"
              });
              break;
            }
          }
        } else {
          let baseFact = this.reachableResults[0];
          this.rules.forEach((rule, index) => {
            if (this.checkRuleReachability(rule.condition, baseFact)) {
              this.warnings[index] = true;
              showNotification = true;
            }
          });
        }
        if (showNotification)
          this.$notify({
            title: "Model-calculations removed",
            text:
              "You have removed (access to) one or more model-calculations that were used in a logical rule, these are now replaced with another." +
              "The modified rules are most likely incorrect, please check the indicated rules.",
            type: "warn"
          });
      }
    }
  },
  methods: {
    addRule() {
      this.rules.push({
        databaseId: -1,
        title: "Empty rule",
        description: "",
        condition: {
          label: "rule"
        },
        target: this.getFirstAvailableTarget(),
        edgeId: -1,
        action: "create"
      });
      this.setChildrenAvailable();
    },
    removeRule(index) {
      let actualIndex = this.findActualIndex(index);
      if (actualIndex != -1) {
        Vue.set(this.rules[actualIndex], "action", "destroy");
        this.setChildrenAvailable();
      }
    },

    /**
     * Finds the actual index of the rule you are trying to destroy, for example. Ignores destroyed rules.
     * @param {Number} index
     */
    findActualIndex(index) {
      let counter = 0;
      while (counter < this.rules.length - 1 && (index > 0 || this.rules[counter].action == "destroy")) {
        if (this.rules[counter].action != "destroy") {
          index--;
        }
        counter++;
      }
      if (index == 0) return counter;
      return -1;
    },

    // Sets the flag for if there are children-steps available for using in a rule
    calculateAvailability() {
      for (let index = this.childrenAvailable.length - 1; index >= 0; index--) {
        if (!this.childrenAvailable[index].$isDisabled) return (this.isAvailable = true);
      }
      this.isAvailable = false;
    },

    // Returns the first available child-step
    getFirstAvailableTarget() {
      for (let index = this.childrenAvailable.length - 1; index >= 0; index--) {
        if (!this.childrenAvailable[index].$isDisabled) return this.childrenAvailable[index];
      }
      return null;
    },

    // Checks the children-step uses in the rules and sets their availability accordingly
    setChildrenAvailable() {
      let newChildren = JSON.parse(JSON.stringify(this.children));
      newChildren.map(child => {
        child.$isDisabled = false;
      });
      for (let ruleIndex = this.existingRules.length - 1; ruleIndex >= 0; ruleIndex--) {
        const ruleTarget = this.existingRules[ruleIndex].target;
        if (ruleTarget != null) {
          for (let index = newChildren.length - 1; index >= 0; index--) {
            if (newChildren[index].stepId === ruleTarget.stepId) {
              newChildren[index].$isDisabled = true;
              break;
            }
          }
        }
      }
      this.childrenAvailable = newChildren;
      this.calculateAvailability();
    },

    /**
     * Returns the index of a reachable result based on the name
     * @param {String} resName
     */
    getReachableResultIndex(resName) {
      for (let index = this.reachableResults.length - 1; index >= 0; index--) {
        if (this.reachableResults[index] == resName) return index;
      }
      return -1;
    },

    /**
     * Checks if a rule uses non-reachable resuls, is used upon change in rules/api-mappings/etc.
     * @param {Object} rule
     * @param {String} baseFact
     */
    checkRuleReachability(rule, baseFact) {
      let remove = false;
      if (rule.hasOwnProperty("fact") && rule.fact != "trueValue") {
        if (this.getReachableResultIndex(rule.fact) == -1) {
          rule.fact = baseFact;
          remove = true;
        }
      } else if (rule.hasOwnProperty("any")) {
        rule.any.forEach(part => {
          if (this.checkRuleReachability(part, baseFact)) remove = true;
        });
      } else if (rule.hasOwnProperty("all")) {
        rule.all.forEach(part => {
          if (this.checkRuleReachability(part, baseFact)) remove = true;
        });
      }
      return remove;
    },

    /**
     * Checks if a rule is using results (true) or is a 'no condition' rule (false)
     * @param {Object} rule
     */
    checkRuleUsingResult(rule) {
      if (rule.hasOwnProperty("fact") && rule.fact != "trueValue") {
        return true;
      } else if (rule.hasOwnProperty("any")) {
        for (let index = rule.any.length - 1; index >= 0; index--)
          if (this.checkRuleUsingResult(rule.any[index])) return true;
      } else if (rule.hasOwnProperty("all")) {
        for (let index = rule.all.length - 1; index >= 0; index--)
          if (this.checkRuleUsingResult(rule.all[index])) return true;
      }
      return false;
    }
  },
  data() {
    return {
      isAvailable: false,
      childrenAvailable: []
    };
  }
};
</script>

<style scoped>
.rule-label {
  font-weight: bold;
  display: block;
}
</style>