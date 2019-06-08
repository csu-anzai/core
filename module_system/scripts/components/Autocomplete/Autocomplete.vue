<template>
  <b-row>
    <b-col sm="3" md="3" lg="3">{{label}}</b-col>
    <b-col sm="6" md="6" lg="6">
      <b-form-input list="userFilter" v-model="userQuery" autocomplete="off"></b-form-input>
      <datalist id="userFilter">
        <option v-for="(element, index) in mappedResults" :key="index">{{ element }}</option>
      </datalist>
    </b-col>
    <b-col sm="3" md="3" lg="3">
      <span class="listButton">
        <a @click="deleteUserQuery">
          <i class="kj-icon fa fa-trash-o"></i>
        </a>
      </span>
    </b-col>
  </b-row>
</template>
<script lang="ts" src="./Autocomplete.ts">
</script>
<style lang="less" scoped src="./Autocomplete.less">
</style>
