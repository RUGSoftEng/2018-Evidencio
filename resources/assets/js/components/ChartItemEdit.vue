<template>
    <div class="card border-secondary">
        <a href="#" @click="toggleShow" class="card-header collapsed" :id="'chartItemEditCollapseHeader_' + indexItem" data-parent="#accRulesEdit"
           aria-expanded="false" :aria-controls="'chartItemEditCollapse_' + indexItem">
            <h6 class="mb-0">
                {{ chartItem.label }}
            </h6>
        </a>

        <div :id="'chartItemEditCollapse_' + indexItem" class="collapse" :aria-labelledby="'#chartItemEditCollapseHeader_' + indexItem">
            <div class="card-body">
                <form onsubmit="return false">
                    <div class="form-group">
                        <label :for="'chartItemTitle_' + indexItem">Title: </label>
                        <input type="text" name="" :id="'chartItemTitle_' + indexItem" class="form-control" v-model="chartItem.label" placeholder="Label" :disabled="!editing">
                        <small :id="'chartItemTitleHelp_' + indexItem" class="form-text text-muted">Label of the variable</small>
                    </div>
                    <div class="form-group">
                        <label :for="'chartItemColor_' + indexItem">Condition: </label>
                        <input type="text" name="" class="form-control" :id="'chartItemColor_' + indexItem" v-model="chartItem.color" :disabled="!editing">
                        <small :id="'chartItemColorHelp_' + indexItem" class="form-text text-muted">Color of the item</small>
                        <input type="image" class="buttonIcon" :src="getImage" @click="editing = !editing" alt="Edit">
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      chartItem: {
        type: Object,
        required: true,
        // default: {
        //   label: "Enter Label111",
        //   color: "#000000"
        // }
      },
      indexItem: {
        type: Number,
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
        this.$emit("toggle1", this.indexItem);
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