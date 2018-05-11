<template>
    <ul>
        <li v-for="(option, index) in options" :key="index">{{ option.title }}</li>
    </ul>  
</template>

<script>
export default {
  props: {
    options: {
      type: Array,
      required: true
    }
  }
};
</script>