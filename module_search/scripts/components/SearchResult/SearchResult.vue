<template>
  <div>
    <div class="numberOfResults">
        {{$t("search.hitlist_text1")}}
        "{{searchQuery}}"
        {{$t("search.hitlist_text2")}}
        {{searchResults.length}}
        {{$t("search.hitlist_text3")}}
        </div>
    <div
      v-for="(item, index) in searchResults"
      :key="index"
      class="searchResultContainer col-sm-12 col-md-12 col-lg-12 col-xl-12"
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
