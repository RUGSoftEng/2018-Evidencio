<template>
    <div id="accRulesEdit">
        <rule-edit-item v-for="(rule, index) in rules" :key="index" :index-item="index" :rule="rule" :options="children" @toggle="selectCard($event)">
        </rule-edit-item>
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
    }
  },

  methods: {
    selectCard(index) {
      for (let ind = 0; ind < this.rules.length; ind++) {
        if (ind == index) $("#ruleEditCollapse_" + ind).collapse("toggle");
        else $("#ruleEditCollapse_" + ind).collapse("hide");
      }
    }
  }
};
</script>