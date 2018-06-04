<template>
    <div>
        <button type="button" class="btn btn-primary ml-2" @click="addRule" :disabled="isLeaf" :title="buttonTitle">Add rule</button>
        <label for="ruleEditList" class="rule-label mb-2">Created Rules</label>
        <div class="list-group" id="ruleEditList">
            <rule-edit-item v-for="(rule, index) in rules" :key="index" :index="index" :rule="rule" :reachable-results="reachableResults"
                :children="children"></rule-edit-item>
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
    }
  },
  computed: {
    isLeaf: function() {
      return this.children.length == 0;
    },
    buttonTitle: function() {
      if (this.isLeaf) return "You cannot add a rule to a step without steps on a next level";
      return "Add a rule to connect this step to the next";
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
        target: null,
        edgeId: -1,
        create: true,
        destroy: false,
        change: false
      });
    }
  }
};
</script>

<style scoped>
.rule-label {
  font-weight: bold;
  display: block;
}
</style>
