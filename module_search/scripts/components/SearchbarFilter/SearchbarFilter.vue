<template>
  <div>
    <p @click="toggleFilter">
      {{$t("search.form_additionalheader")}}
      <i class="fa fa-caret-up" v-if="filterIsOpen"></i>
      <i class="fa fa-caret-down" v-else></i>
    </p>
    <div v-if="filterIsOpen">
      <Loader v-if="filterModules===null" :loading="true"></Loader>
      <div v-else>
        <Multiselect
          :multiple="true"
          :options="moduleNames"
          v-model="selectedModules"
          :searchable="true"
          :close-on-select="false"
          :show-labels="false"
        ></Multiselect>
      </div>
    </div>
  </div>
</template>
<script lang="ts" src="./SearchbarFilter.ts">
</script>
<style lang="less" scoped src="./SearchbarFilter.less">
</style>
