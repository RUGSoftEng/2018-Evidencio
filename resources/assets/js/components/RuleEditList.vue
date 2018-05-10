<template>
    <div>
        <div class="row">
            <button type="button" class="btn btn-primary ml-2" @click="addRule">Add rule</button>
        </div>
        <div class="row" id="accRulesEdit">
            <div class="col">
                <rule-edit-item v-for="(rule, index) in rules" :key="index" :index-item="index" :rule="rule" :options="children" @toggle="selectCard($event)">
                </rule-edit-item>
            </div>
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
    }
  },

  methods: {
    selectCard(index) {
      for (let ind = 0; ind < this.rules.length; ind++) {
        if (ind == index) $("#ruleEditCollapse_" + ind).collapse("toggle");
        else $("#ruleEditCollapse_" + ind).collapse("hide");
      }
    },

    addRule() {
      this.rules.push({
        title: "Empty rule",
        condition: "",
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