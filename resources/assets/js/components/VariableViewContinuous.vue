<template>
    <div>
        <p>Minimal value: {{ options.min }}</p>
        <p>Maximal value: {{ options.max }}</p>
        <p>Stepsize: {{ options.step }}</p>
        <p>Unit: {{ options.unit }}</p>
    </div>
</template>


<script>
export default {
  props: {
    options: {
      type: Object,
      required: true
    }
  }
};
</script>