<template>
  <div>
    <div
      v-for="item in searchResults"
      :key="item.systemid"
      class="searchResultContainer"
    >
      <a :href="item.link" @click="close">
        <span v-html="item.icon"></span>
        {{item.description}}
      </a>
      <small>{{item.module}}</small>
    </div>
  </div>
</template>

<script lang="ts" src="./SearchResult">
</script>
<style lang="less" scoped src="./SearchResult.less">
</style>
