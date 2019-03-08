<template>
  <table class="table table-striped">
    <thead>
      <tr>
        <th v-for="(element, index) in head" :key="index">{{element}}</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(element, index) in body" :key="index">
        <td v-for="(value, index) in Object.values(element)" :key="index">{{value}}</td>
      </tr>
    </tbody>
  </table>
</template>


<style lang="scss" scoped src="./Table.scss">
</style>
<script lang="ts" src="./Table.ts">
</script>
