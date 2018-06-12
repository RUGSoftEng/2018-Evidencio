<template>
    <div class="alert ml-3" :class="{fade: !show, show: show, 'alert-success' : type=='success', 'alert-danger': type=='danger'}" role="alert" aria-labelledby="alertSuccess" :aria-hidden="!show">
        <strong>{{ message }}</strong>
        <!--<button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>-->
    </div>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      required: false,
      default: "success"
    },
    message: {
      type: String,
      required: true,
      default: "Default"
    },
    show: {
      type: Boolean,
      required: true
    }
  },
  watch: {
    show: function() {
      if (this.show == true) {
        window.setTimeout(() => {
          this.$emit("hide");
        }, 2000);
      }
    }
  }
};
</script>

<style scoped>
.alert {
  display: inline;
}
</style>
