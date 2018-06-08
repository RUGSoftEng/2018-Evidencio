<template>
    <div>
        <button type="button" class="btn btn-primary ml-2" @click="addRule" :disabled="isLeaf || !isAvailable" :title="buttonTitle">Add rule</button>
        <label for="ruleEditList" class="rule-label mb-2">Created Rules</label>
        <div class="list-group" id="ruleEditList">
            <rule-edit-item v-for="(rule, index) in existingRules" :key="index" :index="index" :rule="rule" :reachable-results="reachableResults"
                :children="childrenAvailable" @remove="removeRule($event)" @children-changed="setChildrenAvailable"></rule-edit-item>
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
    existingRules: function() {
      return this.rules.filter(rule => {
        return rule.action !== "destroy";
      });
    }
  },
  mounted() {
    this.setChildrenAvailable();
  },
  watch: {
    childrenChanged() {
      this.setChildrenAvailable();
    }
    // reachableResults: function() {
    //   if (this.reachableResults.length == 0) {
    //     this.$emit("remove");
    //   } else {
    //     this.rules.forEach(rule => {
    //     });
    //   }
    // }
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
    },
    removeRule(index) {
      Vue.set(this.rules[index], "action", "destroy");
      this.setChildrenAvailable();
    },
    calculateAvailability() {
      for (let index = this.childrenAvailable.length - 1; index >= 0; index--) {
        if (!this.childrenAvailable[index].$isDisabled) return (this.isAvailable = true);
      }
      this.isAvailable = false;
    },
    getFirstAvailableTarget() {
      for (let index = this.childrenAvailable.length - 1; index >= 0; index--) {
        if (!this.childrenAvailable[index].$isDisabled) return this.childrenAvailable[index];
      }
      return null;
    },
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
